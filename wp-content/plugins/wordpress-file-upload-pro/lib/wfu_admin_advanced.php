<?php

/**
 * Advanced Page in Dashboard Area of Plugin
 *
 * This file contains functions related to Advanced page of plugin's Dashboard
 * area (render function).
 *
 * @link /lib/wfu_admin_advanced.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 3.7.1
 */



/**
 * Display the Advanced Page.
 *
 * This function displays the Advanced page of the plugin's Dashboard area.
 *
 * @since 3.7.1
 *
 * @param string $message Optional. A message to display on top when showing
 *        Advanced page of the plugin's Dashboard area.
 *
 * @return string The HTML output of the plugin's Advanced Dashboard page.
 */
function wfu_advanced_actions($message = '') {
	if ( !current_user_can( 'manage_options' ) ) return wfu_manage_mainmenu();

	$siteurl = site_url();
	
	$echo_str = '<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	if ( $message != '' ) {
		$echo_str .= "\n\t".'<div class="updated">';
		$echo_str .= "\n\t\t".'<p>'.$message.'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Advanced");
	//environment variables
	$echo_str .= "\n\t\t".'<h3 style="margin-bottom: 10px;">Environment Variables<span style="font-weight:normal; font-size:small; margin-left:6px; color:red;">(Please edit carefully)</span></h3>';
	$nonce = wp_nonce_field('wfu_environment_variables', '_wpnonce_envar', false, false);
	$nonce_ref = wp_referer_field(false);
	$echo_str .= "\n\t\t".$nonce;
	$echo_str .= "\n\t\t".$nonce_ref;
	$echo_str .= "\n\t\t".'<table class="wp-list-table widefat fixed striped">';
	$echo_str .= "\n\t\t\t".'<thead>';
	$echo_str .= "\n\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column column-primary">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Variable</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Value</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label>Description</label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="40px" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t".'<label></label>';
	$echo_str .= "\n\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t".'</thead>';
	$echo_str .= "\n\t\t\t".'<tbody>';
	$ii = 1;
	foreach ( $GLOBALS["WFU_GLOBALS"] as $ind => $envar ) {
		if ( $envar[5] == true ) {
			$echo_str .= "\n\t\t\t\t".'<tr id="wfu_envar_tr_'.$ii.'" class="'.( $envar[2] == $envar[3] ? "wfu_envar_tr_normal" : "wfu_envar_tr_different" ).'" >';
			$echo_str .= "\n\t\t\t\t\t".'<td class="column-primary" data-colname="Variable">'.$envar[0];
			$echo_str .= "\n\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
			$echo_str .= "\n\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Value">';
			$echo_str .= "\n\t\t\t\t\t\t".'<div class="wfu_envar_value_container">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="text" id="wfu_envar_value_'.$ii.'" class="wfu_envar_value" name="wfu_envar_value" value="'.$envar[3].'" style="width:100%;" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<img  id="wfu_envar_restore_'.$ii.'" src="'.WFU_IMAGE_GENERIC_RESTORE.'" class="wfu_envar_default" width="16px" height="16px" title="double-click to restore default value" ondblclick="wfu_envar_restore_default('.$ii.');" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="hidden" id="wfu_envar_slug_'.$ii.'" value="'.$ind.'" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="hidden" id="wfu_envar_default_'.$ii.'" value="'.$envar[2].'" />';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="hidden" id="wfu_envar_initial_'.$ii.'" value="'.$envar[3].'" />';
			$echo_str .= "\n\t\t\t\t\t\t".'</div>';
			$echo_str .= "\n\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Description">'.$envar[4].'</td>';
			$echo_str .= "\n\t\t\t\t\t".'<td data-colname="">';
			$echo_str .= "\n\t\t\t\t\t\t".'<img id="wfu_envar_save_'.$ii.'" src="'.WFU_IMAGE_GENERIC_OK.'" class="button wfu_envar_save" width="28px" height="28px" title="press to save changes" disabled="disabled" onclick="wfu_update_envar('.$ii.');" />';
			$echo_str .= "\n\t\t\t\t\t\t".'<img src="'.WFU_IMAGE_OVERLAY_LOADING.'" class="button wfu_envar_updating" width="28px" height="28px" disabled="disabled" />';
			$echo_str .= "\n\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t".'</tr>';
			$ii++;
		}
	}
	$echo_str .= "\n\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t".'</table>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<script type="text/javascript">if(window.addEventListener) { window.addEventListener("load", function() { wfu_Attach_Advanced_Admin_Events(); }, false) } else if(window.attachEvent) { window.attachEvent("onload", function() { wfu_Attach_Advanced_Admin_Events(); }); } else { window["onload"] = function() { wfu_Attach_Advanced_Admin_Events(); }; }</script>';
	$echo_str .= "\n".'</div>';
	
	echo $echo_str;
}