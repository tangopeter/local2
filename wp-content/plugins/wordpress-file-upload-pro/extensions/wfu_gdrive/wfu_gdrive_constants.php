<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_gdrive_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_gdrive_globals_additional');
add_action('_wfu_after_constants', 'wfu_gdrive_constants');

function wfu_gdrive_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_GDRIVE" => array( "Default Google Drive Upload State", "string", "false", "The default Google Drive upload state of the uploader shortcode. It can be 'true' or 'false'." ),
		"WFU_GDRIVEPATH" => array( "Default Google Drive Path", "string", "", "The default Google Drive path of the uploader shortcode." ),
		"WFU_GDRIVEUSERDATA" => array( "Default Google Drive Include Userdata State", "string", "false", "The default Google Drive state for including or not userdata in the transferred Google Drive file. It can be 'true' or 'false'." ),
		"WFU_GDRIVELOCAL" => array( "Default Google Drive Local File Action", "string", "keep", "The default action of Google Drive local file of the uploader shortcode. It can be 'keep' or 'delete'." ),
		"WFU_GDRIVESHARE" => array( "Default Google Drive File Share/List State", "string", "false", "The default state of Google Drive file sharing. It can be 'true' or 'false'." ),
		"WFU_GDRIVEDUPLICATES" => array( "Default Google Drive Trash Duplicates State", "string", "false", "The default Google Drive state for trashing or not duplicate files found at Google Drive destination. It can be 'true' or 'false'." )
	);
}

function wfu_gdrive_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ALT_GDRIVE_SERVER" => array( "Alternative Google Drive Server", "string", "https://iptanusservices.appspot.com/g79xo30q8s", "If the alternative Iptanus server is used and this variable is not empty, then it will be used as the alternative Google Drive Server URL." ),
		"WFU_GDRIVE_CHUNKED_UPLOAD_THRESHOLD" => array( "Google Drive Chunked Upload Threshold", "integer", 5000000, "The file size limit in bytes above which the uploaded file is transferred to Google Drive using chunked uploads." ),
		"WFU_GDRIVE_CHUNK_SIZE" => array( "Google Drive Chunk Size", "integer", 1048576, "The size in bytes of the chunk for chunked Google Drive uploads." ),
		"WFU_GDRIVE_MAX_UPLOADJOBS" => array( "Max Concurrent Google Drive Jobs", "integer", 2, "The number of maximum allowable concurrent upload jobs to Google Drive account." ),
		"WFU_GDRIVE_CHECKUPLOADS_INTERVAL" => array( "Google Drive Jobs Recheck Interval", "integer", 1800, "The interval for checking if there are pending Google Drive upload jobs, in seconds." ),
		"WFU_GDRIVE_MAX_UPLOADTIME" => array( "Google Drive Upload Timeout", "integer", 7200, "The timeout of Google Drive uploads, in seconds. A value of -1 denotes no limit." ),
		"WFU_GDRIVE_MAX_RETRYTIME" => array( "Max Google Drive Upload Retry Time", "integer", 86400, "The maximum time that a Google Drive upload will be retried, in seconds. A value of -1 denotes no limit." ),
		"WFU_GDRIVE_MAX_CHUNKTIME" => array( "Max Google Drive Chunk Timeout", "integer", 120, "The maximum aloowable time for a Google Drive upload chunk when chunked upload is active, in seconds. A value of -1 denotes no limit." ),
		"WFU_GDRIVE_RETRIES" => array( "Google Drive Upload Retries", "integer", 3, "The number of consecutive retries of a Google Drive upload. A value of -1 denotes no limit." ),
		"WFU_GDRIVE_MAX_RETRIES" => array( "Max Google Drive Upload Retries", "integer", 12, "The number of maximum retries of a Google Drive upload. A value of -1 denotes no limit." ),
		"WFU_GDRIVE_KEEP_FAILED_FILES" => array( "Keep Google Drive Failed Files", "string", "true", "Keep or delete from the list files that failed to be transferred to Google Drive. It can be 'true' or 'false'." ),
		"WFU_GDRIVE_USERDATA_INPROPERTIES" => array( "Userdata in Google Drive File Properties", "string", "false", "Add userdata in the transferred Google Drive file's properties, besides adding them in the description. It can be 'true' or 'false'." )
	);
}

function wfu_gdrive_constants() {
	DEFINE("WFU_GDRIVE_SERVER_URL", WFU_SERVICES_SERVER_URL.'/wp-admin/admin-ajax.php');
	DEFINE("WFU_GDRIVE_CLIENTIDENTIFIER", 'Wordpress-File-Upload-Plugin-app/1.0');
	//alternative insecure server
	DEFINE("WFU_GDRIVE_SERVER_ALT_URL", WFU_SERVICES_SERVER_ALT_URL.'/wp-admin/admin-ajax.php');
}