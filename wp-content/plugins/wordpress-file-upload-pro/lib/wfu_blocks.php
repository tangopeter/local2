<?php

/**
 * Plugin Upload Form Blocks
 *
 * This file contains functions related to preparation of the elements of the
 * upload form of the plugin.
 *
 * @link /lib/wfu_blocks.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 2.1.2
 */

/**
 * Prepare the Upload Form Generic Element.
 *
 * This function prepares the display properties of the generic top-level
 * element of the plugin's upload form.
 *
 * @since 4.1.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the generic top-level element.
 */
function wfu_prepare_base_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["params"] = $params;
	
	$base_item["title"] = '';
	$base_item["hidden"] = false;
	$base_item["width"] = "";
	$base_item["object"] = "GlobalData.WFU[".$data["ID"]."].base";
	//read html output from template
	$base_item += wfu_read_template_output("base", $data);

	return $base_item;
}

/**
 * Prepare the Upload Form Visual Editor Element.
 *
 * This function prepares the display properties of the visual editor element of
 * the plugin's upload form.
 *
 * @since 4.0.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the visual editor element.
 */
function wfu_prepare_visualeditorbutton_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	if ( isset($params["uploadid"]) ) {
		$ID = $params["uploadid"];
		$JS_Object = "GlobalData.WFU[".$ID."].visualeditorbutton";
	}
	else {
		$ID = $params["browserid"];
		$JS_Object = "GlobalData.WFUB[".$ID."].visualeditorbutton";
	}
	//prepare data for template
	$data["ID"] = $ID;
	$data["shortcode_tag"] = $additional_params['shortcode_tag'];
	$data["JS_Object"] = $JS_Object;
	$data["params"] = $params;
	
	$visualeditorbutton_item["title"] = '';
	$visualeditorbutton_item["hidden"] = false;
	$visualeditorbutton_item["width"] = "";
	$visualeditorbutton_item["object"] = $JS_Object;
	//read html output from template
	$visualeditorbutton_item += wfu_read_template_output("visualeditorbutton", $data);
	//initialize title object properties
	$visualeditorbutton_item["js"] = $JS_Object." = { ".
		"shortcode_tag: \"".$data["shortcode_tag"]."\", ".
		"attachInvokeHandler: function(invoke_function) {}, ".
		"onInvoke: function() {}, ".
		"afterInvoke: function() {}".
	"};\n\n".$visualeditorbutton_item["js"];
	//append javascript variable that checks if title exists or not
	$visualeditorbutton_item["js"] .= "\n\n".$JS_Object."_exist = true;";

	return $visualeditorbutton_item;
}

/**
 * Prepare the Upload Form Drag-and-Drop Element.
 *
 * This function prepares the display properties of the drag-and-drop element of
 * the plugin's upload form.
 *
 * @since 4.0.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the drag-and-drop element.
 */
function wfu_prepare_dragdrop_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["params"] = $params;
	
	$dragdrop_item["title"] = '';
	$dragdrop_item["hidden"] = false;
	$dragdrop_item["width"] = "";
	$dragdrop_item["object"] = "GlobalData.WFU[".$data["ID"]."].dragdrop";
	//read html output from template
	$dragdrop_item += wfu_read_template_output("dragdrop", $data);
	//initialize title object properties
	$dragdrop_item["js"] = "GlobalData.WFU[".$data["ID"]."].dragdrop = { ".
		"attachHandlers: function(handlers) {}, ".
		"disableDragDrop: function() {}, ".
		"reEnableDragDrop: function() {} ".
	"};\n\n".$dragdrop_item["js"];
	//append javascript variable that checks if title exists or not
	$dragdrop_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].dragdrop_exist = ".( $params["dragdrop"] == "true" ? "true" : "false" ).";";

	return $dragdrop_item;
}

/**
 * Prepare the Upload Form Subfolders Element.
 *
 * This function prepares the display properties of the subfolders element of
 * the plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the subfolders element.
 */
function wfu_prepare_subfolders_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["widths"] = $additional_params['widths'];
	$data["heights"] = $additional_params['heights'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["show_uploadfolder"] = ( $params["showtargetfolder"] == "true" );
	$data["show_subfolders"] = ( $params["askforsubfolders"] == "true" );
	$data["editable"] = ( substr($params["subfoldertree"], 0, 5) == "auto+" );
	$data["uploadfolder"] = wfu_upload_plugin_directory($params["uploadpath"]);
	$data["uploadfolder_title"] = $params["targetfolderlabel"];
	$data["subfolders"] = array( 'path' => array(), 'label' => array(), 'level' => array(), 'default' => array() );
	$data["subfolders_title"] = $params["subfolderlabel"];
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	//prepare data of subfolders
	if ( $data["show_subfolders"] && !$data["testmode"] ) {
		if ( substr($params["subfoldertree"], 0, 4) == "auto" ) {
			$upload_directory = wfu_upload_plugin_full_path($params);
			$dirtree = wfu_getTree($upload_directory);
			foreach ( $dirtree as &$dir ) $dir = '*'.$dir;
			$params["subfoldertree"] = implode(',', $dirtree);
		}
		$subfolders = wfu_parse_folderlist($params["subfoldertree"]);
		if ( count($subfolders['path']) == 0 ) {
			array_push($subfolders['path'], "");
			array_push($subfolders['label'], wfu_upload_plugin_directory($params["uploadpath"]));
			array_push($subfolders['level'], 0);
			array_push($subfolders['default'], false);
		}
		$data["subfolders"] = $subfolders;
	}
	
	$subfolders_item = null;
	if ( $data["show_uploadfolder"] || $data["show_subfolders"] ) {
		$subfolders_item["title"] = 'wordpress_file_upload_subfolders_'.$data["ID"];
		$subfolders_item["hidden"] = false;
		$subfolders_item["width"] = "";
		$subfolders_item["object"] = "GlobalData.WFU[".$data["ID"]."].subfolders";
		//for responsive plugin adjust width
		if ( $data["responsive"] ) $subfolders_item["width"] = $data["width"];
		//read html output from template
		$subfolders_item += wfu_read_template_output("subfolders", $data);
		//initialize subfolders object properties
		$subfolders_item["js"] = "GlobalData.WFU[".$data["ID"]."].subfolders = { ".
			"update_handler: function(new_value) { document.getElementById('hiddeninput_".$data["ID"]."').value = new_value; }, ".
			"check: function() { return true; }, ".
			"index: function() { return -1; }, ".
			"reset: function() {}, ".
			"toggle: function(enabled) {}".
		"};\n\n".$subfolders_item["js"];
		//append javascript variable that checks if subfolders element exists or not
		if ( $data["show_subfolders"] ) $subfolders_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].subfolders_exist = true;";
	}

	return $subfolders_item;
}

