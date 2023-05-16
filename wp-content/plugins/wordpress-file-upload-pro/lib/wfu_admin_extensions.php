<?php

/**
 * Plugin Extensions
 *
 * This file contains functions for managing the plugin's extensions.
 *
 * @link /lib/wfu_admin_extensions.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 4.17.0
 */
 

/**
 * Display the Extensions Page.
 *
 * This function displays the list of extensions in Dashboard area of plugin.
 *
 * @since 4.17.0
 *
 * @param string $error_message Optional. An error message to show on top of
 *        Extensions page.
 *
 * @return string The HTML output of the Extensions page.
 */
function wfu_manage_extensions($error_message = "") {
	global $WFU_PLUGIN_EXTENSIONS;
	
	if ( !current_user_can( 'manage_options' ) ) return;
	$siteurl = site_url();
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	
	$plugin_extensions = apply_filters("_wfu_plugin_extensions", array());
	$plugin_extension_statuses = ( isset($WFU_PLUGIN_EXTENSIONS) ? $WFU_PLUGIN_EXTENSIONS : array() );

	$echo_str = "";
	$echo_str .= "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	if ( $error_message != "" ) {
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.$error_message.'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Extensions");
	$echo_str .= "\n\t\t".'<form enctype="multipart/form-data" name="editextensions" id="editextensions" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=edit_extensions" class="validate">';
	$nonce = wp_nonce_field('wfu_edit_extensions', '_wpnonce', false, false);
	$nonce_ref = wp_referer_field(false);
	$echo_str .= "\n\t\t\t".$nonce;
	$echo_str .= "\n\t\t\t".$nonce_ref;
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="action" value="edit_extensions" />';
	$echo_str .= "\n\t\t\t".'<table class="wp-list-table widefat fixed striped">';
	$echo_str .= "\n\t\t\t\t".'<thead>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="30%" class="manage-column column-primary">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Extension</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="50%" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Description</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="20%" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Status</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'</thead>';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	if ( count($plugin_extensions) == 0 ) {
		$echo_str .= "\n\t\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t\t".'<td colspan="3">';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>No extensions exist.</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'</tr>';
	}
	else {
		$ii = 1;
		foreach( $plugin_extensions as $key => $extension ) {
			$code = $extension["code"];
			$active = ( !array_key_exists($code, $plugin_extension_statuses) || $plugin_extension_statuses[$code] !== "0" );
			$echo_str .= "\n\t\t\t\t\t".'<tr class="extension-row tr-'.$code.' '.( $active ? "active" : "inactive" ).'">';
			$echo_str .= "\n\t\t\t\t\t\t".'<td class="column-primary" data-colname="Extension">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>'.$extension["name"].'</span>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td data-colname="Description">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>'.$extension["description"].'</span>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td data-colname="Status">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span class="wfu-extensions-status"></span>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<a class="wfu-extensions-btn button" href="javascript: wfu_update_extension_active(\''.$code.'\');"></a>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'</tr>';
			$ii++;
		}
	}
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	$echo_str .= "\n\t\t".'</form>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n".'</div>';
	
	return $echo_str;
}