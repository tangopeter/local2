<?php

add_action('wp_ajax_wfu_ajax_action_amazons3_authorize_app_finish', 'wfu_ajax_action_amazons3_authorize_app_finish');
add_action('wp_ajax_wfu_ajax_action_amazons3_authorize_app_reset', 'wfu_ajax_action_amazons3_authorize_app_reset');
add_action('wp_ajax_wfu_ajax_action_amazons3_add_file', 'wfu_ajax_action_amazons3_add_file');

function wfu_ajax_action_amazons3_authorize_app_finish() {
	if ( !isset($_POST['publickey']) || !isset($_POST['privatekey']) || !isset($_POST['nonce']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-amazons3-authorize-app', 'nonce' );
	
	$publickey = sanitize_text_field($_POST['publickey']);
	$privatekey = sanitize_text_field($_POST['privatekey']);
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/_wfu_amazons3.php';
	$result = wfu_amazons3_authorize_app_finish($publickey, $privatekey);
	die("wfu_amazons3_authorize_app_finish:".$result);
}

function wfu_ajax_action_amazons3_authorize_app_reset() {
	if ( !isset($_POST['nonce']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-amazons3-authorize-app', 'nonce' );
	
	wfu_update_setting('amazons3_publickey', "");
	wfu_update_setting('amazons3_privatekey', "");
	die("wfu_amazons3_authorize_app_reset:success");
}

function wfu_ajax_action_amazons3_add_file() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $file_code == '' || $nonce == '' ) die();

	if ( !current_user_can( 'manage_options' ) ) die();
	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_amazons3_send_file') ) die();
	
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$dec_file = wfu_get_filepath_from_safe($file_code);
	if ( $dec_file === false ) die();

	$dec_file = wfu_path_rel2abs(wfu_flatten_path($dec_file));
	$filerec = wfu_get_file_rec($dec_file, false);
	if ( $filerec == null ) die(apply_filters('_wfu_ajax_action_amazons3_add_file', 'wfu_amazons3_add_file:fail:file not in db'));
	
	wfu_add_file_to_transfer_queue($filerec->idlog, $dec_file, "amazons3", $plugin_options["amazons3_defaultpath"], false, "last");
	
	die(apply_filters('_wfu_ajax_action_amazons3_add_file', 'wfu_amazons3_add_file:success:'));
}