/**
 * Prepare the Upload Form Title Element.
 *
 * This function prepares the display properties of the title element of the
 * plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the title element.
 */
function wfu_prepare_title_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['title'];
	$data["height"] = $additional_params['heights']['title'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["title"] = $params["uploadtitle"];
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	
	$title_item["title"] = 'wordpress_file_upload_title_'.$data["ID"];
	$title_item["hidden"] = false;
	$title_item["width"] = "";
	$title_item["object"] = "GlobalData.WFU[".$data["ID"]."].title";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $title_item["width"] = $data["width"];
	//read html output from template
	$title_item += wfu_read_template_output("title", $data);
	//initialize title object properties
	$title_item["js"] = "GlobalData.WFU[".$data["ID"]."].title = {};\n\n".$title_item["js"];
	//append javascript variable that checks if title exists or not
	$title_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].title_exist = true;";

	return $title_item;
}

/**
 * Prepare the Upload Form Filename Element.
 *
 * This function prepares the display properties of the filename (textbox)
 * element of the plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the filename element.
 */
function wfu_prepare_textbox_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['filename'];
	$data["height"] = $additional_params['heights']['filename'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	

	$textbox_item["title"] = 'wordpress_file_upload_textbox_'.$data["ID"];
	$textbox_item["hidden"] = false;
	$textbox_item["width"] = "";
	$textbox_item["object"] = "GlobalData.WFU[".$data["ID"]."].textbox";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $textbox_item["width"] = $data["width"];
	//read html output from template
	$textbox_item += wfu_read_template_output("textbox", $data);
	//initialize textbox object properties
	$textbox_item["js"] = "GlobalData.WFU[".$data["ID"]."].textbox = { ".
		"attachCancelHandler: function(cancel_function) {}, ".
		"dettachCancelHandler: function() {}, ".
		"update: function(action, filenames) {} ".
	"};\n\n".$textbox_item["js"];
	//append javascript variable that checks if textbox exists or not
	$textbox_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].textbox_exist = true;";

	return $textbox_item;
}

/**
 * Prepare the Upload Form Main Form Element.
 *
 * This function prepares the display properties of the form element of the
 * plugin's upload form. This element also contains the select button element.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the main form element.
 */
function wfu_prepare_uploadform_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['selectbutton'];
	$data["height"] = $additional_params['heights']['selectbutton'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["label"] = $params["selectbutton"];
	$data["filename"] = "uploadedfile_".$data["ID"].( $params["multiple"] == "true" ? "[]" : "" );
	$data["hidden_elements"] = array(
		array( "id" => "wfu_uploader_nonce_".$data["ID"], "name" => "wfu_uploader_nonce", "value" => wp_create_nonce("wfu-uploader-nonce") ),
		array( "id" => "hiddeninput_".$data["ID"], "name" => "hiddeninput_".$data["ID"], "value" => "" ),
		array( "id" => "uniqueuploadid_".$data["ID"], "name" => "uniqueuploadid_".$data["ID"], "value" => "" ),
		array( "id" => "nofileupload_".$data["ID"], "name" => "nofileupload_".$data["ID"], "value" => "0" ),
		array( "id" => "uploadedfile_".$data["ID"]."_name", "name" => "uploadedfile_".$data["ID"]."_name", "value" => wfu_plugin_encode_string("dummy.txt") ),
		array( "id" => "uploadedfile_".$data["ID"]."_size", "name" => "uploadedfile_".$data["ID"]."_size", "value" => "0" ),
		array( "id" => "adminerrorcodes_".$data["ID"], "name" => "adminerrorcodes_".$data["ID"], "value" => "" )
	);
	if ( $additional_params["require_consent"] ) array_push( $data["hidden_elements"], 
		array( "id" => "consentresult_".$data["ID"], "name" => "consentresult_".$data["ID"], "value" => "" )
	);
	foreach ($params["userdata_fields"] as $userdata_key => $userdata_field)
		array_push($data["hidden_elements"], array( "id" => "hiddeninput_".$data["ID"]."_userdata_".$userdata_key, "name" => "hiddeninput_".$data["ID"]."_userdata_".$userdata_key, "value" => "" ));
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	$data["multiple"] = ( $params["multiple"] == "true" );
	// if multiple files can be uploaded, then select plural form of select button, if it has been defined
	$labels = explode("/", $params["selectbutton"]);
	if ( count($labels) == 1 ) array_push($labels, $params["selectbutton"]);
	$data["label"] = $labels[0];
	if ( $data["multiple"] ) $data["label"] = $labels[1];
	array_push($data["hidden_elements"],
		array( "id" => "captchachallenge_".$data["ID"], "name" => "captchachallenge_".$data["ID"], "value" => $additional_params['captcha_challenge'] ),
		array( "id" => "captchainput_".$data["ID"], "name" => "captchainput_".$data["ID"], "value" => "" )
	);
	$data["allowdir"] = ( $params["allowdir"] == "true" );
	$data["forcedir"] = ( $params["forcedir"] == "true" );

	$uploadform_item["title"] = 'wordpress_file_upload_form_'.$data["ID"];
	// selectbutton block is mandatory because it contains the upload form element, so in case it is not included in the placements
	// attribute then we set its visibility to hidden
	$uploadform_item["hidden"] = ( strpos($params["placements"], "selectbutton") === false );
	$uploadform_item["width"] = "";
	$uploadform_item["object"] = "GlobalData.WFU[".$data["ID"]."].uploadform";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $uploadform_item["width"] = $data["width"];

	//read html output from template
	$uploadform_item += wfu_read_template_output("uploadform", $data);
	//initialize uploadform object properties
	$uploadform_item["js"] = "GlobalData.WFU[".$data["ID"]."].uploadform = { ".
		"attachActions: function(clickaction, changeaction) {}, ".
		"reset: function() {}, ".
		"resetDummy: function() {}, ".
		"submit: function() {}, ".
		"lock: function() {}, ".
		"unlock: function() {}, ".
		"changeFileName: function(new_filename) {}, ".
		"files: function() { return []; } ".
	"};\n\n".$uploadform_item["js"];
	//append javascript variable that checks if uploadform element exists or not
	$uploadform_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].uploadform_exist = true;";

	return $uploadform_item;
}

