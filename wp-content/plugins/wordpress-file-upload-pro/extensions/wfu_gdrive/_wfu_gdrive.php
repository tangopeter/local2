<?php

require_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;

function wfu_gdrive_authorize_app_start() {
	WFU_USVAR_store('wfu_GDrive_Client', null);

	$state = wfu_create_random_string(16);
	$GClient = wfu_gdrive_getGClient_basic($state);
	$authorizeUrl = $GClient->createAuthUrl();

	//WFU_USVAR_store('wfu_GDrive_Client', serialize($GClient));
	die("wfu_gdrive_authorize_app_start:success:".wfu_plugin_encode_string($authorizeUrl));
}

function wfu_gdrive_authorize_app_finish($state, $authCode) {
	//if ( WFU_USVAR('wfu_GDrive_Client') == null ) die();
	
	//$GClient = unserialize(WFU_USVAR('wfu_GDrive_Client'));
	$GClient = wfu_gdrive_getGClient_basic($state);
	wfu_gdrive_add_proxy_support($GClient);
	$authCode = trim($authCode);
	$accessToken = $GClient->fetchAccessTokenWithAuthCode($authCode);

	wfu_update_setting('gdrive_accesstoken', json_encode($accessToken));
	die("wfu_gdrive_authorize_app_finish:success:");
}

function wfu_gdrive_upload_file($filepath, $destination, $params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	//get service
	try {
		$GClient = wfu_gdrive_getGClient();
		$GService = new Google_Service_Drive($GClient);
	}
	catch (Exception $ex) {
		wfu_tf_LOG("gdrive_transfer_file_end:service_fail");
		wfu_set_transfer_result($fileid, $jobid, "gdrive", false, $ex->getMessage(), "");
		return false;
	}
	wfu_tf_LOG("gdrive_transfer_file_gservice_ok");
	//locate destination folder id
	try {
		$folderid = wfu_locate_destination_id($destination, $GService);
	}
	catch (Exception $ex) {
		wfu_tf_LOG("gdrive_transfer_file_end:destination_fail");
		wfu_set_transfer_result($fileid, $jobid, "gdrive", false, $ex->getMessage(), "");
		return false;
	}
	wfu_tf_LOG("gdrive_transfer_file_destination_ok:".$folderid);
	//trash duplicates if this option is enabled
	if ( isset($params["trash_duplicates"]) ) {
		try {
			wfu_gdrive_trash_duplicates(wfu_basename($filepath), $folderid, wfu_mime_content_type($filepath), $GService);
		}
		catch (Exception $ex) {
			wfu_tf_LOG("gdrive_transfer_file_end:duplicates_trashing_fail");
			wfu_set_transfer_result($fileid, $jobid, "gdrive", false, $ex->getMessage(), "");
			return false;
		}
		//remove trash duplicates flag from params
		unset($params["trash_duplicates"]);
	}
	//add leading and trailing slashes in destination if they do not exist
	if ( substr($destination, 0, 1) != '/' ) $destination = '/'.$destination;
	if ( substr($destination, -1) != '/' ) $destination .= '/';
	$destfile = $destination.wfu_basename($filepath);
	$params["destfile"] = $destfile;
	
	if ( wfu_filesize($filepath, "wfu_gdrive_upload_file") > WFU_VAR("WFU_GDRIVE_CHUNKED_UPLOAD_THRESHOLD") ) {

		$GClient->setDefer(true);		
		try {
			wfu_gdrive_chunked_upload_file($filepath, $folderid, $params, $GService);
		}
		catch (Exception $ex) {
			wfu_tf_LOG("gdrive_transfer_file_end:upload_fail");
			wfu_set_transfer_result($fileid, $jobid, "gdrive", false, $ex->getMessage(), "");
			return false;
		}
	}
	else {
		$metadata = null;
		try {
			$metadata = wfu_gdrive_simple_upload_file($filepath, $folderid, $params, $GService);
		}
		catch (Exception $ex) {
			wfu_tf_LOG("gdrive_transfer_file_end:upload_fail");
			wfu_set_transfer_result($fileid, $jobid, "gdrive", false, $ex->getMessage(), "");
			return false;
		}
		wfu_set_transfer_result($fileid, $jobid, "gdrive", true, "", $filepath, $metadata);
	}
	
	wfu_tf_LOG("gdrive_transfer_file_end");
	return false;
}

function wfu_gdrive_getGClient_basic($state) {
	$secret = wfu_get_gdrive_secret($state);
	if ( $secret === false ) return null;
	$config = json_decode($secret, true);

	$GClient = null;
	$GClient = new Google_Client();
	$GClient->setApplicationName('Google Drive API PHP Quickstart');
	$GClient->setScopes(Google_Service_Drive::DRIVE);
	$GClient->setAuthConfig($config);
	$GClient->setState($state);
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_GDRIVE_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_GDRIVE_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_GDRIVE_SERVER_URL );
	$GClient->setRedirectUri($url.'?action=wfuca_google_oauth2callback');
	$GClient->setAccessType('offline');
	$GClient->setApprovalPrompt('force');
	
	return $GClient;
}

function wfu_gdrive_add_proxy_support(&$GClient) {
	//include proxy support
	$http_client = $GClient->getHttpClient();
	$http_client_config = $http_client->getConfig();
	if ( wfu_add_proxy_param($http_client_config) ) {
		$http_client = new GuzzleHttp\Client($http_client_config);
		$GClient->setHttpClient($http_client);
	}
}

function wfu_gdrive_getGClient() {
	$state = wfu_create_random_string(16);
	$GClient = wfu_gdrive_getGClient_basic($state);
	wfu_gdrive_add_proxy_support($GClient);
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$accessToken = json_decode($plugin_options['gdrive_accesstoken'], true);
	$GClient->setAccessToken($accessToken);
	if ($GClient->isAccessTokenExpired()) {
		$GClient->fetchAccessTokenWithRefreshToken($GClient->getRefreshToken());
		wfu_update_setting('gdrive_accesstoken', json_encode($GClient->getAccessToken()));
	}
	
	return $GClient;
}

function wfu_get_gdrive_secret($state) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$postfields = array();
	$postfields['action'] = 'wfuca_get_gdrive_secret_new';
	$postfields['version_hash'] = WFU_VERSION_HASH;
	$postfields['state'] = $state;
	$postfields['redirect_url'] = wfu_ajaxurl()."?action=wfu_google_oauth2callback";
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_GDRIVE_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_GDRIVE_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_GDRIVE_SERVER_URL );
	$result = wfu_post_request($url, $postfields, false);
	$matches = array();
	if ( preg_match("/wfuca_gdrive_secret:(.*)$/", $result, $matches) != 1 ) return false;
	if ( !isset($matches[1]) || $matches[1] == "" ) return false;
	return wfu_plugin_decode_string($matches[1]);
}