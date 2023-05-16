<?php

/**
 * Settings Page in Dashboard Area of Plugin
 *
 * This file contains functions related to Settings page of plugin's Dashboard
 * area.
 *
 * @link /lib/wfu_admin_settings.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 3.0.0
 */

/**
 * Initialize Plugin's Settings.
 *
 * This function initializes the plugin's settings.
 *
 * @since 4.1.0
 *
 * @return array Array containing plugin settings and their default values.
 */
function wfu_settings_definitions() {
	$settings = array(
		"version" => array("number", "1.0"),
		"shortcode" => array("string", ""),
		"hashfiles" => array("number", ""),
		"basedir" => array("string", ""),
		"personaldata" => array("number", ""),
		"postmethod" => array("number", ""),
		"modsecurity" => array("number", ""),
		"userstatehandler" => array("number", "dboption"),
		"relaxcss" => array("number", ""),
		"admindomain" => array("number", ""),
		"mediacustom" => array("number", ""),
		"createthumbnails" => array("number", ""),
		"includeotherfiles" => array("number", ""),
		"altserver" => array("number", ""),
		"captcha_sitekey" => array("string", ""),
		"captcha_secretkey" => array("string", ""),
		"browser_permissions" => array("array", "")
	);
	/**
	 * Customize settings definitions.
	 *
	 * This filter allows extensions to add their own settings.
	 *
	 * @since 4.1.0
	 *
	 * @param array $settings Array containing plugin settings and their
	 *        default values.
	*/
	$settings = apply_filters("_wfu_settings_definitions", $settings);
	
	return $settings;
}

/**
 * Encode Plugin Settings.
 *
 * This function encodes the plugin settings array into a string.
 *
 * @since 2.1.3
 *
 * @param array $plugin_options The plugin settings.
 *
 * @return string The encoded plugin settings.
 */
function wfu_encode_plugin_options($plugin_options) {
	$settings = wfu_settings_definitions();
	$encoded_options = array();
	foreach ( $settings as $setting => $data ) {
		$encoded = $setting."=";
		if ( !isset($plugin_options[$setting]) ) $encoded .= $data[1];
		elseif ( $data[0] == "string" ) $encoded .= wfu_plugin_encode_string($plugin_options[$setting]);
		elseif ( $data[0] == "array" ) $encoded .= wfu_encode_array_to_string($plugin_options[$setting]);
		else $encoded .= $plugin_options[$setting];
		array_push($encoded_options, $encoded);
	}
	
	return implode(";", $encoded_options);
}

/**
 * Decode Plugin Settings.
 *
 * This function decodes the plugin settings string into an array.
 *
 * @since 2.1.3
 *
 * @param string $encoded_options The encoded plugin settings.
 *
 * @return array The decoded plugin settings.
 */
function wfu_decode_plugin_options($encoded_options) {
	$settings = wfu_settings_definitions();
	foreach ( $settings as $setting => $data )
		$plugin_options[$setting] = $data[1];

	$decoded_array = explode(';', $encoded_options);
	foreach ($decoded_array as $decoded_item) {
		if ( trim($decoded_item) != "" ) {
			list($item_key, $item_value) = explode("=", $decoded_item, 2);
			if ( isset($settings[$item_key]) ) {
				if ( $settings[$item_key][0] == "string" ) $plugin_options[$item_key] = wfu_plugin_decode_string($item_value);
				elseif ( $settings[$item_key][0] == "array" ) $plugin_options[$item_key] = wfu_decode_array_from_string($item_value);
				else $plugin_options[$item_key] = $item_value;
			}
		}
	}

	return $plugin_options;
}

/**
 * Display the Settings Page.
 *
 * This function displays the Settings page of the plugin's Dashboard area.
 *
 * @since 2.1.2
 *
 * @param string $message Optional. A message to display on top of the page.
 *
 * @return string The HTML output of the plugin's Settings Dashboard page.
 */
