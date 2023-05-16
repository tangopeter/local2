<?php

add_action('_wfu_schedule_file_transfer', 'wfu_dropbox_schedule_file_transfer', 10, 4);
add_filter('_wfu_filetransfer_services', 'wfu_dropbox_filetransfer_service', 10, 1);

function wfu_dropbox_schedule_file_transfer($fileid, $target_path, $userdata_fields, $params) {
	if ( $params["dropbox"] == "true" && wfu_dropbox_service_active() ) {
		$user = wp_get_current_user();
		if ( 0 == $user->ID ) {
			$user_id = 0;
			$user_login = "guest";
		}
		else {
			$user_id = $user->ID;
			$user_login = $user->user_login;
		}
		$deletelocal = ( $params["medialink"] != "true" && $params["postlink"] != "true" && $params["dropboxlocal"] == "delete" );
		/* Define dynamic dropbox path from variables and userdata fields */
		$search = array ('/%userid%/', '/%username%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/');	
		$replace = array ($user_id, $user_login, $params['blogid'], $params['pageid'], sanitize_text_field(get_the_title($params['pageid'])));
		foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
			$ind = 1 + $userdata_key;
			array_push($search, '/%userdata'.$ind.'%/');  
			array_push($replace, $userdata_field["value"]);
		}   
		$dropboxpath = preg_replace($search, $replace, $params["dropboxpath"]);
		$additional_params = array();
		$additional_params["share_file"] = ( $params["dropboxshare"] == "true" );
		wfu_add_file_to_transfer_queue($fileid, $target_path, "dropbox", $dropboxpath, $deletelocal, "last", $additional_params);
	}
}

function wfu_dropbox_get_name() {
	$info = wfu_dropbox_get_info();
	return $info["name"];
}

function wfu_dropbox_filetransfer_service($services) {
	$info = wfu_dropbox_get_info();
	array_push($services, $info["code"]);
	return $services;
}

/**
 *  Checks whether Dropbox is active
 *  
 *  This function returns whether Dropbox is active by checking if access token
 *  is not empty.
 *  
 *  @return bool true if Dropbox is active, false if it is not
 */
function wfu_dropbox_service_active() {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	return ( $plugin_options['dropbox_accesstoken'] != "" );
}

/**
 *  Executes transfer of file to Dropbox
 *  
 *  This function first checks if Dropbox API is supported. PHP versions of web
 *  server prior to 5.3.0 do not support namespaces and cannot run Dropbox API.
 *  Furthermore, Dropbox API v2 requires PHP 5.5.0 or newer. If everything is Ok
 *  the Dropbox API upload function is called.
 *  
 *  @param string $filepath path to source file
 *  @param string $destination path to destination
 *  @param array $params additional params for upload
 *  @return array containing 3 items:
 *          - result: bool true if transfer was successful, false otherwise
 *          - error: any error messages on failed transfer
 *          - filepath: the final filepath of the destination file
 */
function wfu_dropbox_transfer_file($filepath, $destination, $params) {
	//define default return array
	$ret = array( "result" => false, "error" => "", "filepath" => "" );
	//check PHP version
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = ( /*WFU_VAR("WFU_DROPBOX_USE_V1_API") == "true" ? "5.3.0" :*/ "5.5.0" );
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) {
		$ret["error"] = "old_php";
		return $ret;
	}
	//include any custom params to pass to the upload functions
	$fileid = $params["fileid"];
	$filedata = wfu_get_latest_filedata_from_id($fileid);
	if ( $filedata != null && isset($filedata["dropbox"]) && isset($filedata["dropbox"]["additional_params"]) ) {
		$additional_params = $filedata["dropbox"]["additional_params"];
		//pass share_file flag to the params
		$params["share_file"] = $additional_params["share_file"];
	}

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/_wfu_dropbox.php'; 
	
	$use_old_API = ( WFU_VAR("WFU_DROPBOX_USE_V1_API") == "true" );
	wfu_dropbox_upload_file($filepath, $destination, $use_old_API, $params);
}

function wfu_dropbox_check_transfer($fileid, $jobid) {
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/_wfu_dropbox.php'; 

	$use_old_API = ( WFU_VAR("WFU_DROPBOX_USE_V1_API") == "true" );
	wfu_dropbox_check_upload($fileid, $jobid, $use_old_API);
}