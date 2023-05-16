<?php

add_filter('_wfu_settings_definitions', 'wfu_dropbox_settings_definitions', 10, 1);
add_filter('_wfu_manage_settings_bottom', 'wfu_dropbox_manage_settings', 10, 1);
add_filter('_wfu_update_settings', 'wfu_dropbox_update_settings', 10, 2);

function wfu_dropbox_settings_definitions($settings) {
	$settings += array(
		"dropbox_accesstoken" => array("string", ""),
		"dropbox_defaultpath" => array("string", "")
	);
	
	return $settings;
}

function wfu_dropbox_manage_settings($echo_str) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>Dropbox Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Dropbox Uploads</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = ( /*WFU_VAR("WFU_DROPBOX_USE_V1_API") == "true" ? "5.3.0" :*/ "5.5.0" );
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold;">Not supported!</label><label style="margin-left:6px;">Your PHP version does not support the Dropbox API. If you want to enable Dropbox uploads, you need to install a PHP version newer than '.$min_version.'.</label>';
	else {
		$dropbox_nonce = wp_create_nonce( "wfu-dropbox-authorize-app" );
		if ( $plugin_options['dropbox_accesstoken'] == "" ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:darkred;">Not activated!</label><button type="button" style="margin-left:6px;" onclick="wfu_dropbox_authorize_app_start(\''.$dropbox_nonce.'\');">Press here</button><label style="margin-left:6px;">to enable Dropbox uploads. A new window will show up with a request to allow Wordpress File Upload Plugin. If you are not already logged in your Dropbox account, login will also be requested. Press <strong>Allow</strong> button to accept the plugin, copy/paste the code that will show up in this textbox</label><input id="wfu_dropbox_authorization_code" type="text" style="margin-left:6px;" /><label style="margin-left:6px;">and then press</label><button type="button" style="margin-left:6px;" onclick="wfu_dropbox_authorize_app_finish(\''.$dropbox_nonce.'\');">Finish</button>';
		else $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:green;">Activated!</label><label style="margin-left:6px;">To reset Dropbox activation</label><button type="button" style="margin-left:6px;" onclick="wfu_dropbox_authorize_app_reset(\''.$dropbox_nonce.'\');">press here</button>';
	}
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_dropbox_defaultpath">Dropbox Default Path</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_dropbox_defaultpath" id="wfu_dropbox_defaultpath" type="text" value="'.$plugin_options['dropbox_defaultpath'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>This is the Dropbox path that will be used for transferring files to Dropbox through the File Browser.</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['dropbox_defaultpath'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';

	return $echo_str;
}

function wfu_dropbox_update_settings($arg, $plugin_options) {
	if ( !isset($_POST['wfu_dropbox_defaultpath']) ) $arg["pass"] = false;
	elseif ( $arg["pass"] ) {
		$arg["new_plugin_options"]['dropbox_accesstoken'] = $plugin_options['dropbox_accesstoken'];
		$arg["new_plugin_options"]['dropbox_defaultpath'] = sanitize_text_field($_POST['wfu_dropbox_defaultpath']);
	}
	
	return $arg;
}