/**
 * Prepare the Upload Form Submit Button Element.
 *
 * This function prepares the display properties of the submit button element of
 * the plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the submit button element.
 */
function wfu_prepare_submit_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['uploadbutton'];
	$data["height"] = $additional_params['heights']['uploadbutton'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["allownofile"] = ( $params["allownofile"] == "true" );
	$data["label"] = $params["uploadbutton"];
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	$data["multiple"] = ( $params["multiple"] == "true" );
	// if multiple files can be uploaded, then select plural form of upload button, if it has been defined
	$labels = explode("/", $data["label"]);
	if ( count($labels) == 1 ) array_push($labels, $data["label"]);
	$data["label"] = $labels[0];
	if ( $data["multiple"] ) $data["label"] = $labels[1];

	$submit_item["title"] = 'wordpress_file_upload_submit_'.$data["ID"];
	$submit_item["hidden"] = false;
	$submit_item["width"] = "";
	$submit_item["object"] = "GlobalData.WFU[".$data["ID"]."].submit";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $submit_item["width"] = $data["width"];

	//read html output from template
	$submit_item += wfu_read_template_output("submit", $data);
	//initialize submit element (upload button) object properties
	$submit_item["js"] = "GlobalData.WFU[".$data["ID"]."].submit = { ".
		"label_default: '".$data["label"]."', ".
		"label_singular: '".$labels[0]."', ".
		"label_plural: '".$labels[1]."', ".
		"attachClickAction: function(clickaction) { }, ".
		"updateLabel: function(new_label) { }, ".
		"toggle: function(status) { } ".
	"};\n\n".$submit_item["js"];
	//append javascript variable that checks if upload button element exists
	$submit_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].submit_exist = true;";

	return $submit_item;
}

/**
 * Prepare the Upload Form Captcha Element.
 *
 * This function prepares the display properties of the captcha element of the
 * plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the captcha element.
 */
