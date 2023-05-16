<?php

add_action('wp_ajax_wfu_ajax_action_facebook_authorize_app_start', 'wfu_ajax_action_facebook_authorize_app_start');
add_action('wp_ajax_wfu_ajax_action_facebook_authorize_app_finish', 'wfu_ajax_action_facebook_authorize_app_finish');
add_action('wp_ajax_wfu_ajax_action_facebook_authorize_app_reset', 'wfu_ajax_action_facebook_authorize_app_reset');

function wfu_ajax_action_facebook_authorize_app_start() {
	if ( !isset($_POST['token']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-facebook-authorize-app', 'token' );
	
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.6.3";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) die();

	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/_wfu_facebook.php'; 
	wfu_facebook_authorize_app_start();
}

function wfu_ajax_action_facebook_authorize_app_finish() {
	if ( !isset($_POST['token']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-facebook-authorize-app', 'token' );
	
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/_wfu_facebook.php'; 
	wfu_facebook_authorize_app_finish();
}

function wfu_ajax_action_facebook_authorize_app_reset() {
	if ( !isset($_POST['token']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-facebook-authorize-app', 'token' );
	
	wfu_update_setting('facebook_userpsid', "");
	wfu_update_setting('facebook_pageaccesstoken', "");
	die("wfu_facebook_authorize_app_reset:success:");
}