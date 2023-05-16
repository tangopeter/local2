<?php

add_filter('_wfu_settings_definitions', 'wfu_facebook_settings_definitions', 10, 1);
add_filter('_wfu_manage_settings_bottom', 'wfu_facebook_manage_settings', 10, 1);
add_filter('_wfu_update_settings', 'wfu_facebook_update_settings', 10, 2);

function wfu_facebook_settings_definitions($settings) {
	$settings += array(
		"facebook_userpsid" => array("string", ""),
		"facebook_pageaccesstoken" => array("string", "")
	);
	
	return $settings;
}

function wfu_facebook_manage_settings($echo_str) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>Facebook Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Messenger Notifications</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = "5.6.3";
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold;">Not supported!</label><label style="margin-left:6px;">Your PHP version does not support the Facebook Messenger API. If you want to enable Facebook messaging, you need to install a PHP version newer than '.$min_version.'.</label>';
	else {
		$facebook_nonce = wp_create_nonce( "wfu-facebook-authorize-app" );
		if ( $plugin_options['facebook_userpsid'] == "" || $plugin_options['facebook_pageaccesstoken'] == "" ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:darkred;">Not activated!</label><button type="button" style="margin-left:6px;" onclick="wfu_facebook_authorize_app_start(\''.$facebook_nonce.'\');">Press here</button><label style="margin-left:6px;">to enable Facebook messaging. A new window will show up with a request to subscribe to Wordpress File Upload Plugin Messenger. If you are not already logged in your Facebook account, login will also be requested. Press <strong>Subscribe in Messenger</strong> button to subscribe and then wait a few seconds until the window closes.</label>';
		else $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:green;">Activated!</label><label style="margin-left:6px;">To reset Messenger activation</label><button type="button" style="margin-left:6px;" onclick="wfu_facebook_authorize_app_reset(\''.$facebook_nonce.'\');">press here</button>';
	}
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';

	return $echo_str;
}

function wfu_facebook_update_settings($arg, $plugin_options) {
	if ( $arg["pass"] ) {
		$arg["new_plugin_options"]['facebook_userpsid'] = $plugin_options['facebook_userpsid'];
		$arg["new_plugin_options"]['facebook_pageaccesstoken'] = $plugin_options['facebook_pageaccesstoken'];
	}
	
	return $arg;
}