function wfu_prepare_captcha_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['captcha'];
	$data["height"] = $additional_params['heights']['captcha'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["type"] = $params["captchatype"];
	$data["V2unsupported"] = false;
	$data["sitekey"] = $plugin_options['captcha_sitekey'];
	$data["frameURL"] = "";
	$data["options"] = wfu_parse_captcha_options($params["captchaoptions"]);
	$data["index"] = $occurrence_index;
	$data["params"] = $params;

	$captcha_item["title"] = 'wordpress_file_upload_captcha_'.$data["ID"];
	$captcha_item["hidden"] = false;
	$captcha_item["width"] = "";
	$captcha_item["object"] = "GlobalData.WFU[".$data["ID"]."].captcha";

	$captcha_options_array = wfu_parse_captcha_options($params["captchaoptions"]);
	$captcha_code = $additional_params['captcha_code'];
	$captcha_init = "";
	if ( $data["type"] == "RecaptchaV1" ) {
//		$plugin_options['captcha_sitekey'] = '';
		if ( !isset($captcha_options_array["theme"]) ) $captcha_options_array["theme"] = "white";
		$captcha_options = wfu_PHP_array_to_JS_object($captcha_options_array);
		if ( $plugin_options['captcha_sitekey'] != '' ) {
			//before loading recaptcha API, first check if has already been loaded by another plugin or if it is forced by admin to be loaded
			$captcharef_exists = false; 
			if ( WFU_VAR("WFU_CAPTCHA_FORCELOAD_API") != "true" ) {
				global $wp_scripts;
				foreach ( $wp_scripts->registered as $script )
					if ( strpos($script->src, "www.google.com/recaptcha/api/js/recaptcha_ajax.js") !== false ) {
						$captcharef_exists = true;
						break;
					}
			}
			$captcha_init .= "\n".'GlobalData.WFU['.$data["ID"].'].captcha._init0 = GlobalData.WFU['.$data["ID"].'].captcha.init;';
			$captcha_init .= "\n".'GlobalData.WFU['.$data["ID"].'].captcha.init = function() {';
			$captcha_init .= "\n\t".'GlobalData.WFU['.$data["ID"].'].captcha._init0();';
			$captcha_init .= "\n\t".'wfu_captcha_init('.$data["ID"].', "RecaptchaV1", "'.$data["sitekey"].'", '.$captcha_options.', '.( $captcharef_exists ? 'true' : 'false' ).');';
			$captcha_init .= "\n".'}';
		}
	}
	elseif ( $data["type"] == "RecaptchaV1 (no account)" ) {
		$data["frameURL"] = wfu_get_captcha_request_code_url($captcha_code);
	}
	elseif ( $data["type"] == "RecaptchaV2" ) {
		if ( !isset($captcha_options_array["theme"]) ) $captcha_options_array["theme"] = "light";
		$captcha_options = wfu_PHP_array_to_JS_object($captcha_options_array);
		//check if php version supports namespaces
		$php_version = preg_replace("/-.*/", "", phpversion());
		$unsupported = false;
		$ret = wfu_compare_versions($php_version, '5.3.0');
		$unsupported = ( $ret['status'] && $ret['result'] == 'lower' );
		$data["V2unsupported"] = $unsupported;
		if ( !$unsupported && $plugin_options['captcha_sitekey'] != '' ) {
			//before loading recaptcha API, first check if has already been loaded by another plugin or if it is forced by admin to be loaded
			$captcharef_exists = false; 
			if ( WFU_VAR("WFU_CAPTCHA_FORCELOAD_API") != "true" ) {
				global $wp_scripts;
				foreach ( $wp_scripts->registered as $script )
					if ( strpos($script->src, WFU_VAR("WFU_RECAPTCHAV2_HOST")."/recaptcha/api.js") !== false ) {
						$captcharef_exists = true;
						break;
					}
			}
			$captcha_init .= "\n".'GlobalData.WFU['.$data["ID"].'].captcha._init0 = GlobalData.WFU['.$data["ID"].'].captcha.init;';
			$captcha_init .= "\n".'GlobalData.WFU['.$data["ID"].'].captcha.init = function() {';
			$captcha_init .= "\n\t".'GlobalData.WFU['.$data["ID"].'].captcha._init0();';
			$captcha_init .= "\n\t".'GlobalData.WFU['.$data["ID"].'].captcha.host = "'.WFU_VAR("WFU_RECAPTCHAV2_HOST").'"';
			$captcha_init .= "\n\t".'wfu_captcha_init('.$data["ID"].', "RecaptchaV2", "'.$data["sitekey"].'", '.$captcha_options.', '.( $captcharef_exists ? 'true' : 'false' ).');';
			$captcha_init .= "\n".'}';
		}
	}
	elseif ( $data["type"] == "RecaptchaV2 (no account)" ) {
		$data["frameURL"] = wfu_get_captcha_request_code_url($captcha_code);		
	}
	//read html output from template
	$captcha_item += wfu_read_template_output("captcha", $data);
	//initialize captcha object properties
	$captcha_item["js"] = "GlobalData.WFU[".$data["ID"]."].captcha = { ".
		"type: \"".$data["type"]."\",".
		"updateStatus: function(status, param) {},".
		"isLocked: function() { return false; },".
		"core: function() { return null; },".
		"resize: function(width, height) {}".
	"};\n\n".$captcha_item["js"].$captcha_init;
	//append javascript variable that checks if captcha element exists or not
	$captcha_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].captcha_exist = true;";

	return $captcha_item;
}

/**
 * Prepare the Upload Form File List Element.
 *
 * This function prepares the display properties of the file list element of the
 * plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the file list element.
 */
function wfu_prepare_filelist_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['filelist'];
	$data["height"] = $additional_params['heights']['filelist'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["params"] = $params;

	$filelist_item["title"] = 'wordpress_file_upload_filelist_'.$data["ID"];
	$filelist_item["hidden"] = false;
	$filelist_item["width"] = "";
	$filelist_item["object"] = "GlobalData.WFU[".$data["ID"]."].filelist";
	//for responsive plugin adjust container's parent width if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $filelist_item["width"] = $data["width"];
	$filelist_item += wfu_read_template_output("filelist", $data);
	//extract item_template and item_template_empty
	$item_template = "";
	$item_template_line = "";
	$in_item_template_block = false;
	$item_template_empty = "";
	$item_template_empty_line = "";
	$in_item_template_empty_block = false;
	foreach ( $filelist_item as $key => $item ) {
		if ( $in_item_template_block ) {
			unset($filelist_item[$key]);
			if ( $item != "</item_template><item_template_empty>" ) $item_template .= $item."\n";
			else {
				$in_item_template_block = false;
				$in_item_template_empty_block = true;
			}
		}
		elseif ( $in_item_template_empty_block ) {
			unset($filelist_item[$key]);
			if ( $item != "</item_template_empty>" ) $item_template_empty .= $item."\n";
			else $in_item_template_empty_block = false;
		}
		elseif ( substr($key, 0, 4) == "line" ) {
			if ( $item == "<item_template>" ) {
				unset($filelist_item[$key]);
				$in_item_template_block = true;
			}
			elseif ( strpos($item, "[item_template]") !== false ) $item_template_line = $key;
			elseif ( strpos($item, "[item_template_empty]") !== false ) $item_template_empty_line = $key;
		}
	}
	if ( $item_template_line != "" )
		$filelist_item[$item_template_line] = str_replace("[item_template]", wfu_plugin_encode_string(trim($item_template)), $filelist_item[$item_template_line]);
	if ( $item_template_empty_line != "" )
		$filelist_item[$item_template_empty_line] = str_replace("[item_template_empty]", wfu_plugin_encode_string(trim($item_template_empty)), $filelist_item[$item_template_empty_line]);
	//initialize textbox object properties
	$filelist_item["js"] = "GlobalData.WFU[".$data["ID"]."].filelist = { ".
		"setVisibility: function(show) {}, ".
		"visible: function() { return false; }, ".
		"updateList: function(files, is_formupload) {}, ".
		"enableEditing: function(is_formupload) {}, ".
		"disableEditing: function() {}, ".
		"afterUploadStart: function(effect) {}, ".
		"attachCancelHandlers: function(cancel_function) {}, ".
		"dettachCancelHandlers: function() {}, ".
		"updateItemProgress: function(index, position) {}, ".
		"updateTotalProgress: function(position) {}, ".
		"resetTotalProgress: function() {}, ".
		"updateItemStatus: function(index, status) {} ".
	"};\n\n".$filelist_item["js"];
	//append javascript variables, one that checks if progress bar exists or not
	//and another that holds confirmation message for clearing of list
	$filelist_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].filelist_exist = true;";
	$filelist_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].filelist.clear_confirm_message = \"".WFU_CONFIRM_CLEARFILES."\";";

	return $filelist_item;
}

