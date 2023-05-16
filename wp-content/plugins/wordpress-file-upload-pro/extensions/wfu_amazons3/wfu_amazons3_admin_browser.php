<?php

add_filter('_wfu_adminbrowser_header', 'wfu_amazons3_adminbrowser_header', 10, 1);
add_filter('_wfu_adminbrowser_file_actions', 'wfu_amazons3_adminbrowser_file_actions', 10, 3);

function wfu_amazons3_adminbrowser_header($header_props) {
	//define amazons3 params to be passed later on to file action definitions
	$header_props["params"]["amazons3"] = array(
		"active"	=> wfu_amazons3_service_active()
	);
	//define html lines in admin browser header for amazons3 if it is active
	if ( $header_props["params"]["amazons3"]["active"] ) {
		$header_props["html_array"] = array_merge($header_props["html_array"], array(
			'<input id="wfu_amazons3_transfer_nonce" type="hidden" value="'.wp_create_nonce('wfu_amazons3_send_file').'" />'
		));
	}
	
	return $header_props;
}

function wfu_amazons3_adminbrowser_file_actions($actions, $file, $params) {
	//add action to transfer file to amazons3 for included files if amazons3 is
	//active
	if ( $params["amazons3"]["active"] ) {
		$amazons3_is_included_actions = array(
			array(
				'<a id="wfu_send_to_amazons3_'.$file["index"].'_a" href="javascript:wfu_amazons3_send_file(\''.$file["code"].'\', '.$file["index"].');" title="Send this file to Amazon S3" disabled="disabled">Send to Amazon S3</a>',
				'<img id="wfu_send_to_amazons3_'.$file["index"].'_img" src="'.WFU_IMAGE_ADMIN_SUBFOLDER_LOADING.'" style="width:12px; display:none;" />',
				'<input id="wfu_send_to_amazons3_'.$file["index"].'_inpok" type="hidden" value="File sent to Amazon S3" />',
				'<input id="wfu_send_to_amazons3_'.$file["index"].'_inpfail" type="hidden" value="File not sent to Amazon S3!" />'
			)
		);
		$actions["is_included"] = array_merge($actions["is_included"], $amazons3_is_included_actions);
	}
	
	return $actions;
}