<?php

add_filter('_wfu_adminbrowser_header', 'wfu_dropbox_adminbrowser_header', 10, 1);
add_filter('_wfu_adminbrowser_file_actions', 'wfu_dropbox_adminbrowser_file_actions', 10, 3);

function wfu_dropbox_adminbrowser_header($header_props) {
	//define dropbox params to be passed later on to file action definitions
	$header_props["params"]["dropbox"] = array(
		"active"	=> wfu_dropbox_service_active()
	);
	//define html lines in admin browser header for Dropbox if it is active
	if ( $header_props["params"]["dropbox"]["active"] ) {
		$header_props["html_array"] += array(
			'<input id="wfu_dropbox_transfer_nonce" type="hidden" value="'.wp_create_nonce('wfu_dropbox_send_file').'" />'
		);
	}
	
	return $header_props;
}

function wfu_dropbox_adminbrowser_file_actions($actions, $file, $params) {
	//add action to transfer file to Dropbox for included files if Dropbox is
	//active
	if ( $params["dropbox"]["active"] ) {
		$dropbox_is_included_actions = array(
			array(
				'<a id="wfu_send_to_dropbox_'.$file["index"].'_a" href="javascript:wfu_dropbox_send_file(\''.$file["code"].'\', '.$file["index"].');" title="Send this file to Dropbox" disabled="disabled">Send to Dropbox</a>',
				'<img id="wfu_send_to_dropbox_'.$file["index"].'_img" src="'.WFU_IMAGE_ADMIN_SUBFOLDER_LOADING.'" style="width:12px; display:none;" />',
				'<input id="wfu_send_to_dropbox_'.$file["index"].'_inpok" type="hidden" value="File sent to Dropbox" />',
				'<input id="wfu_send_to_dropbox_'.$file["index"].'_inpfail" type="hidden" value="File not sent to Dropbox!" />'
			)
		);
		$actions["is_included"] = array_merge($actions["is_included"], $dropbox_is_included_actions);
	}
	
	return $actions;
}