/**
 * Prepare the Upload Form Webcam Element.
 *
 * This function prepares the display properties of the webcam element of the
 * plugin's upload form.
 *
 * @since 3.8.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the webcam element.
 */
function wfu_prepare_webcam_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['webcam'];
	$data["height"] = $additional_params['heights']['webcam'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["params"] = $params;

	$webcam_item["title"] = 'wordpress_file_upload_webcam_'.$data["ID"];
	$webcam_item["hidden"] = false;
	$webcam_item["width"] = "";
	$webcam_item["object"] = "GlobalData.WFU[".$data["ID"]."].webcam";
	
	$webcam_init = "\n".'var wfu_webrtc_ref = document.createElement("SCRIPT");';
	$webcam_init .= "\n".'wfu_webrtc_ref.setAttribute("type", "text/javascript");';
	$webcam_init .= "\n".'wfu_webrtc_ref.setAttribute("src", "https://webrtc.github.io/adapter/adapter-latest.js");';
	$webcam_init .= "\n".'document.getElementById(GlobalData.WFU['.$data["ID"].'].container_id).appendChild(wfu_webrtc_ref);';
	$webcam_init .= "\n".'var wfu_initialize_webcam_loader_'.$data["ID"].' = function() { wfu_initialize_webcam('.$data["ID"].', "'.$params["webcammode"].'", "'.$params["audiocapture"].'", "'.esc_html($params["videowidth"]).'", "'.esc_html($params["videoheight"]).'", "'.esc_html($params["videoaspectratio"]).'", "'.esc_html($params["videoframerate"]).'", "'.$params["camerafacing"].'", '.$params["maxrecordtime"].'); }';
	$webcam_init .= "\n".'if(window.addEventListener) { window.addEventListener("load", wfu_initialize_webcam_loader_'.$data["ID"].', false); } else if(window.attachEvent) { window.attachEvent("onload", wfu_initialize_webcam_loader_'.$data["ID"].'); } else { window["onload"] = wfu_initialize_webcam_loader_'.$data["ID"].'; }';

	//read html output from template
	$webcam_item += wfu_read_template_output("webcam", $data);
	//initialize captcha object properties
	$webcam_item["js"] = "GlobalData.WFU[".$data["ID"]."].webcam = { ".
		"initCallback: function() {},".
		"initButtons: function(mode) {},".
		"updateStatus: function(status) {},".
		"updateButtonStatus: function(status) {},".
		"updateTimer: function(time) {},".
		"updatePlayProgress: function(duration) {},".
		"setVideoProperties: function(props) {},".
		"videoSize: function() { return null; },".
		"readyState: function() { return -1; },".
		"screenshot: function(savefunc, image_type) {},".
		"play: function() {},".
		"pause: function() {},".
		"back: function() {},".
		"fwd: function(duration) {},".
		"ended: function() {}".
	"};\n\n".$webcam_item["js"].$webcam_init;
	//append javascript variable that checks if webcam element exists or not
	$webcam_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].webcam_exist = true;";

	return $webcam_item;
}

/**
 * Prepare the Upload Form Progress Bar Element.
 *
 * This function prepares the display properties of the progress bar element of
 * the plugin's upload form.
 *
 * @since 3.8.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the progress bar element.
 */
function wfu_prepare_progressbar_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['progressbar'];
	$data["height"] = $additional_params['heights']['progressbar'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["params"] = $params;

	$progressbar_item["title"] = 'wordpress_file_upload_progressbar_'.$data["ID"];
	$progressbar_item["hidden"] = ( $params["testmode"] != "true" );
	$progressbar_item["width"] = "";
	$progressbar_item["object"] = "GlobalData.WFU[".$data["ID"]."].progressbar";
	//for responsive plugin adjust container's parent width if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $progressbar_item["width"] = $data["width"];
	//read html output from template
	$progressbar_item += wfu_read_template_output("progressbar", $data);
	//initialize progressbar object properties
	$progressbar_item["js"] = "GlobalData.WFU[".$data["ID"]."].progressbar = { ".
		"show: function(mode) {}, ".
		"hide: function() {}, ".
		"update: function(progress) {} ".
	"};\n\n".$progressbar_item["js"];
	//append javascript variable that checks if progress bar exists or not
	$progressbar_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].progressbar_exist = true;";
	
	return $progressbar_item;
}

