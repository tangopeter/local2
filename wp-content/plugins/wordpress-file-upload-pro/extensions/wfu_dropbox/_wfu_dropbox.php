<?php

//require_once ABSWPFILEUPLOAD_DIR.'vendor/dropbox/autoload.php';
require_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
//use \Dropbox as dbx;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

function wfu_dropbox_get_Dropbox($app) {
	include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/base_classes/WFUDropbox.php'; 
	$config = array();
	$http_client_config = array();
	//include proxy support
	if ( wfu_add_proxy_param($http_client_config) ) $config['http_client_handler'] = new GuzzleHttp\Client($http_client_config);
	return new WFU\WFUDropbox($app, $config);
}

function wfu_dropbox_authorize_app_start($use_old_API = false) {
	WFU_USVAR_store('wfu_Dropbox_WebAuth', null);
	/*if ( $use_old_API ) {
		try {
			$WebAuth = wfu_dropbox_getWebAuth($use_old_API);
			$authorizeUrl = $WebAuth->start();
		}
		catch (dbx\Exception $ex) {
			die("wfu_dropbox_authorize_app_start:error:".$ex->getMessage());
		}
	}
	else */{
		$WebAuth = wfu_dropbox_getWebAuth($use_old_API);
		$dropbox = wfu_dropbox_get_Dropbox($WebAuth);
		$AuthHelper = $dropbox->getAuthHelper();
		$authorizeUrl = $AuthHelper->getAuthUrl();
	}
	WFU_USVAR_store('wfu_Dropbox_WebAuth', serialize($WebAuth));
	die("wfu_dropbox_authorize_app_start:success:".wfu_plugin_encode_string($authorizeUrl));
}

function wfu_dropbox_authorize_app_finish($authCode, $use_old_API = false) {
	if ( WFU_USVAR('wfu_Dropbox_WebAuth') == null ) die();
	
	$WebAuth = unserialize(WFU_USVAR('wfu_Dropbox_WebAuth'));
	$authCode = \trim($authCode);
	/*if ( $use_old_API ) {
		try {
			list($accessToken, $dropboxUserId) = $WebAuth->finish($authCode);
		}
		catch (dbx\Exception $ex) {
			die("wfu_dropbox_authorize_app_finish:error:".$ex->getMessage());
		}
	}
	else */{
		$dropbox = wfu_dropbox_get_Dropbox($WebAuth);
		$AuthHelper = $dropbox->getAuthHelper();
		$accessTokenObj = $AuthHelper->getAccessToken($authCode);
		$accessToken = $accessTokenObj->getToken();
	}
	wfu_update_setting('dropbox_accesstoken', $accessToken);
	die("wfu_dropbox_authorize_app_finish:success:");
}

function wfu_dropbox_upload_file($filepath, $destination, $use_old_API = false, $params) {
	wfu_tf_LOG("dropbox_transfer_file_start:".$filepath);
	//adjust leading and trailing slashes of destination, we just want trailing slash
	if ( substr($destination, 0, 1) != '/' ) $destination = '/'.$destination;
	if ( substr($destination, -1) == '/' ) $destination = substr($destination, 0, -1);
	//get access token
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$accessToken = $plugin_options['dropbox_accesstoken'];
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	/*if ( $use_old_API ) {
		//try to get a new dropbox client
		try {
			$dbxClient = new dbx\Client($accessToken, WFU_DROPBOX_CLIENTIDENTIFIER);
		}
		catch (dbx\Exception $ex) {
			wfu_tf_LOG("dropbox_transfer_file_end:client_fail");
			wfu_set_transfer_result($fileid, $jobid, "dropbox", false, "client_fail", "");
			return false;
		}
	}
	else */{
		$app = new DropboxApp("", "", $accessToken);
		$dropbox = wfu_dropbox_get_Dropbox($app);
	}
	//construct the full destination path
	$destination .= '/'.wfu_basename($filepath);
	/*if ( $use_old_API ) {
		//try to upload the file
		$f = wfu_fopen($filepath, "rb", "wfu_dropbox_upload_file");
		try {
			$uploadMetadata = $dbxClient->uploadFile($destination, dbx\WriteMode::add(), $f);
			$metadata = wfu_dropbox_post_upload_actions($uploadMetadata, $params, $dbxClient, true);
			wfu_set_transfer_result($fileid, $jobid, "dropbox", true, "", $filepath, $metadata);
		}
		catch (dbx\Exception $ex) {
			fclose($f);
			wfu_tf_LOG("dropbox_transfer_file_end:upload_fail");
			wfu_set_transfer_result($fileid, $jobid, "dropbox", false, "upload_fail", "");
			return false;
		}
		fclose($f);
	}
	else */{
		try {
			$dropboxFile = new DropboxFile($filepath);
			$params["autorename"] = true;
			$dropbox->upload($dropboxFile, $destination, $params);
		}
		catch (DropboxClientException $e) {
			wfu_set_transfer_result($fileid, $jobid, "dropbox", false, $e->getMessage(), "");
		}
	}
	wfu_tf_LOG("dropbox_transfer_file_end");
	return false;
}

function wfu_dropbox_getWebAuth($use_old_API = false) {
	$keys = wfu_get_dropbox_keys();
	if ( $keys === false ) return null;
	$WebAuth = null;
	/*if ( $use_old_API ) {
		$jsonArr = array(
			"key"		=> $keys["appkey"],
			"secret"	=> $keys["secret"]
		);
		$appInfo = dbx\AppInfo::loadFromJson($jsonArr);
		$WebAuth =  new dbx\WebAuthNoRedirect($appInfo, WFU_DROPBOX_CLIENTIDENTIFIER);
	}
	else */{
		$WebAuth = new DropboxApp($keys["appkey"], $keys["secret"]);
	}
	
	return $WebAuth;
}

function wfu_get_dropbox_keys() {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$postfields = array();
	$postfields['action'] = 'wfuca_get_dropbox_keys';
	$postfields['version_hash'] = WFU_VERSION_HASH;
	$url = ( $plugin_options["altserver"] == "1" && trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")) != "" ? ( trim(WFU_VAR("WFU_ALT_DROPBOX_SERVER")) != "" ? trim(WFU_VAR("WFU_ALT_DROPBOX_SERVER")) : trim(WFU_VAR("WFU_ALT_IPTANUS_SERVER")).'/wp-admin/admin-ajax.php' ) : WFU_DROPBOX_SERVER_URL );
	$result = wfu_post_request($url, $postfields, false);
	$matches = array();
	if ( preg_match("/wfuca_dropbox_keys:(.*)$/", $result, $matches) != 1 ) return false;
	if ( !isset($matches[1]) ) return false;
	if ( preg_match("/^0;(.*?);(.*?);1$/", wfu_plugin_decode_string($matches[1]), $matches) != 1 ) return false;
	if ( !isset($matches[1]) || !isset($matches[2]) ) return false;
	return array( "appkey" => $matches[1], "secret" => $matches[2] );
}

function wfu_dropbox_check_upload($fileid, $jobid, $use_old_API = false) {
	//get access token
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$accessToken = $plugin_options['dropbox_accesstoken'];
	/*if ( $use_old_API ) return true;
	else */{
		$app = new DropboxApp("", "", $accessToken);
		$dropbox = wfu_dropbox_get_Dropbox($app);
		try {
			$dropbox->checkUpload($fileid, $jobid);
		}
		catch (DropboxClientException $e) {
			wfu_set_transfer_result($fileid, $jobid, "dropbox", false, $e->getMessage(), "");
		}		
	}
}