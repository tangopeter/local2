<?php

add_action('_wfu_register_admin_scripts', 'wfu_facebook_register_admin_scripts', 10, 0);
add_action('_wfu_register_uploadedfiles_admin_scripts', 'wfu_facebook_register_uploadedfiles_admin_scripts', 10, 0);
add_action('_wfu_enqueue_admin_scripts', 'wfu_facebook_enqueue_admin_scripts', 10, 0);
add_action('_wfu_enqueue_uploadedfiles_admin_scripts', 'wfu_facebook_enqueue_uploadedfiles_admin_scripts', 10, 0);
add_filter('_wfu_dashboard_actions', 'wfu_facebook_dashboard_actions', 10, 2);
add_action('init', 'wfu_facebook_monitor_requests', 10, 0);

function wfu_facebook_monitor_requests() {
	$url = add_query_arg(array());
	$test = WFU_FACEBOOK_UPLOADDETAILS_PAGE.'/?upload_id=';
	if ( substr($url, 0, strlen($test)) == $test ) {
		$uniqueid_raw = $_REQUEST["upload_id"];
		$uniqueid = wfu_sanitize_code($uniqueid_raw);
		if ( strlen($uniqueid) >= 8 && $uniqueid == $uniqueid_raw ) {
			//wfu_debug_log("pathfound: ".print_r($url, true)." \n");
			wfu_facebook_upload_details($uniqueid);
			exit();
		}
	}
	//wfu_debug_log("path: ".print_r($url, true)." \n");
}

function wfu_facebook_register_admin_scripts() {
	wp_register_script('wordpress_file_upload_facebook_admin_script', WPFILEUPLOAD_DIR.'extensions/wfu_facebook/js/wfu_facebook_adminfunctions.js');
}

function wfu_facebook_register_uploadedfiles_admin_scripts() {
	wp_register_style('wordpress_file_upload_facebook_admin_style', WPFILEUPLOAD_DIR.'extensions/wfu_facebook/css/wfu_facebook_adminstyle.css');
}

function wfu_facebook_enqueue_admin_scripts() {
	wp_enqueue_script('wordpress_file_upload_facebook_admin_script');
}

function wfu_facebook_enqueue_uploadedfiles_admin_scripts() {
	wp_enqueue_style('wordpress_file_upload_facebook_admin_style');
}

function wfu_facebook_dashboard_actions($echo_str, $action) {
	$nonce = (!empty($_POST['nonce']) ? $_POST['nonce'] : (!empty($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $action == 'reset_facebook' && $nonce != "" ) {
		if ( wfu_reset_facebook($nonce) === true )
			$echo_str = wfu_maintenance_actions('Facebook uploads were successfully reset.');
		else $echo_str = wfu_manage_mainmenu();
	}
	elseif ( $action == 'clear_facebook' && $nonce != "" ) {
		if ( wfu_clear_facebook($nonce) === true )
			$echo_str = wfu_maintenance_actions('Facebook uploads were successfully cleared.');
		else $echo_str = wfu_manage_mainmenu();
	}
	
	return $echo_str;
}