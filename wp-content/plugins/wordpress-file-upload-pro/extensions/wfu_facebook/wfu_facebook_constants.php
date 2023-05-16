<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_facebook_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_facebook_globals_additional');
add_action('_wfu_after_constants', 'wfu_facebook_constants');

function wfu_facebook_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_MESSENGER" => array( "Default Messenger Notifications State", "string", "false", "The default Messenger Notifications state of the uploader shortcode. It can be 'true' or 'false'." ),
		"WFU_MESSENGERTEXT" => array( "Default Messenger Text", "string", "You have received new files. Check the following link for details.%n%%uploaddetails%", "The default Messenger text of the uploader shortcode." ),
		"WFU_MESSENGERUPLOADDETAILS" => array( "Default Upload Details", "string", "You have received new files.%n%%n%Files: %filename%%n%%n%Thank you", "The default upload details text of the uploader shortcode." ),
		"WFU_MESSENGERATTACHFILE" => array( "Default Messenger Attach Uploaded Files State", "string", "false", "Defines whether to include the uploaded files in Messenger message as attachments. It can be 'keep' or 'delete'." )
	);
}

function wfu_facebook_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_ALT_FACEBOOK_SERVER" => array( "Alternative Facebook Server", "string", "https://iptanusservices.appspot.com/g79xo30q8s", "If the alternative Iptanus server is used and this variable is not empty, then it will be used as the alternative Facebook Server URL." ),
		"WFU_FACEBOOK_REGISTRATION_TIMEOUT" => array( "Facebook Registration Timeout", "integer", "120", "Defines the timeout in seconds for completing Facebook registration. It must be a non-zero positive integer." ),
		"WFU_FACEBOOK_PSIDREQUEST_TIMEOUT" => array( "Facebook PSID Request Timeout", "integer", "30", "Defines the timeout in seconds for getting PSID from server. It must be a non-zero positive integer." )
	);
}

function wfu_facebook_constants() {
	DEFINE("WFU_VARIABLE_TITLE_UPLOADDETAILS", __("Insert variable %uploaddetails% inside text. It will be replaced by a link showing details about the upload when clicked.", "wp-file-upload"));
	DEFINE("WFU_ERROR_UPLOADDETAILS_LOGIN", __("You need to login as administrator to see this page!", "wp-file-upload"));
	DEFINE("WFU_FACEBOOK_SERVER_URL", WFU_SERVICES_SERVER_URL.'/wp-admin/admin-ajax.php');
	DEFINE("WFU_FACEBOOK_UPLOADDETAILS_PAGE", '/wfu-upload-details');
	DEFINE("WFU_FACEBOOK_APPID", '310912966338885');
	DEFINE("WFU_FACEBOOK_PAGEID", '487801585055234');
	DEFINE("WFU_FACEBOOK_GRAPHVERSION", 'v3.2');
	DEFINE("WFU_FACEBOOK_REDIRECTURL", 'https://www.iptanus.com/facebook-authorization');
	//alternative insecure server
	DEFINE("WFU_FACEBOOK_SERVER_ALT_URL", WFU_SERVICES_SERVER_ALT_URL.'/wp-admin/admin-ajax.php');
}