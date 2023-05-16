<?php

add_filter('_wfu_settings_definitions', 'wfu_onedrive_settings_definitions', 10, 1);
add_filter('_wfu_manage_settings_bottom', 'wfu_onedrive_manage_settings', 10, 1);
add_filter('_wfu_update_settings', 'wfu_onedrive_update_settings', 10, 2);

function wfu_onedrive_settings_definitions($settings) {
	$settings += array(
		"onedrive_accesstoken" => array("string", ""),
		"onedrive_defaultpath" => array("string", ""),
		"onedrive_includeuserdata" => array("number", ""),
		"onedrive_conflictpolicy" => array("string", "")
	);
	
	return $settings;
}

function wfu_onedrive_manage_settings($echo_str) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>Microsoft OneDrive Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Microsoft OneDrive Uploads</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.6.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold;">Not supported!</label><label style="margin-left:6px;">Your PHP version does not support the Microsoft OneDrive API. If you want to enable Microsoft OneDrive uploads, you need to install a PHP version newer than '.$min_version.'.</label>';
	else {
		$onedrive_nonce = wp_create_nonce( "wfu-onedrive-authorize-app" );
		if ( $plugin_options['onedrive_accesstoken'] == "" ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:darkred;">Not activated!</label><button type="button" style="margin-left:6px;" onclick="wfu_onedrive_authorize_app_start(\''.$onedrive_nonce.'\');">Press here</button><label style="margin-left:6px;">to enable Microsoft OneDrive uploads. A new window will show up with a request to allow Wordpress File Upload Plugin. If you are not already logged in your Microsoft OneDrive account, login will also be requested. Press <strong>Allow</strong> button to accept the plugin, copy/paste the code that will show up in this textbox</label><input id="wfu_onedrive_authorization_code" type="text" style="margin-left:6px;" /><label style="margin-left:6px;">and then press</label><button type="button" style="margin-left:6px;" onclick="wfu_onedrive_authorize_app_finish(\''.$onedrive_nonce.'\');">Finish</button>';
		else $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:green;">Activated!</label><label style="margin-left:6px;">To reset Microsoft OneDrive activation</label><button type="button" style="margin-left:6px;" onclick="wfu_onedrive_authorize_app_reset(\''.$onedrive_nonce.'\');">press here</button>';
	}
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_onedrive_defaultpath">Microsoft OneDrive Default Path</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_onedrive_defaultpath" id="wfu_onedrive_defaultpath" type="text" value="'.$plugin_options['onedrive_defaultpath'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>This is the Microsoft OneDrive path that will be used for transferring files to Microsoft OneDrive through the File Browser.</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['onedrive_defaultpath'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_onedrive_includeuserdata">Include Userdata</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_onedrive_includeuserdata" id="wfu_onedrive_includeuserdata" type="checkbox"'.($plugin_options['onedrive_includeuserdata'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Include additional userdata (if any) when transferring files to Microsoft OneDrive through the File Browser.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['onedrive_includeuserdata'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_onedrive_conflictpolicy">Conflict Policy</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_onedrive_conflictpolicy" id="wfu_onedrive_conflictpolicy_fail" type="radio"'.($plugin_options['onedrive_conflictpolicy'] == 'fail' ? ' checked="checked"' : '' ).' value="fail" />fail <input name="wfu_onedrive_conflictpolicy" id="wfu_onedrive_conflictpolicy_replace" type="radio"'.($plugin_options['onedrive_conflictpolicy'] == 'replace' ? ' checked="checked"' : '' ).' value="replace" />replace <input name="wfu_onedrive_conflictpolicy" id="wfu_onedrive_conflictpolicy_rename" type="radio"'.($plugin_options['onedrive_conflictpolicy'] != 'fail' && $plugin_options['onedrive_conflictpolicy'] != 'replace' ? ' checked="checked"' : '' ).' value="rename" />rename<br />When transferring files to Microsoft OneDrive through the File Browser, define what will happen if a file with the same name already exists at destination, fail the transfer, replace the existing file or rename the new one.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['onedrive_conflictpolicy'] == '' ? 'rename' : $plugin_options['onedrive_conflictpolicy'] ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';

	return $echo_str;
}

function wfu_onedrive_update_settings($arg, $plugin_options) {
	$onedrive_includeuserdata = ( isset($_POST['wfu_onedrive_includeuserdata']) ? ( $_POST['wfu_onedrive_includeuserdata'] == "on" ? 1 : 0 ) : 0 );
	$onedrive_conflictpolicy = ( isset($_POST['wfu_onedrive_conflictpolicy']) ? ( $_POST['wfu_onedrive_conflictpolicy'] == "" ? "rename" : $_POST['wfu_onedrive_conflictpolicy'] ) : "rename" );
	if ( !isset($_POST['wfu_onedrive_defaultpath']) ) $arg["pass"] = false;
	elseif ( $arg["pass"] ) {
		$arg["new_plugin_options"]['onedrive_accesstoken'] = $plugin_options['onedrive_accesstoken'];
		$arg["new_plugin_options"]['onedrive_defaultpath'] = sanitize_text_field($_POST['wfu_onedrive_defaultpath']);
		$arg["new_plugin_options"]['onedrive_includeuserdata'] = $onedrive_includeuserdata;
		$arg["new_plugin_options"]['onedrive_conflictpolicy'] = $onedrive_conflictpolicy;
	}
	
	return $arg;
}