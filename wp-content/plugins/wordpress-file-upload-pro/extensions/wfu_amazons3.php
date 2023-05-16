<?php



add_filter('_wfu_plugin_extensions', 'wfu_amazons3_plugin_extension', 10, 1);

function wfu_amazons3_get_info() {
	return array(
		"code" => "amazons3",
		"name" => "Amazon S3",
		"description" => "Enable uploaded files to be stored on an Amazon S3 account."
	);
}

function wfu_amazons3_plugin_extension($extensions) {
	array_push($extensions, wfu_amazons3_get_info());
	return $extensions;
}

function wfu_check_load_amazons3() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("amazons3", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["amazons3"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_constants.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_settings.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_attributes.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_admin_browser.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_maintenance.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_admin.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_ajaxactions.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_functions.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_amazons3/wfu_amazons3_transfers.php';
	}
}

wfu_check_load_amazons3();