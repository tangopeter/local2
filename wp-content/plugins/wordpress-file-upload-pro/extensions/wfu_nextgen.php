<?php


add_filter('_wfu_plugin_extensions', 'wfu_nextgen_plugin_extension', 10, 1);

function wfu_nextgen_get_info() {
	return array(
		"code" => "nextgen",
		"name" => "NextGEN Gallery",
		"description" => "Enable uploaded files to be added in NextGEN galleries."
	);
}

function wfu_nextgen_plugin_extension($extensions) {
	array_push($extensions, wfu_nextgen_get_info());
	return $extensions;
}

function wfu_check_load_nextgen() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("nextgen", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["nextgen"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_nextgen/wfu_ngg_constants.php'; 
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_nextgen/wfu_ngg_attributes.php';
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_nextgen/wfu_ngg_addfiles.php';
	}
}

wfu_check_load_nextgen();