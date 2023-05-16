<?php


add_filter('_wfu_plugin_extensions', 'wfu_dropbox_plugin_extension', 10, 1);

function wfu_dropbox_get_info() {
	return array(
		"code" => "dropbox",
		"name" => "Dropbox",
		"description" => "Enable uploaded files to be stored on a Dropbox account."
	);
}

function wfu_dropbox_plugin_extension($extensions) {
	array_push($extensions, wfu_dropbox_get_info());
	return $extensions;
}

function wfu_check_load_dropbox() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("dropbox", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["dropbox"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_constants.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_settings.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_attributes.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_admin_browser.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_maintenance.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_ajaxactions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_admin.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_functions.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_dropbox/wfu_dropbox_transfers.php';
	}
}

wfu_check_load_dropbox();