<?php

add_filter('_wfu_adminbrowser_header', 'wfu_gdrive_adminbrowser_header', 10, 1);
add_filter('_wfu_adminbrowser_file_actions', 'wfu_gdrive_adminbrowser_file_actions', 10, 3);

function wfu_gdrive_adminbrowser_header($header_props) {
	//define gdrive params to be passed later on to file action definitions
	$header_props["params"]["gdrive"] = array(
		"active"	=> wfu_gdrive_service_active()
	);
	//define html lines in admin browser header for gdrive if it is active
	if ( $header_props["params"]["gdrive"]["active"] ) {
		$header_props["html_array"] = array_merge($header_props["html_array"], array(
			'<input id="wfu_gdrive_transfer_nonce" type="hidden" value="'.wp_create_nonce('wfu_gdrive_send_file').'" />'
		));
	}
	
	return $header_props;
}

function wfu_gdrive_adminbrowser_file_actions($actions, $file, $params) {
	//add action to transfer file to gdrive for included files if gdrive is
	//active
	if ( $params["gdrive"]["active"] ) {
		$gdrive_is_included_actions = array(
			array(
				'<a id="wfu_send_to_gdrive_'.$file["index"].'_a" href="javascript:wfu_gdrive_send_file(\''.$file["code"].'\', '.$file["index"].');" title="Send this file to Google Drive" disabled="disabled">Send to Google Drive</a>',
				'<img id="wfu_send_to_gdrive_'.$file["index"].'_img" src="'.WFU_IMAGE_ADMIN_SUBFOLDER_LOADING.'" style="width:12px; display:none;" />',
				'<input id="wfu_send_to_gdrive_'.$file["index"].'_inpok" type="hidden" value="File sent to Google Drive" />',
				'<input id="wfu_send_to_gdrive_'.$file["index"].'_inpfail" type="hidden" value="File not sent to Google Drive!" />'
			)
		);
		$actions["is_included"] = array_merge($actions["is_included"], $gdrive_is_included_actions);
	}
	
	return $actions;
}