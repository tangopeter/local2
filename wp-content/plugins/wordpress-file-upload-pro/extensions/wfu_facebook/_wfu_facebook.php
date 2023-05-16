<?php

require_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;

function wfu_facebook_authorize_app_start() {
	WFU_USVAR_store('wfu_Facebook_UID', "");
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$uid = wfu_create_random_string(8);
	$timeout = WFU_VAR("WFU_FACEBOOK_REGISTRATION_TIMEOUT");
	$postfields = array(
		'action' => 'wfuca_facebook_set_uid',
		'version_hash' => WFU_VERSION_HASH,
		'caller_uid' => $uid,
		'expires' => $timeout
	);
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_FACEBOOK_SERVER_URL );
	$result = null;
	$result = wfu_post_request($url, $postfields, false, false, 10);
	if ( strpos($result, "wfuca_facebook_set_uid:success")  !== false ) {
		WFU_USVAR_store('wfu_Facebook_UID', $uid);
		WFU_USVAR_store('wfu_Facebook_UID_expires', time() + (int)$timeout);
		$subscribeUrl = WFU_FACEBOOK_REDIRECTURL.'?uid='.$uid.'&timeout='.$timeout;
		die("wfu_facebook_authorize_app_start:success:".wfu_plugin_encode_string($subscribeUrl));
	}
	else die();
}

function wfu_facebook_authorize_app_finish() {
	if ( WFU_USVAR('wfu_Facebook_UID') == "" ) die();
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$identifier = "wfuca_facebook_get_psid";
	$postfields = array(
		'action' => $identifier,
		'version_hash' => WFU_VERSION_HASH,
		'caller_uid' => WFU_USVAR('wfu_Facebook_UID'),
		'timeout' => WFU_VAR("WFU_FACEBOOK_PSIDREQUEST_TIMEOUT")
	);
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_FACEBOOK_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_FACEBOOK_SERVER_URL );
	$expires = WFU_USVAR('wfu_Facebook_UID_expires');
	$expired = false;
	$psid = "";
	$pageaccesstoken = "";
	while ( time() <= $expires ) {
		$response = wfu_post_request($url, $postfields, false, false, (int)WFU_VAR("WFU_FACEBOOK_PSIDREQUEST_TIMEOUT") + 10);
		$pos = strpos($response, $identifier);
		if ( $pos === false ) break;
		$result = json_decode(substr($response, strlen($identifier) + 1), true);
		if ( $result == null ) break;
		if ( $result["status"] == "timeout" ) sleep(1);
		else {
			if ( $result["status"] == "success" ) {
				$psid = $result["psid"];
				$pageaccesstoken = $result["pageaccesstoken"];
			}
			break;
		}
	}
	if ( $psid == "" || $pageaccesstoken == "" ) die("wfu_facebook_authorize_app_finish:failed:");
	wfu_update_setting('facebook_userpsid', $psid);
	wfu_update_setting('facebook_pageaccesstoken', $pageaccesstoken);
	
	wfu_facebook_send_message('You have successfully subscribed to Wordpress File Upload plugin Messenger service. Now you can receive notifications about new uploaded files to your Messenger.');
	die("wfu_facebook_authorize_app_finish:success:");
}