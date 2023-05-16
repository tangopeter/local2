<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_dropbox_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_dropbox_globals_additional');
add_action('_wfu_after_constants', 'wfu_dropbox_constants');

function wfu_dropbox_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_DROPBOX" => array( "Default Dropbox Upload State", "string", "false", "The default Dropbox upload state of the uploader shortcode. It can be 'true' or 'false'." ),
		"WFU_DROPBOXPATH" => array( "Default Dropbox Path", "string", "", "The default Dropbox path of the uploader shortcode." ),
		"WFU_DROPBOXLOCAL" => array( "Default Dropbox Local File Action", "string", "keep", "The default action of Dropbox local file of the uploader shortcode. It can be 'keep' or 'delete'." ),
		"WFU_DROPBOXSHARE" => array( "Default Dropbox File Share/List State", "string", "false", "The default state of Dropbox file sharing. It can be 'true' or 'false'." )
	);
}

function wfu_dropbox_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ALT_DROPBOX_SERVER" => array( "Alternative Dropbox Server", "string", "https://iptanusservices.appspot.com/g79xo30q8s", "If the alternative Iptanus server is used and this variable is not empty, then it will be used as the alternative Dropbox Server URL." ),
		"WFU_DROPBOX_FORCE_32BIT" => array( "Force Dropbox to Work for 32bit Servers", "string", "false", "Force Dropbox API to work for 32bit servers, which is normally not supported. It can be 'true' or 'false'." ),
		"WFU_DROPBOX_USE_V1_API" => array( "Force Dropbox to Use V1 API", "string", "false", "Force Dropbox to use the old V1 API instead of V2. It can be 'true' or 'false'. This option is depreciated since Dropbox V1 API has been dropped." ),
		"WFU_DROPBOX_MAX_UPLOADJOBS" => array( "Max Concurrent Dropbox Jobs", "integer", 2, "The number of maximum allowable concurrent upload jobs to Dropbox account." ),
		"WFU_DROPBOX_CHECKUPLOADS_INTERVAL" => array( "Dropbox Jobs Recheck Interval", "integer", 1800, "The interval for checking if there are pending Dropbox upload jobs, in seconds." ),
		"WFU_DROPBOX_MAX_UPLOADTIME" => array( "Dropbox Upload Timeout", "integer", 7200, "The timeout of Dropbox uploads, in seconds. A value of -1 denotes no limit." ),
		"WFU_DROPBOX_MAX_RETRYTIME" => array( "Max Dropbox Upload Retry Time", "integer", 86400, "The maximum time that a Dropbox upload will be retried, in seconds. A value of -1 denotes no limit." ),
		"WFU_DROPBOX_MAX_CHUNKTIME" => array( "Max Dropbox Chunk Timeout", "integer", 120, "The maximum aloowable time for a Dropbox upload chunk when chunked upload is active, in seconds. A value of -1 denotes no limit." ),
		"WFU_DROPBOX_RETRIES" => array( "Dropbox Upload Retries", "integer", 3, "The number of consecutive retries of a Dropbox upload. A value of -1 denotes no limit." ),
		"WFU_DROPBOX_MAX_RETRIES" => array( "Max Dropbox Upload Retries", "integer", 12, "The number of maximum retries of a Dropbox upload. A value of -1 denotes no limit." ),
		"WFU_DROPBOX_KEEP_FAILED_FILES" => array( "Keep Dropbox Failed Files", "string", "true", "Keep or delete from the list files that failed to be transferred to Dropbox. It can be 'true' or 'false'." )
	);
}

function wfu_dropbox_constants() {
	DEFINE("WFU_DROPBOX_SERVER_URL", WFU_SERVICES_SERVER_URL.'/wp-admin/admin-ajax.php');
	DEFINE("WFU_DROPBOX_CLIENTIDENTIFIER", 'Wordpress-File-Upload-Plugin-app/1.0');
	DEFINE("WFU_DROPBOX_FROMAUTH1", 'https://api.dropboxapi.com/2/auth/token/from_oauth1');
	//alternative insecure server
	DEFINE("WFU_DROPBOX_SERVER_ALT_URL", WFU_SERVICES_SERVER_ALT_URL.'/wp-admin/admin-ajax.php');
}