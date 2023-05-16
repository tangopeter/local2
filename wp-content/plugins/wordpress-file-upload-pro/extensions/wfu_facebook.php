<?php


add_filter('_wfu_plugin_extensions', 'wfu_facebook_plugin_extension', 10, 1);

function wfu_facebook_get_info() {
	return array(
		"code" => "facebook",
		"name" => "Facebook Notifications",
		"description" => "Enable notifications to be sent to a Facebook Messenger account."
	);
}

function wfu_facebook_plugin_extension($extensions) {
	array_push($extensions, wfu_facebook_get_info());
	return $extensions;
}

function wfu_check_load_facebook() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("facebook", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["facebook"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_constants.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_settings.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_functions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_attributes.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_ajaxactions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_facebook/wfu_facebook_admin.php';
	}
}

wfu_check_load_facebook();