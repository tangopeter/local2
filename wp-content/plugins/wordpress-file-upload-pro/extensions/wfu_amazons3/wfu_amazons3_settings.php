<?php

add_filter('_wfu_settings_definitions', 'wfu_amazons3_settings_definitions', 10, 1);
add_filter('_wfu_manage_settings_bottom', 'wfu_amazons3_manage_settings', 10, 1);
add_filter('_wfu_update_settings', 'wfu_amazons3_update_settings', 10, 2);

function wfu_amazons3_settings_definitions($settings) {
	$settings += array(
		"amazons3_publickey" => array("string", ""),
		"amazons3_privatekey" => array("string", "")
	);
	
	return $settings;
}

function wfu_amazons3_manage_settings($echo_str) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<h3>Amazon S3 Settings</h3>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Amazon S3 Uploads</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$php_version = preg_replace("/-.*/", "", phpversion());
	$min_version = ( "5.5.0" );
	$ret = wfu_compare_versions($php_version, $min_version);
	$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
	if ( $unsupported ) $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold;">Not supported!</label><label style="margin-left:6px;">Your PHP version does not support the Amazon S3 API. If you want to enable Amazon S3 uploads, you need to install a PHP version newer than '.$min_version.'.</label>';
	else {
		$amazons3_nonce = wp_create_nonce( "wfu-amazons3-authorize-app" );
		if ( $plugin_options['amazons3_publickey'] == "" ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<div class="wfu-amazons3-activation">';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<label class="not-activated">Not activated!</label>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<button class="press-here" type="button" onclick="wfu_amazons3_authorize_app_start(\''.$amazons3_nonce.'\');">Press here</button>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<label class="press-here-text">to enable Amazon S3 uploads.</label>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<div class="wfu-amazons3-keys">';
			$prompt = 'Enter your Amazon IAM credentials below and then press **Finish** button. Read this ::article:: for instructions how to get them.';
			$prompt = preg_replace_callback("/\*\*[^*]*\*\*/", function ($m) { return '<strong>'.substr($m[0], 2, -2).'</strong>'; }, $prompt);
			$prompt = preg_replace_callback("/::[^:]*::/", function ($m) { return '<a href="https://www.iptanus.com/how-to-configure-amazon-s3-for-wordpress-file-upload-plugin/" target="_blank">'.substr($m[0], 2, -2).'</a>'; }, $prompt);
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<p><label class="activation-text">'.$prompt.'</label></p>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<p><label class="key-label">Key ID</label>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<input type="text" class="publickey" /></p>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<p><label class="key-label">Secret</label>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<input type="text" class="privatekey" /></p>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<p><button type="button" onclick="wfu_amazons3_authorize_app_finish(\''.$amazons3_nonce.'\');">Finish</button><label class="error-message">This is an error</label></p>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'</div>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
		}
		else $echo_str .= "\n\t\t\t\t\t\t\t".'<label style="font-weight:bold; color:green;">Activated!</label><label style="margin-left:6px;">To reset Amazon S3 activation</label><button type="button" style="margin-left:6px;" onclick="wfu_amazons3_authorize_app_reset(\''.$amazons3_nonce.'\');">press here</button>';
	}
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';

	return $echo_str;
}

function wfu_amazons3_update_settings($arg, $plugin_options) {
	if ( $arg["pass"] ) {
		$arg["new_plugin_options"]['amazons3_publickey'] = $plugin_options['amazons3_publickey'];
		$arg["new_plugin_options"]['amazons3_privatekey'] = $plugin_options['amazons3_privatekey'];
	}
	
	return $arg;
}