function wfu_manage_settings($message = '') {
	if ( !current_user_can( 'manage_options' ) ) return;

	$siteurl = site_url();
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	
	$echo_str = '<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Settings");
	$echo_str .= "\n\t\t".'<form enctype="multipart/form-data" name="editsettings" id="editsettings" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=edit_settings" class="validate">';
	$nonce = wp_nonce_field('wfu_edit_admin_settings', '_wpnonce', false, false);
	$nonce_ref = wp_referer_field(false);
	$echo_str .= "\n\t\t\t".$nonce;
	$echo_str .= "\n\t\t\t".$nonce_ref;
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="action" value="edit_settings">';
	$echo_str .= "\n\t\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	/**
	 * Add Topmost Settings Fields.
	 *
	 * This filter allows extensions to add their own settings fields in
	 * Settings page before the default ones.
	 *
	 * @since 4.1.0
	 *
	 * @param string $echo_str The HTML output of the plugin's Settings
	 *        Dashboard page.
	*/
	$echo_str = apply_filters("_wfu_manage_settings_top", $echo_str);
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>General Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_hashfiles">Hash Files</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_hashfiles" id="wfu_hashfiles" type="checkbox"'.($plugin_options['hashfiles'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Enables better control of uploaded files, but slows down performance when uploaded files are larger than 100MBytes';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['hashfiles'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_basedir">Base Directory</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_basedir" id="wfu_basedir" type="text" value="'.$plugin_options['basedir'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['basedir'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_personaldata">Personal Data</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_personaldata" id="wfu_personaldata" type="checkbox"'.($plugin_options['personaldata'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Enable this option if your website is subject to EU GDPR regulation and you want to define how to handle personal data';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['personaldata'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_postmethod">Post Method</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<select name="wfu_postmethod" id="wfu_postmethod" value="'.$plugin_options['postmethod'].'">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="fopen"'.( $plugin_options['postmethod'] == 'fopen' || $plugin_options['postmethod'] == '' ? ' selected="selected"' : '' ).'>Using fopen (default)</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="curl"'.( $plugin_options['postmethod'] == 'curl' ? ' selected="selected"' : '' ).'>Using cURL</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="socket"'.( $plugin_options['postmethod'] == 'socket' ? ' selected="selected"' : '' ).'>Using Sockets</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</select>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.( $plugin_options['postmethod'] == 'fopen' || $plugin_options['postmethod'] == '' ? 'Using fopen' : ( $plugin_options['postmethod'] == 'curl' ? 'Using cURL' : 'Using Sockets' ) ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_modsecurity">ModSecurity Restrictions</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_modsecurity" id="wfu_modsecurity" type="checkbox"'.($plugin_options['modsecurity'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Activate this option if <strong>mod_security</strong> module is installed on the webserver, otherwise <strong>Code Hook</strong> and <strong>Dropbox</strong> features may not work properly.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['modsecurity'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_userstatehandler">User State Handler</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<select name="wfu_userstatehandler" id="wfu_userstatehandler" value="'.$plugin_options['userstatehandler'].'">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="dboption"'.( $plugin_options['userstatehandler'] == 'dboption' ? ' selected="selected"' : '' ).'>Cookies/DB (default)</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="session"'.( $plugin_options['userstatehandler'] == 'session' || $plugin_options['userstatehandler'] == '' ? ' selected="selected"' : '' ).'>Session</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</select>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.( $plugin_options['userstatehandler'] == 'session' || $plugin_options['userstatehandler'] == '' ? 'Session' : ( $plugin_options['userstatehandler'] == 'dboption' ? 'Cookies/DB' : 'Session' ) ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_relaxcss">Relax CSS Rules</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_relaxcss" id="wfu_relaxcss" type="checkbox"'.($plugin_options['relaxcss'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> If enabled then the textboxes and the buttons of the plugin will inherit the theme\'s styling';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['relaxcss'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_admindomain">Admin Domain</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<select name="wfu_admindomain" id="wfu_admindomain" value="'.$plugin_options['admindomain'].'">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="siteurl"'.( $plugin_options['admindomain'] == 'siteurl' || $plugin_options['admindomain'] == '' ? ' selected="selected"' : '' ).'>Using site_url (default)</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="adminurl"'.( $plugin_options['admindomain'] == 'adminurl' ? ' selected="selected"' : '' ).'>Using admin_url</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<option value="homeurl"'.( $plugin_options['admindomain'] == 'homeurl' ? ' selected="selected"' : '' ).'>Using home_url</option>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</select>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.( $plugin_options['admindomain'] == 'siteurl' || $plugin_options['admindomain'] == '' ? 'Using site_url' : ( $plugin_options['admindomain'] == 'adminurl' ? 'Using admin_url' : 'Using home_url' ) ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_mediacustom">Show Custom Fields in Media Library</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_mediacustom" id="wfu_mediacustom" type="checkbox"'.($plugin_options['mediacustom'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> If enabled and the uploaded files are added to Media Library then any user fields submitted together with the files will be shown in Media Library';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['mediacustom'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_createthumbnails">Create Thumbnails</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_createthumbnails" id="wfu_createthumbnails" type="checkbox"'.($plugin_options['createthumbnails'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> If this option is activated then thumbnails will be generated for every image file uploaded by the plugin';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['createthumbnails'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_includeotherfiles">Include Other Files in Plugin\'s Database</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_includeotherfiles" id="wfu_includeotherfiles" type="checkbox"'.($plugin_options['includeotherfiles'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> If enabled administrators can include in the plugin\'s database additional files through the File Browser';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['includeotherfiles'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_altserver">Use Alternative Iptanus Server</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_altserver" id="wfu_altserver" type="checkbox"'.($plugin_options['altserver'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Switches to the alternative Iptanus server, residing on Google Cloud, for getting information such as latest version number.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['altserver'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_captcha_sitekey">Google ReCaptcha Site Key</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_captcha_sitekey" id="wfu_captcha_sitekey" type="text" value="'.$plugin_options['captcha_sitekey'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['captcha_sitekey'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_captcha_secretkey">Google ReCaptcha Secret Key</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_captcha_secretkey" id="wfu_captcha_secretkey" type="text" value="'.$plugin_options['captcha_secretkey'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['captcha_secretkey'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_browser_permissions">File Browser Permissions</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	// role permissions
	$echo_str .= "\n\t\t\t\t\t\t\t".'<button type="button" style="margin-right:8px;" onclick="if (this.innerHTML.substr(0, 4) == \'Edit\') { this.innerHTML = \'Close Role Permissions\'; document.getElementById(\'wfu_rolepermissions_table\').style.display=\'\'; } else { this.innerHTML = \'Edit Role Permissions\'; document.getElementById(\'wfu_rolepermissions_table\').style.display=\'none\'; }">Edit Role Permissions</button>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<div id="wfu_rolepermissions_table" style="margin:20px 0px; display:none;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<table class="widefat" style="background:none;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<tbody>';
	$ret = wfu_get_rolepermissions_props(true);
	$role_props = $ret["role_props"];
	$roles_enc = $ret["roles_enc"];
	$echo_str .= $ret["echo_str"];
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'</table>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
	// user permissions
	$args = array();
	if ( WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS") > 0 ) {
		$args = array( 'number' => WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS") );
	}
	/** This filter is documented in lib/wfu_admin_browser.php */
	$args = apply_filters("_wfu_get_users", $args, "settings_userpermissions");
	$users = get_users($args);
	$echo_str .= "\n\t\t\t\t\t\t\t".'<button type="button" onclick="if (this.innerHTML.substr(0, 4) == \'Edit\') { this.innerHTML = \'Close User Permissions\'; document.getElementById(\'wfu_userpermissions_container\').style.display=\'\'; } else { this.innerHTML = \'Edit User Permissions\'; document.getElementById(\'wfu_userpermissions_container\').style.display=\'none\'; }">Edit User Permissions</button>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<div id="wfu_userpermissions_container" style="margin-top:20px; position:relative; display:none;">';
	$echo_str .= wfu_add_loading_overlay("\n\t\t\t\t\t\t\t\t", "userpermissions");
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<div class="wfu_userpermissions_header" style="width: 100%;">';
	if ( WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS") > 0 ) {
		$result = count_users();
		$users_total = $result['total_users'];
		$pages = ceil($users_total / WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS"));
		$echo_str .= wfu_add_pagination_header("\n\t\t\t\t\t\t\t\t\t", "userpermissions", 1, $pages);
	}
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<table id="wfu_userpermissions_table" class="widefat" style="background:none;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<tbody>';
	$echo_str .= wfu_get_userpermissions_rows($users, $role_props);
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'</table>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_roles" name="wfu_fbperm_roles" type="hidden" value="'.$roles_enc.'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_users" name="wfu_fbperm_users" type="hidden" value="" />';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<script type="text/javascript">var attach_browserprop_loader = function() { wfu_update_browserpermission_option("user", "0"); }; if(window.addEventListener) { window.addEventListener("load", attach_browserprop_loader, false); } else if(window.attachEvent) { window.attachEvent("onload", attach_browserprop_loader); } else { window["onload"] = attach_browserprop_loader; }</script>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	/**
	 * Add Bottom-most Settings Fields.
	 *
	 * This filter allows extensions to add their own settings fields in
	 * Settings page after the default ones.
	 *
	 * @since 4.1.0
	 *
	 * @param string $echo_str The HTML output of the plugin's Settings
	 *        Dashboard page.
	*/
	$echo_str = apply_filters("_wfu_manage_settings_bottom", $echo_str);
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	$echo_str .= "\n\t\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t\t".'<input type="submit" class="button-primary" name="submitform" value="Update" />';
	$echo_str .= "\n\t\t\t".'</p>';
	$echo_str .= "\n\t\t".'</form>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n".'</div>';
	
	echo $echo_str;
}

/**
 * Get Role Permissions List.
 *
 * This function generates the HTML code of the list of back-end browser role
 * permissions.
 *
 * @since 3.5.0
 *
 * @param bool $echo_rows Optional. True if generated HTML code must be echoed.
 *
 * @return string The HTML code of the list of back-end browser role
 *         permissions.
 */
function wfu_get_rolepermissions_props($echo_rows = false) {
	global $wp_roles;
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$role_props = array();
	
	$roleprops_def = "";
	if ( isset($plugin_options['browser_permissions']) && $plugin_options['browser_permissions'] != "" && isset($plugin_options['browser_permissions']['roles']) && $plugin_options['browser_permissions']['roles'] != "" && isset($plugin_options['browser_permissions']['roles']['0']) ) $roleprops_def = $plugin_options['browser_permissions']['roles']['0'];
	$rl_view_def = ( strpos($roleprops_def, 'v') !== false );
	$rl_dl_def = ( $rl_view_def && strpos($roleprops_def, 'd') !== false );
	$rl_edit_def = ( $rl_view_def && strpos($roleprops_def, 'e') !== false );
	$rl_del_def = ( $rl_view_def && strpos($roleprops_def, 'l') !== false );
	$echo_str = "";
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'<tr style="background-color:#f9f9f9; font-weight:bold;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td style="width:30%;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<label>Default Role</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input type="checkbox" class="wfu_rolepermissions_option" style="visibility:hidden;" /><label style="visibility:hidden;"> default</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input type="checkbox" class="wfu_rolepermissions_option" style="visibility:hidden;" /><label style="visibility:hidden;"> fromrole</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_0_view" type="checkbox"'.( $rl_view_def ? ' checked="checked"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \'0\', true);" /><label for="wfu_fbperm_role_0_view"> view</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_0_dl" type="checkbox"'.( $rl_dl_def ? ' checked="checked"' : '' ).( $rl_view_def ? '' : ' disabled="disabled"' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \'0\', true);" /><label for="wfu_fbperm_role_0_dl"> download</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_0_edit" type="checkbox"'.( $rl_edit_def ? ' checked="checked"' : '' ).( $rl_view_def ? '' : ' disabled="disabled"' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \'0\', true);" /><label for="wfu_fbperm_role_0_edit"> edit</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_0_del" type="checkbox"'.( $rl_del_def ? ' checked="checked"' : '' ).( $rl_view_def ? '' : ' disabled="disabled"' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \'0\', true);" /><label for="wfu_fbperm_role_0_del"> delete</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'</tr>';
	$roles_enc = "0[".( $rl_view_def ? "v" : "" ).( $rl_dl_def ? "d" : "" ).( $rl_edit_def ? "e" : "" ).( $rl_del_def ? "l" : "" )."]";
	$role_props["0"] = ( $rl_view_def ? "v" : "" ).( $rl_dl_def ? "d" : "" ).( $rl_edit_def ? "e" : "" ).( $rl_del_def ? "l" : "" );
	$roles = $wp_roles->get_names();
	foreach ( $roles as $roleid => $rolename ) {
		$roleprops = "";
		if ( isset($plugin_options['browser_permissions']) && $plugin_options['browser_permissions'] != "" && isset($plugin_options['browser_permissions']['roles']) && $plugin_options['browser_permissions']['roles'] != "" && isset($plugin_options['browser_permissions']['roles'][$roleid]) ) $roleprops = $plugin_options['browser_permissions']['roles'][$roleid];
		$is_admin = ( $roleid == "administrator" );
		$rl_def = ( $is_admin ? false : $roleprops == "" );
		$rl_view = ( $is_admin ? true : ( $rl_def ? $rl_view_def : strpos($roleprops, 'v') !== false ) );
		$rl_dl = ( $is_admin ? true : ( $rl_def ? $rl_dl_def : $rl_view && strpos($roleprops, 'd') !== false ) );
		$rl_edit = ( $is_admin ? true : ( $rl_def ? $rl_edit_def : $rl_view && strpos($roleprops, 'e') !== false ) );
		$rl_del = ( $is_admin ? true : ( $rl_def ? $rl_del_def : $rl_view && strpos($roleprops, 'l') !== false ) );
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'<tr id="wfu_fbperm_role_'.$roleid.'_row" class="wfu_fbperm_role_tr"'.( $is_admin || $rl_def ? '' : ' style="font-weight:bold;"' ).'>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<label>'.$rolename .'</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_'.$roleid.'_def" type="checkbox"'.( $rl_def ? ' checked="checked"' : '' ).( $is_admin ? ' disabled="disabled"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \''.$roleid.'\', true);" /><label for="wfu_fbperm_role_'.$roleid.'_def"> default</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_'.$roleid.'_view" type="checkbox"'.( $rl_view ? ' checked="checked"' : '' ).( $is_admin || $rl_def ? ' disabled="disabled"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \''.$roleid.'\', true);" /><label for="wfu_fbperm_role_'.$roleid.'_view"> view</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_'.$roleid.'_dl" type="checkbox"'.( $rl_dl ? ' checked="checked"' : '' ).( $is_admin || $rl_def || !$rl_view ? ' disabled="disabled"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \''.$roleid.'\', true);" /><label for="wfu_fbperm_role_'.$roleid.'_dl"> download</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_'.$roleid.'_edit" type="checkbox"'.( $rl_edit ? ' checked="checked"' : '' ).( $is_admin || $rl_def || !$rl_view ? ' disabled="disabled"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \''.$roleid.'\', true);" /><label for="wfu_fbperm_role_'.$roleid.'_edit"> edit</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_role_'.$roleid.'_del" type="checkbox"'.( $rl_del ? ' checked="checked"' : '' ).( $is_admin || $rl_def || !$rl_view ? ' disabled="disabled"' : '' ).' class="wfu_rolepermissions_option" onchange="wfu_update_browserpermission_option(\'role\', \''.$roleid.'\', true);" /><label for="wfu_fbperm_role_'.$roleid.'_del"> delete</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'</tr>';
		$roles_enc .= ",".$roleid."[".( $rl_def ? "" : "D".( $rl_view ? "v" : "" ).( $rl_dl ? "d" : "" ).( $rl_edit ? "e" : "" ).( $rl_del ? "l" : "" ) )."]";
		$role_props[$roleid] = ( $rl_view ? "v" : "" ).( $rl_dl ? "d" : "" ).( $rl_edit ? "e" : "" ).( $rl_del ? "l" : "" );
	}
	
	return array( "role_props" => $role_props, "echo_str" => $echo_str, "roles_enc" => $roles_enc );
}

/**
 * Get User Permissions List.
 *
 * This function generates the HTML code of the list of back-end browser user
 * permissions.
 *
 * @since 3.5.0
 *
 * @param array $users Array of users to include in the list.
 * @param string $role_props Encoded role properties to pass to the users.
 *
 * @return string The HTML code of the list of back-end browser user
 *         permissions.
 */
function wfu_get_userpermissions_rows($users, $role_props) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$userprops_def = "";
	if ( isset($plugin_options['browser_permissions']) && $plugin_options['browser_permissions'] != "" && isset($plugin_options['browser_permissions']['users']) && $plugin_options['browser_permissions']['users'] != "" && isset($plugin_options['browser_permissions']['users']['0']) ) $userprops_def = $plugin_options['browser_permissions']['users']['0'];
	$ur_role_def = ( $userprops_def == '' );
	$ur_view_def = ( !$ur_role_def && strpos($userprops_def, 'v') !== false );
	$ur_dl_def = ( !$ur_role_def && $ur_view_def && strpos($userprops_def, 'd') !== false );
	$ur_edit_def = ( !$ur_role_def && $ur_view_def && strpos($userprops_def, 'e') !== false );
	$ur_del_def = ( !$ur_role_def && $ur_view_def && strpos($userprops_def, 'l') !== false );
	$echo_str = "";
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'<tr style="background-color:#f9f9f9; font-weight:bold;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td style="width:30%;">';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<label>Default User</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input type="checkbox" class="wfu_userpermissions_option" style="visibility:hidden;" /><label style="visibility:hidden;"> default</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_0_role" type="checkbox"'.( $ur_role_def ? ' checked="checked"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \'0\', true);" /><label for="wfu_fbperm_user_0_role"> from role</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_0_view" type="checkbox"'.( $ur_view_def ? ' checked="checked"' : '' ).( $ur_role_def ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \'0\', true);" /><label for="wfu_fbperm_user_0_view"> view</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_0_dl" type="checkbox"'.( $ur_dl_def ? ' checked="checked"' : '' ).( $ur_role_def || !$ur_view_def ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \'0\', true);" /><label for="wfu_fbperm_user_0_dl"> download</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_0_edit" type="checkbox"'.( $ur_edit_def ? ' checked="checked"' : '' ).( $ur_role_def || !$ur_view_def ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \'0\', true);" /><label for="wfu_fbperm_user_0_edit"> edit</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_0_del" type="checkbox"'.( $ur_del_def ? ' checked="checked"' : '' ).( $ur_role_def || !$ur_view_def ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \'0\', true);" /><label for="wfu_fbperm_user_0_del"> delete</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'</tr>';
	$users_enc = "0[".( $ur_role_def ? "" : "R".( ( $ur_view_def ? "v" : "" ).( $ur_dl_def ? "d" : "" ).( $ur_edit_def ? "e" : "" ).( $ur_del_def ? "l" : "" ) ) )."]";
	foreach ( $users as $userid => $user ) {
		$userid = $user->user_login;
		$userprops = "";
		if ( isset($plugin_options['browser_permissions']) && $plugin_options['browser_permissions'] != "" && isset($plugin_options['browser_permissions']['users']) && $plugin_options['browser_permissions']['users'] != "" && isset($plugin_options['browser_permissions']['users'][$userid]) ) $userprops = $plugin_options['browser_permissions']['users'][$userid];
		// calculate permissions inherited from user's roles
		$user_roles = wfu_get_user_valid_role_names($user);
		$is_admin = in_array("administrator", $user_roles);
		if ( count($user_roles) > 0 ) {
			$ur_view_from_roles = false;
			$ur_dl_from_roles = false;
			$ur_edit_from_roles = false;
			$ur_del_from_roles = false;
			foreach ( $user_roles as $user_role ) {
				$ur_view_from_roles = ( $ur_view_from_roles || strpos($role_props[$user_role], 'v') !== false );
				$ur_dl_from_roles = ( $ur_view_from_roles && ( $ur_dl_from_roles || strpos($role_props[$user_role], 'd') !== false ) );
				$ur_edit_from_roles = ( $ur_view_from_roles && ( $ur_edit_from_roles || strpos($role_props[$user_role], 'e') !== false ) );
				$ur_del_from_roles = ( $ur_view_from_roles && ( $ur_del_from_roles || strpos($role_props[$user_role], 'l') !== false ) );
			}
		}
		// if user has no roles then inherit permissions from default role
		else {
			$ur_view_from_roles = ( strpos($role_props["0"], 'v') !== false );
			$ur_dl_from_roles = ( strpos($role_props["0"], 'd') !== false );
			$ur_edit_from_roles = ( strpos($role_props["0"], 'e') !== false );
			$ur_del_from_roles = ( strpos($role_props["0"], 'l') !== false );
		}
		
		$ur_def = ( $is_admin ? false : $userprops == "" );
		$ur_role = ( $is_admin ? true : ( $ur_def ? $ur_role_def : strpos($userprops, 'R') === false ) );
		$ur_view = ( $ur_def ? ( $ur_role_def ? $ur_view_from_roles : $ur_view_def ) : ( $ur_role ? $ur_view_from_roles : strpos($userprops, 'v') !== false ) );
		$ur_dl = ( $ur_def ? ( $ur_role_def ? $ur_dl_from_roles : $ur_dl_def ) : ( $ur_role ? $ur_dl_from_roles : $ur_view && strpos($userprops, 'd') !== false ) );
		$ur_edit = ( $ur_def ? ( $ur_role_def ? $ur_edit_from_roles : $ur_edit_def ) : ( $ur_role ? $ur_edit_from_roles : $ur_view && strpos($userprops, 'e') !== false ) );
		$ur_del = ( $ur_def ? ( $ur_role_def ? $ur_del_from_roles : $ur_del_def ) : ( $ur_role ? $ur_del_from_roles : $ur_view && strpos($userprops, 'l') !== false ) );
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'<tr id="wfu_fbperm_user_'.$userid.'_row" class="wfu_fbperm_user_tr"'.( $is_admin || $ur_def ? '' : ' style="font-weight:bold;"' ).'>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<label>'.$user->display_name.' </label><label style="font-size:smaller;">('.( count($user_roles) > 0 ? implode(', ', $user_roles) : 'no roles' ).')</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_roles" type="hidden" value="'.implode(',', $user_roles).'" />';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_def" type="checkbox"'.( $ur_def ? ' checked="checked"' : '' ).( $is_admin ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_def"> default</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_role" type="checkbox"'.( $ur_role ? ' checked="checked"' : '' ).( $is_admin || $ur_def ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_role"> from role</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_view" type="checkbox"'.( $ur_view ? ' checked="checked"' : '' ).( $ur_def || $ur_role ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_view"> view</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_dl" type="checkbox"'.( $ur_dl ? ' checked="checked"' : '' ).( $ur_def || $ur_role || !$ur_view ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_dl"> download</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_edit" type="checkbox"'.( $ur_edit ? ' checked="checked"' : '' ).( $ur_def || $ur_role || !$ur_view ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_edit"> edit</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'<td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t\t".'<input id="wfu_fbperm_user_'.$userid.'_del" type="checkbox"'.( $ur_del ? ' checked="checked"' : '' ).( $ur_def || $ur_role || !$ur_view ? ' disabled="disabled"' : '' ).' class="wfu_userpermissions_option" onchange="wfu_update_browserpermission_option(\'user\', \''.$userid.'\', true);" /><label for="wfu_fbperm_user_'.$userid.'_del"> delete</label>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t\t".'</tr>';
		$users_enc .= ",".$userid."[".( $ur_def ? "" : "D".( $ur_role ? "" : "R".( $ur_view ? "v" : "" ).( $ur_dl ? "d" : "" ).( $ur_edit ? "e" : "" ).( $ur_del ? "l" : "" ) ) )."]";
	}
	
	return $echo_str;
}

/**
 * Update Settings.
 *
 * This function updates plugin's settings.
 *
 * @since 2.1.2
 *
 * @return bool Always true.
 */
function wfu_update_settings() {
	if ( !current_user_can( 'manage_options' ) ) return;
	if ( !check_admin_referer('wfu_edit_admin_settings') ) return;
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$new_plugin_options = array();

//	$enabled = ( isset($_POST['wfu_enabled']) ? ( $_POST['wfu_enabled'] == "on" ? 1 : 0 ) : 0 ); 
	$hashfiles = ( isset($_POST['wfu_hashfiles']) ? ( $_POST['wfu_hashfiles'] == "on" ? 1 : 0 ) : 0 );
	$personaldata = ( isset($_POST['wfu_personaldata']) ? ( $_POST['wfu_personaldata'] == "on" ? 1 : 0 ) : 0 );
	$relaxcss = ( isset($_POST['wfu_relaxcss']) ? ( $_POST['wfu_relaxcss'] == "on" ? 1 : 0 ) : 0 ); 
	$mediacustom = ( isset($_POST['wfu_mediacustom']) ? ( $_POST['wfu_mediacustom'] == "on" ? 1 : 0 ) : 0 ); 
	$includeotherfiles = ( isset($_POST['wfu_includeotherfiles']) ? ( $_POST['wfu_includeotherfiles'] == "on" ? 1 : 0 ) : 0 ); 
	$altserver = ( isset($_POST['wfu_altserver']) ? ( $_POST['wfu_altserver'] == "on" ? 1 : 0 ) : 0 ); 
	if ( isset($_POST['wfu_basedir']) && isset($_POST['wfu_postmethod']) && isset($_POST['wfu_userstatehandler']) && isset($_POST['wfu_admindomain']) && isset($_POST['submitform']) ) {
		if ( $_POST['submitform'] == "Update" ) {
			$new_plugin_options['version'] = '1.0';
			$new_plugin_options['shortcode'] = $plugin_options['shortcode'];
			$new_plugin_options['hashfiles'] = $hashfiles;
			$new_plugin_options['basedir'] = sanitize_text_field($_POST['wfu_basedir']);
			$new_plugin_options['personaldata'] = $personaldata;
			$new_plugin_options['postmethod'] = sanitize_text_field($_POST['wfu_postmethod']);
			$new_plugin_options['userstatehandler'] = sanitize_text_field($_POST['wfu_userstatehandler']);
			$new_plugin_options['relaxcss'] = $relaxcss;
			$new_plugin_options['admindomain'] = sanitize_text_field($_POST['wfu_admindomain']);
			$new_plugin_options['mediacustom'] = $mediacustom;
			$new_plugin_options['includeotherfiles'] = $includeotherfiles;
			$new_plugin_options['altserver'] = $altserver;
			$modsecurity = ( isset($_POST['wfu_modsecurity']) ? ( $_POST['wfu_modsecurity'] == "on" ? 1 : 0 ) : 0 );
			$createthumbnails = ( isset($_POST['wfu_createthumbnails']) ? ( $_POST['wfu_createthumbnails'] == "on" ? 1 : 0 ) : 0 );
			if ( !isset($_POST['wfu_captcha_sitekey']) || !isset($_POST['wfu_captcha_secretkey']) || !isset($_POST['wfu_fbperm_roles']) || !isset($_POST['wfu_fbperm_users']) ) return true;
			$new_plugin_options['modsecurity'] = $modsecurity;
			$new_plugin_options['createthumbnails'] = $createthumbnails;
			$new_plugin_options['captcha_sitekey'] = sanitize_text_field($_POST['wfu_captcha_sitekey']);
			$new_plugin_options['captcha_secretkey'] = sanitize_text_field($_POST['wfu_captcha_secretkey']);
			$browser_permissions['roles'] = array();
			$browser_permissions['users'] = array();
			$roles = explode(",", sanitize_text_field($_POST['wfu_fbperm_roles']));
			foreach ( $roles as $role ) {
				$pos = strpos($role, "[");
				$roleid = substr($role, 0, $pos);
				if ( $role != $roleid."[]" ) $browser_permissions['roles'][$roleid] = substr($role, $pos + 1, -1);
			}
			$users = explode(",", sanitize_text_field($_POST['wfu_fbperm_users']));
			foreach ( $users as $user ) {
				$pos = strpos($user, "[");
				$userid = substr($user, 0, $pos);
				if ( $user != $userid."[]" ) $browser_permissions['users'][$userid] = substr($user, $pos + 1, -1);
			}
			$new_plugin_options['browser_permissions'] = $browser_permissions;
			//check and update settings of extensions
			$arg["pass"] = true;
			$arg["new_plugin_options"] = $new_plugin_options;
			/**
			 * Customize update of settings.
			 *
			 * This filter allows extensions to customize the update of plugin's
			 * settings and/or update any custom settings.
			 *
			 * @since 4.1.0
			 *
			 * @param array $arg {
			 *     Controls result of update and new settings' values.
			 *
			 *     @type bool $pass True if update operation succeeds, false if
			 *           it must fail.
			 *     @type array $new_plugin_options The array of new settings'
			 *           values.
			 * }
			 * @param array $plugin_options The array of old settings' values.
			*/
			$arg = apply_filters("_wfu_update_settings", $arg, $plugin_options);
			if ( !$arg["pass"] ) return true;
			$new_plugin_options = $arg["new_plugin_options"];
			$encoded_options = wfu_encode_plugin_options($new_plugin_options);
			update_option( "wordpress_file_upload_options", $encoded_options );
			if ( $new_plugin_options['hashfiles'] == '1' && $plugin_options['hashfiles'] != '1' )
				wfu_reassign_hashes();
		}
	}

	return true;
}

/**
 * Update a Plugin Setting.
 *
 * This function updates an individual plugin setting.
 *
 * @since 4.12.0
 *
 * @param string $option The plugin option to change.
 * @param mixed $value The new value of the option.
 */
function wfu_update_setting($option, $value) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$plugin_options[$option] = $value;
	$encoded_options = wfu_encode_plugin_options($plugin_options);
	update_option( "wordpress_file_upload_options", $encoded_options );	
}

