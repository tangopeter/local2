<?php

require_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
use Microsoft\Graph\Graph;

function wfu_onedrive_authorize_app_start() {
	wfu_tf_LOG("onedrive_authorize_app_start_start");

	$args = array(
		'client_id'		=> WFU_ONEDRIVE_CLIENTIDENTIFIER,
		'scope'			=> ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? 'onedrive' : 'files' ).'.readwrite+offline_access',
		'response_type'	=> 'code',
		'response_mode'	=> 'form_post',
		'redirect_uri'	=> urlencode(WFU_ONEDRIVE_REDIRECTURI)
	);
	$authorizeUrl = ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? WFU_ONEDRIVE_AUTHORIZEURI_OENDP : WFU_ONEDRIVE_AUTHORIZEURI_GENDP ).'?'.wfu_array_to_GET_params($args);
	
	wfu_tf_LOG("onedrive_authorize_app_start_end:".$authorizeUrl);
	die("wfu_onedrive_authorize_app_start:success:".wfu_plugin_encode_string($authorizeUrl));
}

function wfu_onedrive_authorize_app_finish($authCode) {
	wfu_tf_LOG("onedrive_authorize_app_finish_start");

	$accessToken = wfu_onedrive_update_accessToken($authCode);
	if ( isset($accessToken['error']) ) {
		wfu_tf_LOG("onedrive_authorize_app_finish_end:error:".$accessToken['error']);
		die("wfu_onedrive_authorize_app_finish:error:".$accessToken['error']);
	}

	wfu_tf_LOG("onedrive_authorize_app_finish_end:success:");
	die("wfu_onedrive_authorize_app_finish:success:");
}

function wfu_onedrive_upload_file($filepath, $destination, $params) {
	wfu_tf_LOG("onedrive_transfer_file_start:".$filepath);
	
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	//get service
	$Graph = wfu_onedrive_getGraph();
	if ( $Graph == null ) {
		wfu_tf_LOG("onedrive_transfer_file_end:service_fail");
		wfu_set_transfer_result($fileid, $jobid, "onedrive", false, 'service_fail', '');
		return false;
		
	}
	wfu_tf_LOG("onedrive_transfer_file_gservice_ok");
	//add leading and trailing slashes in destination if they do not exist
	if ( substr($destination, 0, 1) != '/' ) $destination = '/'.$destination;
	if ( substr($destination, -1) != '/' ) $destination .= '/';
	$destfile = $destination.wfu_basename($filepath);
	$params["destfile"] = $destfile;

	//allow chunked uploads for OneDrive endp when file size is over threshold
	//allow chunked uploads always for Graph endp, because it does not support
	//multipart uploads
	if ( wfu_filesize($filepath, "wfu_onedrive_upload_file") > WFU_VAR("WFU_ONEDRIVE_CHUNKED_UPLOAD_THRESHOLD") || WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "Graph" ) {

		try {
			wfu_onedrive_chunked_upload_file($filepath, $destination, $params, $Graph);
		}
		catch (Exception $ex) {
			wfu_tf_LOG("onedrive_transfer_file_end:upload_fail:".wfu_onedrive_process_uploadexception($ex, $params));
			return false;
		}
	}
	else {
		try {
			$response = wfu_onedrive_multipart_upload_file($filepath, $destination, $params, $Graph);
		}
		catch (Exception $ex) {
			wfu_tf_LOG("onedrive_transfer_file_end:upload_fail:".wfu_onedrive_process_uploadexception($ex, $params));
			return false;
		}
		$new_filepath = $filepath;
		if ( is_array($response) && isset($response['name']) && $response['name'] != "" )
			$new_filepath = wfu_basedir($filepath).$response['name'];
		$metadata = wfu_onedrive_post_upload_actions($response, $params, $Graph);
		wfu_set_transfer_result($fileid, $jobid, "onedrive", true, "", $new_filepath, $metadata);
	}

	wfu_tf_LOG("onedrive_transfer_file_end");
	return false;
}

