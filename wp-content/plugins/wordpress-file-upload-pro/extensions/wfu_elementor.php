<?php


add_filter('_wfu_plugin_extensions', 'wfu_elementor_plugin_extension', 10, 1);

function wfu_elementor_get_info() {
	return array(
		"code" => "elementor",
		"name" => "Elementor Support",
		"description" => "Enable compatibility of the plugin with Elementor Page Builder."
	);
}

function wfu_elementor_plugin_extension($extensions) {
	array_push($extensions, wfu_elementor_get_info());
	return $extensions;
}

function wfu_check_load_elementor() {
	global $WFU_PLUGIN_EXTENSIONS;
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) || !array_key_exists("elementor", $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS["elementor"] !== "0" ) {
		include_once ABSWPFILEUPLOAD_DIR.'extensions/wfu_elementor/wfu_elementor_functions.php';
	}
}

wfu_check_load_elementor();