<?php

add_action('_wfu_schedule_file_transfer', 'wfu_onedrive_schedule_file_transfer', 10, 4);
add_filter('_wfu_filetransfer_services', 'wfu_onedrive_filetransfer_service', 10, 1);

function wfu_onedrive_schedule_file_transfer($fileid, $target_path, $userdata_fields, $params) {
	if ( $params["onedrive"] == "true" && wfu_onedrive_service_active() ) {
		$user = wp_get_current_user();
		if ( 0 == $user->ID ) {
			$user_id = 0;
			$user_login = "guest";
		}
		else {
			$user_id = $user->ID;
			$user_login = $user->user_login;
		}
		$deletelocal = ( $params["medialink"] != "true" && $params["postlink"] != "true" && $params["onedrivelocal"] == "delete" );
		// Define dynamic onedrive path from variables and userdata fields
		$search = array ('/%userid%/', '/%username%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/');	
		$replace = array ($user_id, $user_login, $params['blogid'], $params['pageid'], sanitize_text_field(get_the_title($params['pageid'])));
		foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
			$ind = 1 + $userdata_key;
			array_push($search, '/%userdata'.$ind.'%/');  
			array_push($replace, $userdata_field["value"]);
		}   
		$onedrivepath = preg_replace($search, $replace, $params["onedrivepath"]);
		//define any additional parameters
		$additional_params = array();
		if ( $params["onedriveuserdata"] == "true" ) $additional_params["include_userdata"] = true;
		$additional_params["share_file"] = ( $params["onedriveshare"] == "true" );
		$additional_params["conflict_policy"] = $params["onedriveconflict"];
		wfu_add_file_to_transfer_queue($fileid, $target_path, "onedrive", $onedrivepath, $deletelocal, "last", $additional_params);
	}
}

function wfu_onedrive_get_name() {
	$info = wfu_onedrive_get_info();
	return $info["name"];
}

function wfu_onedrive_filetransfer_service($services) {
	$info = wfu_onedrive_get_info();
	array_push($services, $info["code"]);
	return $services;
}

/**
 *  Checks whether Microsoft OneDrive is active
 *  
 *  This function returns whether Microsoft OneDrive is active by checking if access
 *  token is not empty.
 *  
 *  @return bool true if Microsoft OneDrive is active, false if it is not
 */
function wfu_onedrive_service_active() {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	return ( $plugin_options['onedrive_accesstoken'] != "" );
}

/**
 *  Executes transfer of file to Microsoft OneDrive
 *  
 *  This function first checks if Microsoft OneDrive API is supported. PHP versions
 *  of web server prior to 5.4.0 cannot run Microsoft OneDrive API. If everything is
 *  Ok then Microsoft OneDrive API upload function is called.
 *  
 *  @param string $filepath path to source file
 *  @param string $destination path to destination
 *  @param array $params additional params for upload
 *  @return array containing 3 items:
 *          - result: bool true if transfer was successful, false otherwise
 *          - error: any error messages on failed transfer
 *          - filepath: the final filepath of the destination file
 */
function wfu_onedrive_transfer_file($filepath, $destination, $params) {
	//define default return array
	$ret = array( "result" => false, "error" => "", "filepath" => "" );
	//check PHP version
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.6.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) {
		$ret["error"] = "old_php";
		return $ret;
	}
	//include any custom params to pass to the upload functions
	$fileid = $params["fileid"];
	$filedata = wfu_get_latest_filedata_from_id($fileid);
	if ( $filedata != null && isset($filedata["onedrive"]) && isset($filedata["onedrive"]["additional_params"]) ) {
		$additional_params = $filedata["onedrive"]["additional_params"];
		//if include_userdata flag is in additional params and file has
		//userdata, then add these userdata to params so that they are added to
		//the transferred file
		if ( isset($additional_params["include_userdata"]) && $additional_params["include_userdata"] == true ) {
			$userdata = wfu_get_userdata_from_id($fileid);
			if ( count($userdata) > 0 ) $params["userdata"] = $userdata;
		}
		//pass share_file flag to the params
		$params["share_file"] = $additional_params["share_file"];
		//pass conflict policy to the params
		$params["conflict_policy"] = $additional_params["conflict_policy"];
	}

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/_wfu_onedrive.php'; 
	
	wfu_onedrive_upload_file($filepath, $destination, $params);
}

function wfu_onedrive_check_transfer($fileid, $jobid) {
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/_wfu_onedrive.php'; 

	wfu_onedrive_check_upload($fileid, $jobid);
}