<?php

add_action('_wfu_register_admin_scripts', 'wfu_gdrive_register_admin_scripts', 10, 0);
add_action('_wfu_register_uploadedfiles_admin_scripts', 'wfu_gdrive_register_uploadedfiles_admin_scripts', 10, 0);
add_action('_wfu_enqueue_admin_scripts', 'wfu_gdrive_enqueue_admin_scripts', 10, 0);
add_action('_wfu_enqueue_uploadedfiles_admin_scripts', 'wfu_gdrive_enqueue_uploadedfiles_admin_scripts', 10, 0);
add_filter('_wfu_dashboard_actions', 'wfu_gdrive_dashboard_actions', 10, 2);

function wfu_gdrive_register_admin_scripts() {
	wp_register_script('wordpress_file_upload_gdrive_admin_script', WPFILEUPLOAD_DIR.'extensions/wfu_gdrive/js/wfu_gdrive_adminfunctions.js');
}

function wfu_gdrive_register_uploadedfiles_admin_scripts() {
	wp_register_style('wordpress_file_upload_gdrive_admin_style', WPFILEUPLOAD_DIR.'extensions/wfu_gdrive/css/wfu_gdrive_uploadedfiles_style.css');
}

function wfu_gdrive_enqueue_admin_scripts() {
	wp_enqueue_script('wordpress_file_upload_gdrive_admin_script');
}

function wfu_gdrive_enqueue_uploadedfiles_admin_scripts() {
	wp_enqueue_style('wordpress_file_upload_gdrive_admin_style');
}

function wfu_gdrive_dashboard_actions($echo_str, $action) {
	$nonce = (!empty($_POST['nonce']) ? $_POST['nonce'] : (!empty($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $action == 'reset_gdrive' && $nonce != "" ) {
		if ( wfu_reset_gdrive($nonce) === true )
			$echo_str = wfu_maintenance_actions('google drive uploads were successfully reset.');
		else $echo_str = wfu_manage_mainmenu();
	}
	elseif ( $action == 'clear_gdrive' && $nonce != "" ) {
		if ( wfu_clear_gdrive($nonce) === true )
			$echo_str = wfu_maintenance_actions('google drive uploads were successfully cleared.');
		else $echo_str = wfu_manage_mainmenu();
	}
	
	return $echo_str;
}