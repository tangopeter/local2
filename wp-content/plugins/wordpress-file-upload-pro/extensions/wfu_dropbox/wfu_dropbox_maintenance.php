<?php

add_filter('_wfu_maintenance_actions_bottom', 'wfu_dropbox_maintenance_actions', 10, 1);

function wfu_dropbox_maintenance_actions($echo_str) {
	$siteurl = site_url();

	$echo_str .= "\n\t\t".'<h3 style="margin-bottom: 10px;">Dropbox Actions</h3>';
	$echo_str .= "\n\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t".'<tbody>';
	$echo_str .= "\n\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=reset_dropbox&amp;nonce='.wp_create_nonce("wfu_reset_dropbox").'" class="button" title="Reset Dropbox Uploads">Reset Dropbox Uploads</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Use this action to restart pending Dropbox uploads, in case they seem to delay.</label>';
	$echo_str .= "\n\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t".'<a onclick="if (confirm(\'You are about to clear all pending Dropbox uploads. Are you sure you want to continue?\')) window.location = \''.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=clear_dropbox&amp;nonce='.wp_create_nonce("wfu_clear_dropbox").'\'; return false;" class="button" title="Clear Dropbox Uploads" style="color:red;">Clear Dropbox Uploads</a>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Use this action to cancel and clear all pending Dropbox uploads.</label>';
	$echo_str .= "\n\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t".'</table>';

	return $echo_str;
}

/**
 *  resets the dropbox datatables
 *  
 *  This function clears wfu_dbxqueue table from any Dropbox transfers and
 *  runs transfermanager again to update its status
 */
function wfu_dropbox_reset_uploads($clearfiles = false) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $wpdb;
	if ( $clearfiles ) {
		$table_name1 = $wpdb->prefix . "wfu_log";
		$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
		$wpdb->query('DELETE FROM '.$table_name3.' WHERE jobid LIKE \'d_%\'');
	}
	wfu_schedule_transfermanager(true);
}

/**
 *  executes dropbox reset
 *  
 *  This function executes wfu_dropbox_reset_uploads() function to reset
 *  dropbox uploadmanager after it verifies that operation is allowed
 */
function wfu_reset_dropbox($nonce) {
	if ( !current_user_can( 'manage_options' ) ) return false;
	if ( !wp_verify_nonce($nonce, 'wfu_reset_dropbox') ) return false;
	
	wfu_dropbox_reset_uploads();
	
	return true;
}

/**
 *  executes dropbox reset and clear
 *  
 *  This function executes wfu_dropbox_reset_uploads() function with $clearfiles
 *  flag enabled to reset and clear dropbox databases after it verifies that
 *  operation is allowed
 */
function wfu_clear_dropbox($nonce) {
	if ( !current_user_can( 'manage_options' ) ) return false;
	if ( !wp_verify_nonce($nonce, 'wfu_clear_dropbox') ) return false;
	
	wfu_dropbox_reset_uploads(true);
	
	return true;
}