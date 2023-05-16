<?php

add_action('_wfu_register_admin_scripts', 'wfu_onedrive_register_admin_scripts', 10, 0);
add_action('_wfu_register_uploadedfiles_admin_scripts', 'wfu_onedrive_register_uploadedfiles_admin_scripts', 10, 0);
add_action('_wfu_enqueue_admin_scripts', 'wfu_onedrive_enqueue_admin_scripts', 10, 0);
add_action('_wfu_enqueue_uploadedfiles_admin_scripts', 'wfu_onedrive_enqueue_uploadedfiles_admin_scripts', 10, 0);
add_filter('_wfu_dashboard_actions', 'wfu_onedrive_dashboard_actions', 10, 2);

function wfu_onedrive_register_admin_scripts() {
	wp_register_script('wordpress_file_upload_onedrive_admin_script', WPFILEUPLOAD_DIR.'extensions/wfu_onedrive/js/wfu_onedrive_adminfunctions.js');
}

function wfu_onedrive_register_uploadedfiles_admin_scripts() {
	wp_register_style('wordpress_file_upload_onedrive_admin_style', WPFILEUPLOAD_DIR.'extensions/wfu_onedrive/css/wfu_onedrive_uploadedfiles_style.css');
}

function wfu_onedrive_enqueue_admin_scripts() {
	wp_enqueue_script('wordpress_file_upload_onedrive_admin_script');
}

function wfu_onedrive_enqueue_uploadedfiles_admin_scripts() {
	wp_enqueue_style('wordpress_file_upload_onedrive_admin_style');
}

function wfu_onedrive_dashboard_actions($echo_str, $action) {
	$nonce = (!empty($_POST['nonce']) ? $_POST['nonce'] : (!empty($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $action == 'reset_onedrive' && $nonce != "" ) {
		if ( wfu_reset_onedrive($nonce) === true )
			$echo_str = wfu_maintenance_actions('onedrive uploads were successfully reset.');
		else $echo_str = wfu_manage_mainmenu();
	}
	elseif ( $action == 'clear_onedrive' && $nonce != "" ) {
		if ( wfu_clear_onedrive($nonce) === true )
			$echo_str = wfu_maintenance_actions('onedrive uploads were successfully cleared.');
		else $echo_str = wfu_manage_mainmenu();
	}
	
	return $echo_str;
}