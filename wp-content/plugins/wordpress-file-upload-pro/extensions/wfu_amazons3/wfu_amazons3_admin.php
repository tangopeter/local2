<?php

add_action('_wfu_register_admin_scripts', 'wfu_amazons3_register_admin_scripts', 10, 0);
add_action('_wfu_register_uploadedfiles_admin_scripts', 'wfu_amazons3_register_uploadedfiles_admin_scripts', 10, 0);
add_action('_wfu_enqueue_admin_scripts', 'wfu_amazons3_enqueue_admin_scripts', 10, 0);
add_action('_wfu_enqueue_uploadedfiles_admin_scripts', 'wfu_amazons3_enqueue_uploadedfiles_admin_scripts', 10, 0);
add_filter('_wfu_dashboard_actions', 'wfu_amazons3_dashboard_actions', 10, 2);

function wfu_amazons3_register_admin_scripts() {
	wp_register_style('wordpress_file_upload_amazons3_admin_style', WPFILEUPLOAD_DIR.'extensions/wfu_amazons3/css/wfu_amazons3_adminstyles.css');
	wp_register_script('wordpress_file_upload_amazons3_admin_script', WPFILEUPLOAD_DIR.'extensions/wfu_amazons3/js/wfu_amazons3_adminfunctions.js');
}

function wfu_amazons3_register_uploadedfiles_admin_scripts() {
	wp_register_style('wordpress_file_upload_amazons3_uploadedfiles_style', WPFILEUPLOAD_DIR.'extensions/wfu_amazons3/css/wfu_amazons3_uploadedfiles_style.css');
}

function wfu_amazons3_enqueue_admin_scripts() {
	wp_enqueue_style('wordpress_file_upload_amazons3_admin_style');
	wp_enqueue_script('wordpress_file_upload_amazons3_admin_script');
}

function wfu_amazons3_enqueue_uploadedfiles_admin_scripts() {
	wp_enqueue_style('wordpress_file_upload_amazons3_uploadedfiles_style');
}

function wfu_amazons3_dashboard_actions($echo_str, $action) {
	$nonce = (!empty($_POST['nonce']) ? $_POST['nonce'] : (!empty($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $action == 'reset_amazons3' && $nonce != "" ) {
		if ( wfu_reset_amazons3($nonce) === true )
			$echo_str = wfu_maintenance_actions('Amazon S3 uploads were successfully reset.');
		else $echo_str = wfu_manage_mainmenu();
	}
	elseif ( $action == 'clear_amazons3' && $nonce != "" ) {
		if ( wfu_clear_amazons3($nonce) === true )
			$echo_str = wfu_maintenance_actions('Amazon S3 uploads were successfully cleared.');
		else $echo_str = wfu_manage_mainmenu();
	}
	
	return $echo_str;
}