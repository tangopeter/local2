<?php

add_filter('_wfu_adminbrowser_header', 'wfu_onedrive_adminbrowser_header', 10, 1);
add_filter('_wfu_adminbrowser_file_actions', 'wfu_onedrive_adminbrowser_file_actions', 10, 3);

function wfu_onedrive_adminbrowser_header($header_props) {
	//define onedrive params to be passed later on to file action definitions
	$header_props["params"]["onedrive"] = array(
		"active"	=> wfu_onedrive_service_active()
	);
	//define html lines in admin browser header for onedrive if it is active
	if ( $header_props["params"]["onedrive"]["active"] ) {
		$header_props["html_array"] = array_merge($header_props["html_array"], array(
			'<input id="wfu_onedrive_transfer_nonce" type="hidden" value="'.wp_create_nonce('wfu_onedrive_send_file').'" />'
		));
	}
	
	return $header_props;
}

function wfu_onedrive_adminbrowser_file_actions($actions, $file, $params) {
	//add action to transfer file to onedrive for included files if onedrive is
	//active
	if ( $params["onedrive"]["active"] ) {
		$onedrive_is_included_actions = array(
			array(
				'<a id="wfu_send_to_onedrive_'.$file["index"].'_a" href="javascript:wfu_onedrive_send_file(\''.$file["code"].'\', '.$file["index"].');" title="Send this file to Microsoft OneDrive" disabled="disabled">Send to OneDrive</a>',
				'<img id="wfu_send_to_onedrive_'.$file["index"].'_img" src="'.WFU_IMAGE_ADMIN_SUBFOLDER_LOADING.'" style="width:12px; display:none;" />',
				'<input id="wfu_send_to_onedrive_'.$file["index"].'_inpok" type="hidden" value="File sent to OneDrive" />',
				'<input id="wfu_send_to_onedrive_'.$file["index"].'_inpfail" type="hidden" value="File not sent to OneDrive!" />'
			)
		);
		$actions["is_included"] = array_merge($actions["is_included"], $onedrive_is_included_actions);
	}
	
	return $actions;
}