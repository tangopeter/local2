<?php

add_action('_wfu_schedule_file_transfer', 'wfu_amazons3_schedule_file_transfer', 10, 4);
add_filter('_wfu_filetransfer_services', 'wfu_amazons3_filetransfer_service', 10, 1);

function wfu_amazons3_schedule_file_transfer($fileid, $target_path, $userdata_fields, $params) {
	if ( $params["amazons3"] == "true" && wfu_amazons3_service_active() ) {
		$user = wp_get_current_user();
		if ( 0 == $user->ID ) {
			$user_id = 0;
			$user_login = "guest";
		}
		else {
			$user_id = $user->ID;
			$user_login = $user->user_login;
		}
		$deletelocal = ( $params["medialink"] != "true" && $params["postlink"] != "true" && $params["amazons3local"] == "delete" );
		// Define dynamic amazons3 path from variables and userdata fields
		$search = array ('/%userid%/', '/%username%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/');	
		$replace = array ($user_id, $user_login, $params['blogid'], $params['pageid'], sanitize_text_field(get_the_title($params['pageid'])));
		$usearch = array ();	
		$ureplace = array ();
		foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
			$ind = 1 + $userdata_key;
			array_push($search, '/%userdata'.$ind.'%/');  
			array_push($replace, $userdata_field["value"]);
			array_push($usearch, '/%userdata'.$ind.'%/');  
			array_push($ureplace, $userdata_field["value"]);
		}   
		$amazons3path = preg_replace($search, $replace, $params["amazons3path"]);
		// Define dynamic amazons3 bucket from userdata fields
		$amazons3bucket = preg_replace($usearch, $ureplace, $params["amazons3bucket"]);
		//define any additional parameters
		$additional_params = array(
			"bucket" => $amazons3bucket,
			"file_access" => $params["amazons3access"],
			"share_file" => ( $params["amazons3share"] == "true" )
		);
		if ( $params["amazons3userdata"] == "true" ) $additional_params["include_userdata"] = true;
		wfu_add_file_to_transfer_queue($fileid, $target_path, "amazons3", $amazons3path, $deletelocal, "last", $additional_params);
	}
}

function wfu_amazons3_get_name() {
	$info = wfu_amazons3_get_info();
	return $info["name"];
}

function wfu_amazons3_filetransfer_service($services) {
	$info = wfu_amazons3_get_info();
	array_push($services, $info["code"]);
	return $services;
}

/**
 *  Checks whether Amazon S3 is active
 *  
 *  This function returns whether Amazon S3 is active by checking if access
 *  token is not empty.
 *  
 *  @return bool true if Amazon S3 is active, false if it is not
 */
function wfu_amazons3_service_active() {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	return ( $plugin_options['amazons3_publickey'] != "" );
}

/**
 *  Executes transfer of file to Amazon S3
 *  
 *  This function first checks if Amazon S3 API is supported. PHP versions of
 *  web server prior to 5.5.0 cannot run Amazon S3 API. If everything is Ok then
 *  Amazon S3 API upload function is called.
 *  
 *  @param string $filepath path to source file
 *  @param string $destination path to destination
 *  @param array $params additional params for upload
 *  @return array containing 3 items:
 *          - result: bool true if transfer was successful, false otherwise
 *          - error: any error messages on failed transfer
 *          - filepath: the final filepath of the destination file
 */
function wfu_amazons3_transfer_file($filepath, $destination, $params) {
	//define default return array
	$ret = array( "result" => false, "error" => "", "filepath" => "" );
	//check PHP version
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.5.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) {
		$ret["error"] = "old_php";
		return $ret;
	}
	//include any custom params to pass to the upload functions
	$fileid = $params["fileid"];
	$filedata = wfu_get_latest_filedata_from_id($fileid);
	$bucket = "";
	if ( $filedata != null && isset($filedata["amazons3"]) && isset($filedata["amazons3"]["additional_params"]) ) {
		$additional_params = $filedata["amazons3"]["additional_params"];
		$bucket = $additional_params["bucket"];
		//if include_userdata flag is in additional params and file has
		//userdata, then add these userdata to params so that they are added to
		//the transferred file
		if ( isset($additional_params["include_userdata"]) && $additional_params["include_userdata"] == true ) {
			$userdata = wfu_get_userdata_from_id($fileid);
			if ( count($userdata) > 0 ) $params["userdata"] = $userdata;
		}
		//pass file_access and share_file flag to the params
		$params["file_access"] = $additional_params["file_access"];
		$params["share_file"] = $additional_params["share_file"];
	}
	else {
		$ret["error"] = "no_bucket";
		return $ret;
	}

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/_wfu_amazons3.php'; 
	
	wfu_amazons3_upload_file($filepath, $bucket, $destination, $params);
}

function wfu_amazons3_check_transfer($fileid, $jobid) {
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/_wfu_amazons3.php'; 

	wfu_amazons3_check_upload($fileid, $jobid);
}