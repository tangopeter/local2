<?php

add_filter('_wfu_settings_definitions', 'wfu_gdrive_settings_definitions', 10, 1);
add_filter('_wfu_manage_settings_bottom', 'wfu_gdrive_manage_settings', 10, 1);
add_filter('_wfu_update_settings', 'wfu_gdrive_update_settings', 10, 2);

function wfu_gdrive_settings_definitions($settings) {
	$settings += array(
		"gdrive_accesstoken" => array("string", ""),
		"gdrive_defaultpath" => array("string", ""),
		"gdrive_includeuserdata" => array("number", ""),
		"gdrive_trashduplicates" => array("number", "")
	);
	
	return $settings;
}

function wfu_gdrive_manage_settings($echo_str) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>Google Drive Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Google Drive Uploads</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.4.0";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold;">Not supported!</label><label style="margin-left:6px;">Your PHP version does not support the Google Drive API. If you want to enable Google Drive uploads, you need to install a PHP version newer than '.$min_version.'.</label>';
	else {
		$gdrive_nonce = wp_create_nonce( "wfu-gdrive-authorize-app" );
		if ( $plugin_options['gdrive_accesstoken'] == "" ) $echo_str .= "\n\t\t\t\t\t\t\t".'<input type="hidden" id="wfu_gdrive_nonce" value="'.$gdrive_nonce.'" /><label style="font-weight:bold; color:darkred;">Not activated!</label><button type="button" style="margin-left:6px;" onclick="wfu_gdrive_authorize_app_start();">Press here</button><label style="margin-left:6px;">to enable Google Drive uploads.<br/><span style="display:inline-block; font-style:italic; margin-top:8px;">A new window will show up with a request to allow Wordpress File Upload Plugin. If you are not already logged in your Google Drive account, login will also be requested.<br/>Press <strong>Allow</strong> button to accept the plugin and then wait until the window closes.</span>';
		else $echo_str .= "\n\t\t\t\t\t\t\t".'<input type="hidden" id="wfu_gdrive_nonce" value="'.$gdrive_nonce.'" /><label style="font-weight:bold; color:green;">Activated!</label><label style="margin-left:6px;">To reset Google Drive activation</label><button type="button" style="margin-left:6px;" onclick="wfu_gdrive_authorize_app_reset();">press here</button>';
	}
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_gdrive_defaultpath">Google Drive Default Path</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_gdrive_defaultpath" id="wfu_gdrive_defaultpath" type="text" value="'.$plugin_options['gdrive_defaultpath'].'" />';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>This is the Google Drive path that will be used for transferring files to Google Drive through the File Browser.</label>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.$plugin_options['gdrive_defaultpath'].'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_gdrive_includeuserdata">Include Userdata</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_gdrive_includeuserdata" id="wfu_gdrive_includeuserdata" type="checkbox"'.($plugin_options['gdrive_includeuserdata'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> Include additional userdata (if any) when transferring files to Google Drive through the File Browser.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['gdrive_includeuserdata'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_gdrive_trashduplicates">Trash Duplicates</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_gdrive_trashduplicates" id="wfu_gdrive_trashduplicates" type="checkbox"'.($plugin_options['gdrive_trashduplicates'] == '1' ? ' checked="checked"' : '' ).' style="width:auto;" /> When transferring files to Google Drive through the File Browser, trash any files in destination that are found to have the same filename as the transferred files.';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<p style="cursor: text; font-size:9px; padding: 0px; margin: 0px; width: 95%; color: #AAAAAA;">Current value: <strong>'.($plugin_options['gdrive_trashduplicates'] == '1' ? 'Yes' : 'No' ).'</strong></p>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';

	return $echo_str;
}

function wfu_gdrive_update_settings($arg, $plugin_options) {
	$gdrive_includeuserdata = ( isset($_POST['wfu_gdrive_includeuserdata']) ? ( $_POST['wfu_gdrive_includeuserdata'] == "on" ? 1 : 0 ) : 0 );
	$gdrive_trashduplicates = ( isset($_POST['wfu_gdrive_trashduplicates']) ? ( $_POST['wfu_gdrive_trashduplicates'] == "on" ? 1 : 0 ) : 0 );
	if ( !isset($_POST['wfu_gdrive_defaultpath']) ) $arg["pass"] = false;
	elseif ( $arg["pass"] ) {
		$arg["new_plugin_options"]['gdrive_accesstoken'] = $plugin_options['gdrive_accesstoken'];
		$arg["new_plugin_options"]['gdrive_defaultpath'] = sanitize_text_field($_POST['wfu_gdrive_defaultpath']);
		$arg["new_plugin_options"]['gdrive_includeuserdata'] = $gdrive_includeuserdata;
		$arg["new_plugin_options"]['gdrive_trashduplicates'] = $gdrive_trashduplicates;
	}
	
	return $arg;
}