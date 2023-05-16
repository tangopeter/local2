<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_onedrive_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_onedrive_globals_additional');
add_action('_wfu_after_constants', 'wfu_onedrive_constants');

function wfu_onedrive_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ONEDRIVE" => array( "Default Microsoft OneDrive Upload State", "string", "false", "The default Microsoft OneDrive upload state of the uploader shortcode. It can be 'true' or 'false'." ),
		"WFU_ONEDRIVEPATH" => array( "Default Microsoft OneDrive Path", "string", "", "The default Microsoft OneDrive path of the uploader shortcode." ),
		"WFU_ONEDRIVEUSERDATA" => array( "Default Microsoft OneDrive Include Userdata State", "string", "false", "The default Microsoft OneDrive state for including or not userdata in the transferred Microsoft OneDrive file. It can be 'true' or 'false'." ),
		"WFU_ONEDRIVELOCAL" => array( "Default Microsoft OneDrive Local File Action", "string", "keep", "The default action of Microsoft OneDrive local file of the uploader shortcode. It can be 'keep' or 'delete'." ),
		"WFU_ONEDRIVESHARE" => array( "Default Microsoft OneDrive File Share/List State", "string", "false", "The default state of Microsoft OneDrive file sharing. It can be 'true' or 'false'." ),
		"WFU_ONEDRIVECONFLICT" => array( "Default Microsoft OneDrive Conflict Policy", "string", "rename", "The default action when a file with the same filename already exists at Microsoft OneDrive destination. It can be 'fail', 'replace' or 'rename'." )
	);
}

function wfu_onedrive_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ALT_ONEDRIVE_SERVER" => array( "Alternative Microsoft OneDrive Server", "string", "https://iptanusservices.appspot.com/g79xo30q8s", "If the alternative Iptanus server is used and this variable is not empty, then it will be used as the alternative Microsoft OneDrive Server URL." ),
		"WFU_ONEDRIVE_REST_ENDPOINT" => array( "Microsoft OneDrive REST End-Point", "string", "OneDrive", "The REST end-point to use for interacting with OneDrive. It can be 'OneDrive' or 'Graph'." ),
		"WFU_ONEDRIVE_CHUNKED_UPLOAD_THRESHOLD" => array( "Microsoft OneDrive Chunked Upload Threshold", "integer", 4194304, "The file size limit in bytes above which the uploaded file is transferred to Microsoft OneDrive using chunked uploads." ),
		"WFU_ONEDRIVE_CHUNK_SIZE" => array( "Microsoft OneDrive Chunk Size", "integer", 4915200, "The size in bytes of the chunk for chunked Microsoft OneDrive uploads." ),
		"WFU_ONEDRIVE_MAX_UPLOADJOBS" => array( "Max Concurrent Microsoft OneDrive Jobs", "integer", 2, "The number of maximum allowable concurrent upload jobs to Microsoft OneDrive account." ),
		"WFU_ONEDRIVE_CHECKUPLOADS_INTERVAL" => array( "Microsoft OneDrive Jobs Recheck Interval", "integer", 1800, "The interval for checking if there are pending Microsoft OneDrive upload jobs, in seconds." ),
		"WFU_ONEDRIVE_MAX_UPLOADTIME" => array( "Microsoft OneDrive Upload Timeout", "integer", 7200, "The timeout of Microsoft OneDrive uploads, in seconds. A value of -1 denotes no limit." ),
		"WFU_ONEDRIVE_MAX_RETRYTIME" => array( "Max Microsoft OneDrive Upload Retry Time", "integer", 86400, "The maximum time that a Microsoft OneDrive upload will be retried, in seconds. A value of -1 denotes no limit." ),
		"WFU_ONEDRIVE_MAX_CHUNKTIME" => array( "Max Microsoft OneDrive Chunk Timeout", "integer", 120, "The maximum aloowable time for a Microsoft OneDrive upload chunk when chunked upload is active, in seconds. A value of -1 denotes no limit." ),
		"WFU_ONEDRIVE_RETRIES" => array( "Microsoft OneDrive Upload Retries", "integer", 3, "The number of consecutive retries of a Microsoft OneDrive upload. A value of -1 denotes no limit." ),
		"WFU_ONEDRIVE_MAX_RETRIES" => array( "Max Microsoft OneDrive Upload Retries", "integer", 12, "The number of maximum retries of a Microsoft OneDrive upload. A value of -1 denotes no limit." ),
		"WFU_ONEDRIVE_KEEP_FAILED_FILES" => array( "Keep Microsoft OneDrive Failed Files", "string", "true", "Keep or delete from the list files that failed to be transferred to Microsoft OneDrive. It can be 'true' or 'false'." ),
		"WFU_ONEDRIVE_USERDATA_INPROPERTIES" => array( "Userdata in Microsoft OneDrive File Properties", "string", "false", "Add userdata in the transferred Microsoft OneDrive file's properties, besides adding them in the description. It can be 'true' or 'false'." )
	);
}

function wfu_onedrive_constants() {
	DEFINE("WFU_ONEDRIVE_SERVER_URL", WFU_SERVICES_SERVER_URL.'/wp-admin/admin-ajax.php');
	DEFINE("WFU_ONEDRIVE_CLIENTIDENTIFIER", 'b76bf22f-446e-42b7-ae35-f7a8d304d5c4');
	DEFINE("WFU_ONEDRIVE_REST_ENDPOINT_OENDP", 'https://api.onedrive.com/');
	DEFINE("WFU_ONEDRIVE_AUTHORIZEURI_OENDP", 'https://login.live.com/oauth20_authorize.srf');
	DEFINE("WFU_ONEDRIVE_TOKENURI_OENDP", 'https://login.live.com/oauth20_token.srf');
	DEFINE("WFU_ONEDRIVE_REST_ENDPOINT_GENDP", 'https://graph.microsoft.com/');
	DEFINE("WFU_ONEDRIVE_AUTHORIZEURI_GENDP", 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize');
	DEFINE("WFU_ONEDRIVE_TOKENURI_GENDP", 'https://login.microsoftonline.com/common/oauth2/v2.0/token');
	DEFINE("WFU_ONEDRIVE_REDIRECTURI", 'https://www.iptanus.com/microsoft-onedrive-authorization');
	//alternative insecure server
	DEFINE("WFU_ONEDRIVE_SERVER_ALT_URL", WFU_SERVICES_SERVER_ALT_URL.'/wp-admin/admin-ajax.php');
}