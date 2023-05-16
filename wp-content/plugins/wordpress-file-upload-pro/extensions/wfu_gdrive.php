<?php


add_filter('_wfu_plugin_extensions', 'wfu_gdrive_plugin_extension', 10, 1);

function wfu_gdrive_get_info() {
	return array(
		"code" => "gdrive",
		"name" => "Google Drive",
		"description" => "Enable uploaded files to be stored on a Google Drive account."
	);
}

function wfu_gdrive_plugin_extension($extensions) {
	array_push($extensions, wfu_gdrive_get_info());
	return $extensions;
}

function wfu_check_load_gdrive() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("gdrive", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["gdrive"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_constants.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_settings.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_attributes.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_admin_browser.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_maintenance.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_ajaxactions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_admin.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_transfers.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_gdrive/wfu_gdrive_functions.php';
	}
}

wfu_check_load_gdrive();