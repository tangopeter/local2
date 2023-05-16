<?php

add_action('wp_ajax_wfu_ajax_action_gdrive_authorize_app_start', 'wfu_ajax_action_gdrive_authorize_app_start');
add_action('wp_ajax_wfu_ajax_action_gdrive_authorize_app_finish', 'wfu_ajax_action_gdrive_authorize_app_finish');
add_action('wp_ajax_wfu_ajax_action_gdrive_authorize_app_reset', 'wfu_ajax_action_gdrive_authorize_app_reset');
add_action('wp_ajax_wfu_ajax_action_gdrive_add_file', 'wfu_ajax_action_gdrive_add_file');
add_action('wp_ajax_wfu_google_oauth2callback', 'wfu_ajax_action_google_oauth2callback');

function wfu_ajax_action_gdrive_authorize_app_start() {
	if ( !isset($_POST['token']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-gdrive-authorize-app', 'token' );
	
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.4.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) die();

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/_wfu_gdrive.php'; 
	wfu_gdrive_authorize_app_start();
}

function wfu_ajax_action_gdrive_authorize_app_finish() {
	if ( !isset($_POST['token']) || !isset($_POST['authcode']) || !isset($_POST['state']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-gdrive-authorize-app', 'token' );
	
	$authCode = sanitize_text_field($_POST['authcode']);
	$state = wfu_sanitize_code($_POST["state"]);
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/_wfu_gdrive.php'; 
	wfu_gdrive_authorize_app_finish($state, $authCode);
}

function wfu_ajax_action_gdrive_authorize_app_reset() {
	if ( !isset($_POST['token']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-gdrive-authorize-app', 'token' );
	
	wfu_update_setting('gdrive_accesstoken', "");
	die("wfu_gdrive_authorize_app_reset:success:");
}

function wfu_ajax_action_gdrive_add_file() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $file_code == '' || $nonce == '' ) die();

	if ( !current_user_can( 'manage_options' ) ) die();
	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_gdrive_send_file') ) die();
	
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$dec_file = wfu_get_filepath_from_safe($file_code);
	if ( $dec_file === false ) die();

	$dec_file = wfu_path_rel2abs(wfu_flatten_path($dec_file));
	$filerec = wfu_get_file_rec($dec_file, false);
	if ( $filerec == null ) die(apply_filters('_wfu_ajax_action_gdrive_add_file', 'wfu_gdrive_add_file:fail:file not in db'));
	
	$additional_params = array();
	if ( $plugin_options["gdrive_includeuserdata"] == 1 ) $additional_params["include_userdata"] = true;
	if ( $plugin_options["gdrive_trashduplicates"] == 1 ) $additional_params["trash_duplicates"] = true;
	wfu_add_file_to_transfer_queue($filerec->idlog, $dec_file, "gdrive", $plugin_options["gdrive_defaultpath"], false, "last", $additional_params);
	
	die(apply_filters('_wfu_ajax_action_gdrive_add_file', 'wfu_gdrive_add_file:success:'));
}

function wfu_ajax_action_google_oauth2callback() {
	if ( !isset($_GET['state']) || !isset($_GET['code']) ) die();
	
	$state = wfu_sanitize_code($_GET["state"]);
	$param = wfu_encode_array_to_string(array( "code" => $_GET['code'], "state" => $state ));
	
	$html = '
		<html>
			<head>
				<script type="text/javascript">
					window.opener.postMessage("'.$param.'", "*");
					window.close();
				</script>
			</head>
			<body>
				<div>Finalizing authorization, please wait...</div>
			</body>
		</html>
	';
	die($html);
}