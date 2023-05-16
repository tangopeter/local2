<?php

/**
 * Captcha Elements of the Plugin
 *
 * This file contains functions related to captcha elements of the plugin.
 *
 * @link /lib/wfu_captcha.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 2.1.2
 */
add_filter("_wfu_before_upload", "wfu_captcha_before_upload_handler", 10, 2);
add_filter("_wfu_after_upload", "wfu_captcha_after_upload_handler", 10, 3);
add_filter("_wfu_pre_upload_check", "wfu_captcha_pre_upload_check_handler", 10, 2);

/**
 * Verify Captcha Before Upload
 *
 * This function is executed in case that captcha has been added in upload form
 * and verifies the captcha. If captcha cannot be verified, then the upload will
 * be rejected.
 *
 * @since 3.7.0
 *
 * @see _wfu_before_upload filter in lib/wfu_ajaxactions.php For more
 *      information on $ret and $attr array formats.
 *
 * @param array $ret First input param that must be processed and returned by
 *        the function. It controls the upload.
 * @param array $attr Additional information about the upload.
 *
 * @return array An array that controls whether the upload will continue or not.
 */
function wfu_captcha_before_upload_handler($ret, $attr) {
	if ( $ret["status"] == "die" ) return $ret;
	if ( !isset($_REQUEST['captcha_challenge']) ) return $ret;
	if ( !isset($_REQUEST['captcha_input']) ) {
		$ret["status"] = "die";
		return $ret;
	}
	$captcha_challenge = sanitize_text_field( $_REQUEST["captcha_challenge"] );
	$captcha_input = sanitize_text_field( $_REQUEST["captcha_input"] );
	//if captcha_challenge or captcha_input is empty, this means that client 
	//parameters did not pass correctly to the server, due to a connection
	//problem or hack attempt; in this case the whole operation is aborted
	//but an error message is returned
	if ( $captcha_challenge == "" ) {
			$ret["status"] = "error";
			$ret["echo"] .= "captcha[nochallenge]";
	}
	elseif ( $captcha_input == "" ) {
			$ret["status"] = "error";
			$ret["echo"] .= "captcha[noinput]";
	}
	else {
		//if a previous ask_server filter has generated an error, there is no
		//need to check the captcha but we only need to generate and return a
		//new captcha_challenge
		if ( $ret["status"] == "error" ) $ret["echo"] .= "captcha[noprocess]";
		else {
			$result = wfu_check_captcha($captcha_challenge, $captcha_input);
			if ( $result == "success:" ) {
				//generate unique combination of upload id and captcha challenge
				//and store it in session; it will be used to approve or reject
				//the uploaded files that follow
				WFU_USVAR_store("wfu_approvedcaptcha_".$attr["unique_id"], $captcha_input);
				$ret["status"] = "success";
			}
			else {
				$ret["status"] = "error";
				$ret["echo"] .= "captcha[".preg_replace("/^error:/", "", $result)."]";
			}
		}
	}
	return $ret;
}

/**
 * Captcha-Related Actions After Upload
 *
 * This function runs after an upload has finished and cleans any approved
 * captcha stored variables.
 *
 * @since 3.7.0
 *
 * @see _wfu_after_upload filter in wfu_loader.php For more information on $ret
 *      and $attr array formats.
 *
 * @param array $ret First input param that must be processed and returned by
 *        the function. It contains custom output to return to the plugin.
 * @param array $attr Various attributes of the upload.
 * @param array $params The shortcode attributes of the upload form.
 *
 * @return array An array that contains custom output to return to the plugin.
 */
function wfu_captcha_after_upload_handler($ret, $attr, $params) {
	$unique_id = $attr["unique_id"];
	//destroy approved captcha stored variables so that they cannot be reused
	if ( WFU_USVAR_exists("wfu_approvedcaptcha_".$unique_id) ) WFU_USVAR_unset("wfu_approvedcaptcha_".$unique_id);	
	return $ret;
}

