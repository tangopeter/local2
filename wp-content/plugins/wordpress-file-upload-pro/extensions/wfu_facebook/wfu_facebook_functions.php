<?php

add_filter('_wfu_after_upload', 'wfu_facebook_after_upload_handler', 10, 3);
add_filter('_wfu_get_all_plugin_options', 'wfu_facebook_get_all_plugin_options', 10, 1);

function wfu_facebook_after_upload_handler($changable_data, $additional_data, $params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $blog_id;
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( $params["messenger"] == "true" ) {
		$files = $additional_data["files"];
		$force_notifications = ( WFU_VAR("WFU_FORCE_NOTIFICATIONS") == "true" );
		//retrieve the list of uploaded files
		$uploaded_file_paths = array();
		$all_files_count = count($files);
		foreach ( $files as $file ) {
			if ( $file["upload_result"] == "success" || $file["upload_result"] == "warning" )
				array_push($uploaded_file_paths, $file["filepath"]);
		}
		if ( count($uploaded_file_paths) > 0 || $force_notifications ) {
			//get user info
			$user = wp_get_current_user();
			if ( 0 == $user->ID ) {
				$user_login = "guest";
				$user_email = "";
			}
			else {
				$user_login = $user->user_login;
				$user_email = $user->user_email;
			}
			//get list of filenames and list of file paths
			$only_filename_list = "";
			$target_path_list = "";
			foreach ( $uploaded_file_paths as $filepath ) {
				$only_filename_list .= ( $only_filename_list == "" ? "" : ", " ).wfu_basename($filepath);
				$target_path_list .= ( $target_path_list == "" ? "" : ", " ).$filepath;
			}
			//get userdata if they exist
			$userdata_fields = array();
			foreach ( $files as $file ) {
				if ( isset($file["user_data"]) ) {
					foreach ( $file["user_data"] as $userdata_key => $userdata_field )
						$userdata_fields[$userdata_key] = array( "label" => $userdata_field["label"], "value" => $userdata_field["value"] );
					break;
				}
			}
			
			//if Upload Details shortcode attribute has been defined, then
			//define a link which will show these details
			$uploaddetails_url = ( trim($params["messengeruploaddetails"]) == "" ? "" : site_url().WFU_FACEBOOK_UPLOADDETAILS_PAGE.'/?upload_id='.$additional_data["unique_id"] );
			//replace dynamic variables in Messenger Text
			$search = array ('/%username%/', '/%useremail%/', '/%filename%/', '/%filepath%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/', '/%uploaddetails%/', '/%n%/', '/%dq%/', '/%brl%/', '/%brr%/');	 
			$replace = array ($user_login, ( $user_email == "" ? "no email" : $user_email ), $only_filename_list, $target_path_list, $blog_id, $params["pageid"], sanitize_text_field(get_the_title($params["pageid"])), $uploaddetails_url, "\n", "\"", "[", "]");
			foreach ( $userdata_fields as $userdata_key => $userdata_field ) { 
				$ind = 1 + $userdata_key;
				array_push($search, '/%userdata'.$ind.'%/');  
				array_push($replace, $userdata_field["value"]);
			}   
			$messengertext = preg_replace($search, $replace, $params["messengertext"]);
			//do actions if Upload Details shortcode attribute has been defined
			if ( trim($params["messengeruploaddetails"]) != "" ) {
				//replace dynamic variables in Upload Details
				$uploaddetails = preg_replace($search, $replace, $params["messengeruploaddetails"]);
				//store upload details to database; they are stored to the first
				//record created in the database for this upload, so that they
				//can be retrieved at any time based on the unique id of the
				//upload
				$oldestrec = wfu_get_oldestrec_from_uniqueid($additional_data["unique_id"]);
				if ( $oldestrec ) {
					$filedata = wfu_get_filedata_from_rec($oldestrec, true);
					$filedata["facebook"] = array(
						"type"	=> "data",
						"uploaddetails"	=> $uploaddetails
					);
					wfu_save_filedata_from_id($oldestrec->idlog, $filedata, false);
				}
			}
			try {
				$response = wfu_facebook_send_message($messengertext);
			}
			catch (Exception $e) {}
		}
	}
	return $changable_data;
}

function wfu_facebook_get_all_plugin_options($options) {
	array_push($options,
		//stored Messenger subscription process unique ID 
		array( "wfu_Facebook_UID", "session", true, false ),
		//stored expiration time of Messenger subscription process unique ID
		array( "wfu_Facebook_UID_expires", "session", true, false )
	);
	return $options;
}

function wfu_facebook_send_message($message) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options['facebook_userpsid'] == "" || $plugin_options['facebook_pageaccesstoken'] == "" ) return null;

	include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
	
	$data = [
		'messaging_type' => 'RESPONSE',
		'recipient' => [ 'id' => $plugin_options['facebook_userpsid'] ],
		'message' => ['text' => $message ]
	];
	$options['form_params'] = $data;
	$config = array();
	//include proxy support
	wfu_add_proxy_param($config);
	$client = new GuzzleHttp\Client($config);
	$response = $client->post('https://graph.facebook.com/v3.2/me/messages?access_token='.$plugin_options['facebook_pageaccesstoken'], $options);
	
	return $response;
}

function wfu_facebook_send_attachment($message) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options['facebook_userpsid'] == "" || $plugin_options['facebook_pageaccesstoken'] == "" ) return null;

	include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;

	$data = [
		'messaging_type' => 'RESPONSE',
		'recipient' => [ 'id' => $plugin_options['facebook_userpsid'] ],
		'message' => [
			'attachment' => [
				"type" => "image",
				"payload" => [
					"url" => $message,
					"is_reusable" => false
				]
			]
		]
	];
	$options['form_params'] = $data;
	$config = array();
	//include proxy support
	wfu_add_proxy_param($config);
	$client = new GuzzleHttp\Client($config);
	$response = $client->post('https://graph.facebook.com/v3.2/me/messages?access_token='.$plugin_options['facebook_pageaccesstoken'], $options);
	
	return $response;
}

function wfu_facebook_upload_details($uniqueid) {
	$output = "";
	$oldestrec = wfu_get_oldestrec_from_uniqueid($uniqueid);
	if ( $oldestrec ) {
		$filedata = wfu_get_filedata_from_rec($oldestrec);
		if ( $filedata != null && isset($filedata["facebook"]) ) {
			if ( !is_user_logged_in() ) auth_redirect();
			if ( !current_user_can( 'manage_options' ) ) header("HTTP/1.1 403 Forbidden");
			else $output = wpautop($filedata["facebook"]["uploaddetails"]);
		}
		else header("HTTP/1.1 404 Not Found");
	}
	else header("HTTP/1.1 404 Not Found");
	die($output);
}