function wfu_onedrive_process_uploadexception($exception, $params) {
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	$errorCode = $exception->getCode();
	$reterror = "";
	//if file failed because another one already exists at destination then we
	//need to abort the transfer by passing true in wfu_set_transfer_result
	if (  $params["conflict_policy"] == 'fail' && $errorCode == 409 ) {
		wfu_set_transfer_result($fileid, $jobid, "onedrive", true, "failed:conflict", "");
		$reterror = "aborted due to conflict";
	}
	else {
		wfu_set_transfer_result($fileid, $jobid, "onedrive", false, $exception->getMessage(), "");
		$reterror = print_r($exception, true);
	}
}

function wfu_onedrive_update_accessToken($code, $from_refresh_token = false) {
	wfu_tf_LOG("onedrive_update_accessToken_start");
	
	$secret = wfu_get_onedrive_secret();
	if ( $secret == false ) {
		wfu_tf_LOG("onedrive_update_accessToken_end:error:invalid_secret");
		return array( 'error' => 'invalid_secret' );
	}

	$http_client_config = array();
	//include proxy support
	wfu_add_proxy_param($http_client_config);
	$client = new \GuzzleHttp\Client($http_client_config);
	$params = array(
		'form_params' => array(
			'client_id' => WFU_ONEDRIVE_CLIENTIDENTIFIER,
			'client_secret' => $secret,
			( $from_refresh_token ? 'refresh_token' : 'code' ) => $code,
			'grant_type' => ( $from_refresh_token ? 'refresh_token' : 'authorization_code' ),
			'redirect_uri' => WFU_ONEDRIVE_REDIRECTURI
		)
	);
	$response = array();
	$t0 = time();
	try {
		$rawresponse = $client->request('POST', ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? WFU_ONEDRIVE_TOKENURI_OENDP : WFU_ONEDRIVE_TOKENURI_GENDP ), $params);
		$body = (string)$rawresponse->getBody();
		$response = json_decode($body, true);
	}
	catch (Exception $ex) {
		wfu_tf_LOG("onedrive_update_accessToken_end:error:".$ex->getMessage());
		return array( 'error' => $ex->getMessage() );
	}

	if ( !isset($response['expires_in']) || !isset($response['access_token']) || !isset($response['refresh_token']) || (int)$response['expires_in'] <= 0 || $response['access_token'] == "" || $response['refresh_token'] == "" ) {
		wfu_tf_LOG("onedrive_update_accessToken_end:error:invalid_token_response");
		return array( 'error' => 'invalid_token_response' );
	}
	$accessToken = array(
		'expires_on'	=> $t0 + (int)$response['expires_in'],
		'access_token'	=> $response['access_token'],
		'refresh_token'	=> $response['refresh_token']
	);
	
	wfu_update_setting('onedrive_accesstoken', json_encode($accessToken));

	wfu_tf_LOG("onedrive_update_accessToken_end:success:");
	return $accessToken;
}

function wfu_onedrive_get_accessToken() {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options['onedrive_accesstoken'] == "" ) return "";
	$accessToken = json_decode($plugin_options['onedrive_accesstoken'], true);
	if ( time() < $accessToken['expires_on'] ) return $accessToken['access_token'];
	else {
		$accessToken = wfu_onedrive_update_accessToken($accessToken['refresh_token'], true);
		if ( isset($accessToken['error']) ) return "";
		else return $accessToken['access_token'];
	}
}

function wfu_onedrive_getGraph() {
	$accessToken = wfu_onedrive_get_accessToken();
	if ( $accessToken == "" ) return null;
	
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/base_classes/WFUGraph.php'; 
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/base_classes/WFUGraphRequest.php'; 
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/base_classes/WFUGraphSessionFile.php'; 
	$Graph = new WFU\WFUGraph();
	$Graph->setAccessToken($accessToken);
	
	return $Graph;
}

function wfu_get_onedrive_secret() {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$postfields = array();
	$postfields['action'] = 'wfuca_get_onedrive_secret';
	$postfields['version_hash'] = WFU_VERSION_HASH;
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_ONEDRIVE_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_ONEDRIVE_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_ONEDRIVE_SERVER_URL );
	$result = wfu_post_request($url, $postfields, false);
	$matches = array();
	if ( preg_match("/wfuca_onedrive_secret:(.*)$/", $result, $matches) != 1 ) return false;
	if ( !isset($matches[1]) || $matches[1] == "" ) return false;
	return wfu_plugin_decode_string($matches[1]);
}