/**
 * Prepare the Upload Form Message Element.
 *
 * This function prepares the display properties of the message element of the
 * plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the message element.
 */
function wfu_prepare_message_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['message'];
	$data["height"] = $additional_params['heights']['message'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	
	$header_styles["State0"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE0"));
	$header_styles["State0"]['message'] = WFU_UPLOAD_STATE0;
	$header_styles["State1"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State1"]['message'] = WFU_UPLOAD_STATE1;
	$header_styles["State2"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State2"]['message'] = WFU_UPLOAD_STATE2;
	$header_styles["State3"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State3"]['message'] = WFU_UPLOAD_STATE3;
	$header_styles["State4"] = wfu_prepare_message_colors($params["successmessagecolors"]);
	$header_styles["State4"]['message'] = WFU_UPLOAD_STATE4;
	$header_styles["State5"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State5"]['message'] = WFU_UPLOAD_STATE5;
	$header_styles["State5_singlefile"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE5"));
	$header_styles["State5_singlefile"]['message'] = WFU_UPLOAD_STATE5_SINGLEFILE;
	$header_styles["State6"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State6"]['message'] = WFU_UPLOAD_STATE6;
	$header_styles["State7"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State7"]['message'] = WFU_UPLOAD_STATE7;
	$header_styles["State7_singlefile"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE7"));
	$header_styles["State7_singlefile"]['message'] = WFU_UPLOAD_STATE7_SINGLEFILE;
	$header_styles["State8"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State8"]['message'] = WFU_UPLOAD_STATE8;
	$header_styles["State9"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE9"));
	$header_styles["State9"]['message'] = WFU_UPLOAD_STATE9;
	$header_styles["State10"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State10"]['message'] = WFU_UPLOAD_STATE10;
	$header_styles["State11"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE11"));
	$header_styles["State11"]['message'] = WFU_UPLOAD_STATE11;
	$header_styles["State12"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State12"]['message'] = WFU_UPLOAD_STATE12;
	$header_styles["State13"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE13"));
	$header_styles["State13"]['message'] = WFU_UPLOAD_STATE13;
	$header_styles["State14"] = wfu_prepare_message_colors($params["successmessagecolors"]);
	$header_styles["State14"]['message'] = WFU_UPLOAD_STATE14;
	$header_styles["State15"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State15"]['message'] = WFU_UPLOAD_STATE15;
	$header_styles["State16"] = wfu_prepare_message_colors(WFU_VAR("WFU_HEADERMESSAGECOLORS_STATE16"));
	$header_styles["State16"]['message'] = WFU_UPLOAD_STATE16;
	$header_styles["State17"] = wfu_prepare_message_colors($params["failmessagecolors"]);
	$header_styles["State17"]['message'] = WFU_UPLOAD_STATE17;
	$header_styles["State18"] = wfu_prepare_message_colors($params["successmessagecolors"]);
	$header_styles["State18"]['message'] = WFU_UPLOAD_STATE18;
	$header_styles["State19"] = wfu_prepare_message_colors($params["warningmessagecolors"]);
	$header_styles["State19"]['message'] = WFU_UPLOAD_STATE19;
	$data["header_styles"] = $header_styles;

	$message_item["title"] = 'wordpress_file_upload_message_'.$data["ID"];
	$message_item["hidden"] = false;
	$message_item["width"] = "";
	$message_item["object"] = "GlobalData.WFU[".$data["ID"]."].message";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $message_item["width"] = $data["width"];

//	$message_block = wfu_prepare_message_block_skeleton($sid, $styles, ( $params["testmode"] == "true" ));
//	$message_item = $message_block["msgblock"];

	$message_init = "\n".'if (!GlobalData.States) {';
	$message_init .= "\n\t".'GlobalData.States = {};';
	foreach ( $data["header_styles"] as $state => $statedata )
		$message_init .= "\n\t\t".'GlobalData.States.'.$state.' = { color:"'.$statedata["color"].'", bgcolor:"'.$statedata["bgcolor"].'", borcolor:"'.$statedata["borcolor"].'", message:"'.$statedata["message"].'" };';
	$message_init .= "\n\t".'GlobalData.Colors = {';
	$message_init .= "\n\t\t".'default: "'.WFU_VAR("WFU_DEFAULTMESSAGECOLORS").'".split(","),';
	$message_init .= "\n\t\t".'success: "'.$params['successmessagecolors'].'".split(","),';
	$message_init .= "\n\t\t".'warning: "'.$params['warningmessagecolors'].'".split(","),';
	$message_init .= "\n\t\t".'error: "'.$params['failmessagecolors'].'".split(",")';
	$message_init .= "\n\t".'};';
	$message_init .= "\n".'}';

	//read html output from template
	$message_item += wfu_read_template_output("message", $data);
	//extract header_template and file_template
	$header_template = "";
	$header_template_line = "";
	$in_header_template_block = false;
	$file_template = "";
	$file_template_line = "";
	$in_file_template_block = false;
	foreach ( $message_item as $key => $item ) {
		if ( $in_header_template_block ) {
			unset($message_item[$key]);
			if ( $item != "</header_template><file_template>" ) $header_template .= $item."\n";
			else {
				$in_header_template_block = false;
				$in_file_template_block = true;
			}
		}
		elseif ( $in_file_template_block ) {
			unset($message_item[$key]);
			if ( $item != "</file_template>" ) $file_template .= $item."\n";
			else $in_file_template_block = false;
		}
		elseif ( substr($key, 0, 4) == "line" ) {
			if ( $item == "<header_template>" ) {
				unset($message_item[$key]);
				$in_header_template_block = true;
			}
			elseif ( strpos($item, "[header_template]") !== false ) $header_template_line = $key;
			elseif ( strpos($item, "[file_template]") !== false ) $file_template_line = $key;
		}
	}
	if ( $header_template_line != "" )
		$message_item[$header_template_line] = str_replace("[header_template]", wfu_plugin_encode_string(trim($header_template)), $message_item[$header_template_line]);
	if ( $file_template_line != "" )
		$message_item[$file_template_line] = str_replace("[file_template]", wfu_plugin_encode_string(trim($file_template)), $message_item[$file_template_line]);
	//initialize message object properties
	$message_item["js"] = "GlobalData.WFU[".$data["ID"]."].message = { ".
		"update: function(data) {},".
		"reset: function() {}".
	"};\n\n".$message_item["js"].$message_init;
	//append javascript variable that checks if message block exists or not
	$message_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].message_exist = true;";

	return $message_item;
}

/**
 * Prepare the Upload Form User Data Element.
 *
 * This function prepares the display properties of the user data element of the
 * plugin's upload form.
 *
 * @since 2.1.2
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the user data element.
 */
function wfu_prepare_userdata_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['userdata'];
	$data["height"] = $additional_params['heights']['userdata'];
	$data["width_label"] = $additional_params['widths']['userdata_label'];
	$data["height_label"] = $additional_params['heights']['userdata_label'];
	$data["width_value"] = $additional_params['widths']['userdata_value'];
	$data["height_value"] = $additional_params['heights']['userdata_value'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["props"] = array();
	$data["index"] = $occurrence_index;
	$data["params"] = $params;
	//fill $data["props"] variable with properties of existing userdata fields
	foreach ($params["userdata_fields"] as $userdata_key => $userdata_field) {
		//use only fields belonging to $occurrence_index
		if ( $occurrence_index == 0 || $userdata_field["occurrence"] == $occurrence_index ) {
			$props = $userdata_field;
			$props["key"] = $userdata_key;
			array_push($data["props"], $props);
		}
	}
	
	$userdata_item["title"] = 'wordpress_file_upload_userdata_'.$data["ID"].( $occurrence_index == 0 ? "" : "_".($occurrence_index - 1) );
	$userdata_item["hidden"] = false;
	$userdata_item["width"] = "";
	$userdata_item["object"] = "GlobalData.WFU[".$data["ID"]."].userdata";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $userdata_item["width"] = $data["width"];
	$userdata_item += wfu_read_template_output("userdata", $data);
	//extract templates of field types
	$templates_html = "";
	foreach ( $userdata_item as $key => $item ) {
		if ( substr($key, 0, 4) == "line" ) {
			$templates_html .= ( $templates_html == "" ? "" : "\r\n" ).$item;
			unset($userdata_item[$key]);
		}
	}
	//subclass init() function of userdata object so that it fills WFU.userdata
	//object with properties and code handlers of the userdata fields;
	//the subclassed init() function is carefully written because userdata is a
	//multiplacements component, so init() function will run more than once
	//and every time it runs we need to make sure that only the elements
	//corresponding to the specific $occurrence_index will be initialized
	$init_index = ( $occurrence_index <= 1 ? 0 : $occurrence_index - 1 );
	$userdata_init = "";
	$userdata_init .= "\n".'GlobalData.WFU['.$data["ID"].'].userdata._init'.$init_index.' = GlobalData.WFU['.$data["ID"].'].userdata.init;';
	$userdata_init .= "\n".'GlobalData.WFU['.$data["ID"].'].userdata.init = function() {';
	$userdata_init .= "\n\t".'GlobalData.WFU['.$data["ID"].'].userdata._init'.$init_index.'();';
	$userdata_init .= "\n\t".'var WFU = GlobalData.WFU['.$data["ID"].'];';
	if ( $init_index == 0 ) {
		$userdata_init .= "\n\t".'if (typeof WFU.userdata.init_count == "undefined") {';
		$userdata_init .= "\n\t\t".'WFU.userdata.init_count = 0;';
		$userdata_init .= "\n\t\t".'WFU.userdata.codes = [];';
		$userdata_init .= "\n\t\t".'WFU.userdata.props = [];';
		$userdata_init .= "\n\t".'}';
		$userdata_init .= "\n\t".'else WFU.userdata.init_count ++;';
	}
	$userdata_init .= "\n\t".'if (WFU.userdata.init_count == '.$init_index.') {';
	$i = 1;
	foreach ($params["userdata_fields"] as $userdata_key => $userdata_field) {
		//show only fields belonging to $occurrence_index
		if ( $occurrence_index == 0 || $userdata_field["occurrence"] == $occurrence_index ) {
			$userdata_field["key"] = $userdata_key;
			//get field template
			$template = "";
			$matches = array();
			preg_match("/<userdata_".$userdata_field["key"]."_template>(.*?)<\/userdata_".$userdata_field["key"]."_template>/s", $templates_html, $matches);
			if ( isset($matches[1]) ) $template = $matches[1];
			//generate html code
			foreach ( explode("\r\n", $template) as $line ) $userdata_item["line".$i++] = $line;
			//generate javascript code
			$userdata_init .= "\n\t\t".'WFU.userdata.codes['.$userdata_field["key"].'] = {};';
			$userdata_init .= "\n\t\t".'WFU.userdata.props['.$userdata_field["key"].'] = '.wfu_PHP_array_to_JS_object($userdata_field).';';
			$userdata_init .= "\n\t\t".'WFU.userdata.props['.$userdata_field["key"].'].store = function() { document.getElementById("hiddeninput_'.$data["ID"].'_userdata_'.$userdata_field["key"].'").value = WFU.userdata.codes['.$userdata_field["key"].'].value(); };';
			$userdata_init .= "\n\t\t".'WFU.userdata.props['.$userdata_field["key"].'].getstored = function() { return document.getElementById("hiddeninput_'.$data["ID"].'_userdata_'.$userdata_field["key"].'").value; };';
			$userdata_init .= "\n\t\t".'wfu_init_userdata_handlers('.$data["ID"].', '.$userdata_field["key"].');';
		}
	} 
	$userdata_init .= "\n\t".'}';
	$userdata_init .= "\n".'}';
	//initialize userdata object properties only for the first $occurrence_index
	if ( $init_index == 0 ) {
		$userdata_item["js"] = "GlobalData.WFU[".$data["ID"]."].userdata = { ".
			"initField: function(props) {}, ".
			"attachHandlers: function(props, handlerfunc) {}, ".
			"getValue: function(props) { return ''; }, ".
			"setValue: function(props, value) {}, ".
			"enable: function(props) {}, ".
			"disable: function(props) {}, ".
			"prompt: function(props, message) {}".
		"};\n\n".$userdata_item["js"];
	}
	else $userdata_item["js"] = "";
	$userdata_item["js"] .= $userdata_init;
	//append javascript additional userdata variables only for the first
	//$occurrence_index
	if ( $init_index == 0 ) {
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata_exist = true;";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_empty = \"".WFU_ERROR_USERDATA_EMPTY."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_invalid_number = \"".WFU_ERROR_USERDATANUMBER_INVALID."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_invalid_email = \"".WFU_ERROR_USERDATAEMAIL_INVALID."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_confirm_email_nomatch = \"".WFU_ERROR_USERDATACONFIRMEMAIL_NOMATCH."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_confirm_email_nobase = \"".WFU_ERROR_USERDATACONFIRMEMAIL_NOBASE."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_confirm_password_nomatch = \"".WFU_ERROR_USERDATACONFIRMPASSWORD_NOMATCH."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_confirm_password_nobase = \"".WFU_ERROR_USERDATACONFIRMPASSWORD_NOBASE."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_checkbox_notchecked = \"".WFU_ERROR_USERDATACHECKBOX_NOTCHECKED."\";";
		$userdata_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].userdata.error_radio_notselected = \"".WFU_ERROR_USERDATARADIO_NOTSELECTED."\";";
	}

	return $userdata_item;
}

/**
 * Prepare the Upload Form Consent Element.
 *
 * This function prepares the display properties of the consent element of the
 * plugin's upload form.
 *
 * @since 4.5.0
 *
 * @redeclarable
 *
 * @param array $params The uploader shortcode attributes.
 * @param array $additional_params A list of additional parameters passed to the
 *        function.
 * @param integer $occurrence_index The occurrence index of this element inside
 *        the upload form.
 *
 * @return array The display properties of the consent element.
 */
function wfu_prepare_consent_block($params, $additional_params, $occurrence_index) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//prepare data for template
	$data["ID"] = $params["uploadid"];
	$data["width"] = $additional_params['widths']['consent'];
	$data["height"] = $additional_params['heights']['consent'];
	$data["responsive"] = ( $params["fitmode"] == "responsive" );
	$data["testmode"] = ( $params["testmode"] == "true" );
	$data["index"] = $occurrence_index;
	$data["format"] = $params["consentformat"];
	$data["preselected"] = $params["consentpreselect"];
	$question = preg_replace("/:(\w*):/", "<a href=\"".esc_html($params["consentdisclaimer"])."\">$1</a>", esc_html($params["consentquestion"]));
	$data["question"] = $question;
	$data["params"] = $params;

	$consent_item["title"] = 'wordpress_file_upload_consent_'.$data["ID"];
	$consent_item["hidden"] = ( $params["consentformat"] == "prompt" );
	$consent_item["width"] = "";
	$consent_item["object"] = "GlobalData.WFU[".$data["ID"]."].consent";
	//for responsive plugin adjust container and container's parent widths if a % width has been defined
	if ( $data["responsive"] && strlen($data["width"]) > 1 && substr($data["width"], -1, 1) == "%" ) $consent_item["width"] = $data["width"];
	//read html output from template
	$consent_item += wfu_read_template_output("consent", $data);
	//initialize consent object properties
	$consent_item["js"] = "GlobalData.WFU[".$data["ID"]."].consent = { ".
		"consentCompleted: function() { return false; }, ".
		"attachActions: function(completeaction) {}, ".
		"update: function(action) {} ".
	"};\n\n".$consent_item["js"];
	//append javascript variable that checks if consent exists or not
	$consent_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].consent_exist = true;";
	//append additional consent parameters
	$consent_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].consent.remember_consent = ".( $params["notrememberconsent"] != "true" ? "true" : "false" ).";";
	$consent_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].consent.no_rejects_upload = ".( $params["consentrejectupload"] == "true" ? "true" : "false" ).";";
	$consent_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].consent.consent_format = '".$params["consentformat"]."';";
	$consent_item["js"] .= "\n\nGlobalData.WFU[".$data["ID"]."].consent.consent_question = '".$question."';";

	return $consent_item;
}