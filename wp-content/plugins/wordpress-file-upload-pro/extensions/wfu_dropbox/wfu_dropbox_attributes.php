<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_dropbox_uploader_attributes', 10, 1);

function wfu_dropbox_uploader_attributes($attributes) {
	$dropbox_defs = array(
		array(
			"name"		=> "Upload To Dropbox",
			"attribute"	=> "dropbox",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_DROPBOX"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "",
			"dependencies"	=> array("dropboxpath", "dropboxlocal", "dropboxshare"),
			"variables"	=> null,
			"help"		=> "If enabled then the uploaded files will be transferred to the Dropbox account that has been configured in the plugin's settings. Default is false."
		),
		array(
			"name"		=> "Dropbox Path",
			"attribute"	=> "dropboxpath",
			"type"		=> "ltext",
			"validator"	=> "path",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_DROPBOXPATH"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "dropbox",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the path inside the Dropbox account that the uploaded file will be transferred. If the path does not exist it will be created. This attribute accepts variables, just like the uploadpath attribute. Default value is empty (root folder of Dropbox account)."
		),
		array(
			"name"		=> "Local File Action",
			"attribute"	=> "dropboxlocal",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("keep", "delete"),
			"value"		=> WFU_VAR("WFU_DROPBOXLOCAL"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "dropbox",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether to keep or delete the local file stored in the website, after it has been transferred to the Dropbox account. Default value is keep."
		),
		array(
			"name"		=> "Share/List Dropbox File",
			"attribute"	=> "dropboxshare",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_DROPBOXSHARE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "dropbox",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then sharing of Dropbox files will be activated and the files will also be included in front-end/back-end file viewers regardless of whether local copies of the files are preserved or not."
		)
	);
	
	return wfu_insert_attributes($attributes, "interoperability", "", "last", $dropbox_defs);
}