/**
 * Check Captcha on Every Upload Request
 *
 * This function runs every time an upload request of a file or a file chunk is
 * sent to the web server in order to check that the captcha has been resolved.
 *
 * @since 3.7.0
 *
 * @see _wfu_pre_upload_check filter in lib/wfu_ajaxactions.php For more
 *      information on $ret and $attr array formats.
 *
 * @param array $ret First input param that must be processed and returned by
 *        the function. It controls the upload.
 * @param array $attr Various attributes of the upload.
 *
 * @return array An array that controls whether the upload will continue or not.
 */
function wfu_captcha_pre_upload_check_handler($ret, $attr) {
	if ( $ret["status"] == "die" ) return $ret;
	$unique_id = $attr["unique_id"];
	$params = $attr["params"];
	//check if captcha passed
	if ( $params["captcha"] == "true" ) {
		if ( !isset($_POST["captcha_verify"]) ) $ret["status"] = "die";
		$captcha_verify = sanitize_text_field( $_POST["captcha_verify"] );
		if ( $captcha_verify == "" ) $ret["status"] = "die";

		if ( !WFU_USVAR_exists("wfu_approvedcaptcha_".$unique_id) || WFU_USVAR("wfu_approvedcaptcha_".$unique_id) != $captcha_verify ) {
			$ret["status"] = "die";
			$ret["echo"] = "force_errorabort_code";
		}
	}
	return $ret;
}

/**
 * Get Captcha Generation Ticket
 *
 * This function runs when 'RecaptchaV1 (no account)' or 'RecaptchaV2 (no
 * account)' captchas have been added in upload form. It notifies Iptanus
 * Services Server that a 'no account' captcha is going to be requested shortly.
 * Iptanus Services Server will return a short-life ticket that can be used to
 * generate the captcha when the page loads. This process ensures that Iptanus
 * Services Server will serve only requests coming from a Wordpress website
 * using the plugin. The ticket has a very short life, so it is also ensured
 * that a hacker cannot use it again to render the captcha.
 *
 * @since 2.7.0
 *
 * @param integer $sid The ID of the shortcode.
 * @param string $mode The Recaptcha version. It can be 'V1' or 'V2'.
 *
 * @return string A string notifying that ticket retrieval was successful,
 *         including also the ticket.
 */
function wfu_get_captcha_ticket($sid, $mode) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$postfields = array();
	$postfields['action'] = 'wfuca_get_captcha_ticket';
	$postfields['version_hash'] = WFU_VERSION_HASH;
	$postfields['caller_uri'] = site_url();
	$postfields['sid'] = $sid;
	$postfields['mode'] = $mode;
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_CAPTCHA_SERVER_URL );
	$result = wfu_post_request($url, $postfields, false);
	$result = preg_replace("/.*wfuca_captcha_ticket:/", "", $result);
	return "wfu_captcha_success:".$result;
}

/**
 * Get Captcha Generation URL
 *
 * This function runs when 'RecaptchaV1 (no account)' or 'RecaptchaV2 (no
 * account)' captchas have been added in upload form. It returns a URL that can
 * be used by an iframe to generate the captcha. The captcha will only be
 * generated if the ticket provided is valid.
 *
 * @since 2.7.0
 *
 * @param string $code The captcha generation ticket.
 *
 * @return string A URL that can be used by an iframe to generate the captcha.
 */
function wfu_get_captcha_request_code_url($code) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_CAPTCHA_SERVER_URL );
	return $url.'?action=wfuca_get_captcha_code&wfu_captcha_code='.$code;
}

/**
 * Captcha Verification
 *
 * This function verifies the captcha. If it is verified the upload continues.
 * If not the upload is cancelled.
 *
 * @since 2.1.2
 *
 * @param string $captcha_challenge The captcha challenge. Its value depends on
 *        the captcha type. It should be like this:
 *          - for 'RecaptchaV2': 'RecaptchaV2'.
 *          - for 'RecaptchaV1': the captcha challenge plus '[V1]'.
 *          - for 'RecaptchaV2 (no account)': 'RecaptchaV2 (no account)'.
 *          - for 'RecaptchaV1 (no account)': the captcha challenge.
 * @param string $captcha_input The captcha verification code generated when
 *        captcha is resolved.
 *
 * @return string The result of the verification. If it is successful,
 *         'success:' should be returned. If it failed, 'error:' should be
 *         returned including also any error messages.
 */
