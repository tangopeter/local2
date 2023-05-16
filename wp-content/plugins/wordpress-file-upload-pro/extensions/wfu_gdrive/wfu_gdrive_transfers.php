<?php

add_action('_wfu_schedule_file_transfer', 'wfu_gdrive_schedule_file_transfer', 10, 4);
add_filter('_wfu_filetransfer_services', 'wfu_gdrive_filetransfer_service', 10, 1);

function wfu_gdrive_schedule_file_transfer($fileid, $target_path, $userdata_fields, $params) {
	if ( $params["gdrive"] == "true" && wfu_gdrive_service_active() ) {
		$user = wp_get_current_user();
		if ( 0 == $user->ID ) {
			$user_id = 0;
			$user_login = "guest";
		}
		else {
			$user_id = $user->ID;
			$user_login = $user->user_login;
		}
		$deletelocal = ( $params["medialink"] != "true" && $params["postlink"] != "true" && $params["gdrivelocal"] == "delete" );
		// Define dynamic gdrive path from variables and userdata fields
		$search = array ('/%userid%/', '/%username%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/');	
		$replace = array ($user_id, $user_login, $params['blogid'], $params['pageid'], sanitize_text_field(get_the_title($params['pageid'])));
		foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
			$ind = 1 + $userdata_key;
			array_push($search, '/%userdata'.$ind.'%/');  
			array_push($replace, $userdata_field["value"]);
		}   
		$gdrivepath = preg_replace($search, $replace, $params["gdrivepath"]);
		//define any additional parameters
		$additional_params = array();
		if ( $params["gdriveuserdata"] == "true" ) $additional_params["include_userdata"] = true;
		if ( $params["gdriveduplicates"] == "true" ) $additional_params["trash_duplicates"] = true;
		$additional_params["share_file"] = ( $params["gdriveshare"] == "true" );
		wfu_add_file_to_transfer_queue($fileid, $target_path, "gdrive", $gdrivepath, $deletelocal, "last", $additional_params);
	}
}

function wfu_gdrive_get_name() {
	$info = wfu_gdrive_get_info();
	return $info["name"];
}

function wfu_gdrive_filetransfer_service($services) {
	$info = wfu_gdrive_get_info();
	array_push($services, $info["code"]);
	return $services;
}

/**
 *  Checks whether Google Drive is active
 *  
 *  This function returns whether Google Drive is active by checking if access
 *  token is not empty.
 *  
 *  @return bool true if Google Drive is active, false if it is not
 */
function wfu_gdrive_service_active() {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	return ( $plugin_options['gdrive_accesstoken'] != "" );
}

/**
 *  Executes transfer of file to Google Drive
 *  
 *  This function first checks if Google Drive API is supported. PHP versions
 *  of web server prior to 5.4.0 cannot run Google Drive API. If everything is
 *  Ok then Google Drive API upload function is called.
 *  
 *  @param string $filepath path to source file
 *  @param string $destination path to destination
 *  @param array $params additional params for upload
 *  @return array containing 3 items:
 *          - result: bool true if transfer was successful, false otherwise
 *          - error: any error messages on failed transfer
 *          - filepath: the final filepath of the destination file
 */
function wfu_gdrive_transfer_file($filepath, $destination, $params) {
	//define default return array
	$ret = array( "result" => false, "error" => "", "filepath" => "" );
	//check PHP version
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.4.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) {
		$ret["error"] = "old_php";
		return $ret;
	}
	//include any custom params to pass to the upload functions
	$fileid = $params["fileid"];
	$filedata = wfu_get_latest_filedata_from_id($fileid);
	if ( $filedata != null && isset($filedata["gdrive"]) && isset($filedata["gdrive"]["additional_params"]) ) {
		$additional_params = $filedata["gdrive"]["additional_params"];
		//if include_userdata flag is in additional params and file has
		//userdata, then add these userdata to params so that they are added to
		//the transferred file
		if ( isset($additional_params["include_userdata"]) && $additional_params["include_userdata"] == true ) {
			$userdata = wfu_get_userdata_from_id($fileid);
			if ( count($userdata) > 0 ) $params["userdata"] = $userdata;
		}
		//pass trash_duplicates flag to the params
		if ( isset($additional_params["trash_duplicates"]) && $additional_params["trash_duplicates"] == true )
			$params["trash_duplicates"] = true;
		//pass share_file flag to the params
		$params["share_file"] = $additional_params["share_file"];
	}

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/_wfu_gdrive.php'; 
	
	wfu_gdrive_upload_file($filepath, $destination, $params);
}

function wfu_gdrive_check_transfer($fileid, $jobid) {
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/_wfu_gdrive.php'; 

	wfu_gdrive_check_upload($fileid, $jobid);
}