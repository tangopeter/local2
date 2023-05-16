<?php


add_filter('_wfu_plugin_extensions', 'wfu_onedrive_plugin_extension', 10, 1);

function wfu_onedrive_get_info() {
	return array(
		"code" => "onedrive",
		"name" => "Microsoft OneDrive",
		"description" => "Enable uploaded files to be stored on a Microsoft OneDrive account."
	);
}

function wfu_onedrive_plugin_extension($extensions) {
	array_push($extensions, wfu_onedrive_get_info());
	return $extensions;
}

function wfu_check_load_onedrive() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("onedrive", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["onedrive"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_constants.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_settings.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_attributes.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_admin_browser.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_maintenance.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_ajaxactions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_admin.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_transfers.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_onedrive/wfu_onedrive_functions.php';
	}
}

wfu_check_load_onedrive();