function wfu_check_captcha($captcha_challenge, $captcha_input) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $captcha_challenge == "RecaptchaV2" ) {
		$php_version = preg_replace("/-.*/", "", phpversion());
		$unsupported = false;
		$ret = wfu_compare_versions($php_version, '5.3.0');
		$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );

		if ( !$unsupported ) {
			$path = ABSWPFILEUPLOAD_DIR;
			include_once $path.'vendor/recaptchav2/ReCaptcha.php';
			include_once $path.'vendor/recaptchav2/RequestMethod.php';
			include_once $path.'vendor/recaptchav2/RequestParameters.php';
			include_once $path.'vendor/recaptchav2/Response.php';
			include_once $path.'vendor/recaptchav2/RequestMethod/Socket.php';
			include_once $path.'extensions/wfu_captcha/base_classes/WFUPost.php';
			include_once $path.'extensions/wfu_captcha/base_classes/WFUSocketPost.php';
			
			$requestmethod = "";
			if ( isset($plugin_options['postmethod']) ) {
				if ( $plugin_options['postmethod'] == 'socket' ) $requestmethod = ', new \ReCaptcha\RequestMethod\WFUSocketPost()';
//				elseif ( $plugin_options['postmethod'] == 'curl' ) $requestmethod = ', new \ReCaptcha\RequestMethod\Curl()';
				else $requestmethod = ', new \ReCaptcha\RequestMethod\WFUPost()';
			}
			eval('$recaptcha = new \ReCaptcha\ReCaptcha($plugin_options[\'captcha_secretkey\']'.$requestmethod.');');
			$resp = $recaptcha->verify($captcha_input, $_SERVER["REMOTE_ADDR"]);
			if ($resp->isSuccess()) {
				$result = "success:";
			}
			else {
				$errors = $resp->getErrorCodes();
				$result = "error:(".implode("|", $errors).")";
			}
		}
		else $result = "error:(oldphp)";
	}
	elseif ( substr($captcha_challenge, 0, 4) == "[V1]" ) {
		$path = ABSWPFILEUPLOAD_DIR;
		include_once $path.'vendor/recaptchav1/recaptchalib.php';
		$captcha_challenge = substr($captcha_challenge, 4);
		$resp = recaptcha_check_answer($plugin_options['captcha_secretkey'], $_SERVER["REMOTE_ADDR"], $captcha_challenge, $captcha_input);
		if ( !$resp->is_valid ) {
			$result = "error:wrongcaptcha";
		}
		else {
			$result = "success:";
		}
	}
	else {
		$postfields = array();
		$postfields['action'] = 'wfuca_check_captcha_answer';
		$postfields['captcha_challenge'] = $captcha_challenge;
		$postfields['captcha_input'] = $captcha_input;
		$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_CAPTCHA_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_CAPTCHA_SERVER_URL );
		$result = wfu_post_request($url, $postfields, false);
	}
	
	return $result;
}

/**
 * Parse Captcha Options
 *
 * This function parses Captcha Options attribute of the uploader shortcode into
 * an associative array.
 *
 * @since 3.9.6
 *
 * @param string $options The Captcha Options attribute of the shortcode.
 *
 * @return array An associative array of parsed captcha options.
 */
function wfu_parse_captcha_options($options) {
	$options = str_replace(array( "%dq%", "%brl%", "%brr%" ), array( '"', "[", "]" ), $options);
	$arr_raw = explode(",", $options);
	$options_array = array();
	foreach ( $arr_raw as $raw ) {
		$parts = explode("=", trim($raw));
		if ( count($parts) == 2 && trim($parts[0]) != "" ) {
			$option = trim($parts[0]);
			$val = trim($parts[1]);
			if ( ( substr($val, 0, 1) == '"' && substr($val, -1) == '"' ) || ( substr($val, 0, 1) == "'" && substr($val, -1) == "'" ) )
				$val = substr($val, 1, strlen($val) - 2);
			$options_array[$option] = wfu_sanitize_code($val);
		}
	}
	return $options_array;
}