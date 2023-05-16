<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_amazons3_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_amazons3_globals_additional');
add_action('_wfu_after_constants', 'wfu_amazons3_constants');

function wfu_amazons3_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_AMAZONS3" => array( "Default Amazon S3 Upload State", "string", "false", "The default Amazon S3 upload state of the uploader shortcode. It can be 'true' or 'false'." ),
		"WFU_AMAZONS3BUCKET" => array( "Default Amazon S3 Bucket Name", "string", "", "The default Amazon S3 bucket name." ),
		"WFU_AMAZONS3PATH" => array( "Default Amazon S3 Path", "string", "", "The default Amazon S3 path of the uploader shortcode." ),
		"WFU_AMAZONS3ACCESS" => array( "Default Amazon S3 File Access", "string", "private", "The default Amazon S3 file access of the uploader shortcode." ),
		"WFU_AMAZONS3USERDATA" => array( "Default Amazon S3 Include Userdata State", "string", "false", "The default Amazon S3 state for including or not userdata in the transferred Amazon S3 file. It can be 'true' or 'false'." ),
		"WFU_AMAZONS3LOCAL" => array( "Default Amazon S3 Local File Action", "string", "keep", "The default action of Amazon S3 local file of the uploader shortcode. It can be 'keep' or 'delete'." ),
		"WFU_AMAZONS3SHARE" => array( "Default Amazon S3 File Share/List State", "string", "false", "The default state of Amazon S3 file sharing. It can be 'true' or 'false'." )
	);
}

function wfu_amazons3_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ALT_AMAZONS3_SERVER" => array( "Alternative Amazon S3 Server", "string", "https://iptanusservices.appspot.com/g79xo30q8s", "If the alternative Iptanus server is used and this variable is not empty, then it will be used as the alternative Amazon S3 Server URL." ),
		"WFU_AMAZONS3_VERSION" => array( "Amazon S3 Version", "string", "latest", "The default Amazon S3 version." ),
		"WFU_AMAZONS3_REGION" => array( "Amazon S3 Region", "string", "us-east-1", "The default Amazon S3 region." ),
		"WFU_AMAZONS3_MULTIPART_UPLOAD_THRESHOLD" => array( "Amazon S3 Multipart Upload Threshold", "integer", 5242880, "The file size above which multipart uploads will be used to transfer the files to Amazon S3." ),
		"WFU_AMAZONS3_MAX_UPLOADJOBS" => array( "Max Concurrent Amazon S3 Jobs", "integer", 2, "The number of maximum allowable concurrent upload jobs to Amazon S3 account." ),
		"WFU_AMAZONS3_CHECKUPLOADS_INTERVAL" => array( "Amazon S3 Jobs Recheck Interval", "integer", 1800, "The interval for checking if there are pending Amazon S3 upload jobs, in seconds." ),
		"WFU_AMAZONS3_MAX_UPLOADTIME" => array( "Amazon S3 Upload Timeout", "integer", 7200, "The timeout of Amazon S3 uploads, in seconds. A value of -1 denotes no limit." ),
		"WFU_AMAZONS3_MAX_RETRYTIME" => array( "Max Amazon S3 Upload Retry Time", "integer", 86400, "The maximum time that a Amazon S3 upload will be retried, in seconds. A value of -1 denotes no limit." ),
		"WFU_AMAZONS3_MAX_CHUNKTIME" => array( "Max Amazon S3 Chunk Timeout", "integer", 120, "The maximum aloowable time for an Amazon S3 upload chunk when multipart upload is active, in seconds. A value of -1 denotes no limit." ),
		"WFU_AMAZONS3_RETRIES" => array( "Amazon S3 Upload Retries", "integer", 3, "The number of consecutive retries of a Amazon S3 upload. A value of -1 denotes no limit." ),
		"WFU_AMAZONS3_MAX_RETRIES" => array( "Max Amazon S3 Upload Retries", "integer", 12, "The number of maximum retries of an Amazon S3 upload. A value of -1 denotes no limit." ),
		"WFU_AMAZONS3_KEEP_FAILED_FILES" => array( "Keep Amazon S3 Failed Files", "string", "true", "Keep or delete from the list files that failed to be transferred to Amazon S3. It can be 'true' or 'false'." )
	);
}

function wfu_amazons3_constants() {
	DEFINE("WFU_AMAZONS3_SERVER_URL", WFU_SERVICES_SERVER_URL.'/wp-admin/admin-ajax.php');
	//alternative insecure server
	DEFINE("WFU_AMAZONS3_SERVER_ALT_URL", WFU_SERVICES_SERVER_ALT_URL.'/wp-admin/admin-ajax.php');
}