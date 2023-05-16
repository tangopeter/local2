<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_onedrive_uploader_attributes', 10, 1);

function wfu_onedrive_uploader_attributes($attributes) {
	$onedrive_defs = array(
		array(
			"name"		=> "Upload To Microsoft OneDrive",
			"attribute"	=> "onedrive",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_ONEDRIVE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "",
			"dependencies"	=> array("onedrivepath", "onedriveuserdata", "onedrivelocal", "onedriveshare", "onedriveconflicts"),
			"variables"	=> null,
			"help"		=> "If enabled then the uploaded files will be transferred to the Microsoft OneDrive account that has been configured in the plugin's settings. Default is false."
		),
		array(
			"name"		=> "OneDrive Path",
			"attribute"	=> "onedrivepath",
			"type"		=> "ltext",
			"validator"	=> "path",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_ONEDRIVEPATH"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "onedrive",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the path inside the Microsoft OneDrive account that the uploaded file will be transferred. If the path does not exist it will be created. This attribute accepts variables, just like the uploadpath attribute. Default value is empty (root folder of Microsoft OneDrive account)."
		),
		array(
			"name"		=> "Include Userdata",
			"attribute"	=> "onedriveuserdata",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_ONEDRIVEUSERDATA"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "onedrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then the uploaded file's userdata will be included in the transferred Microsoft OneDrive file description."
		),
		array(
			"name"		=> "Local File Action",
			"attribute"	=> "onedrivelocal",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("keep", "delete"),
			"value"		=> WFU_VAR("WFU_ONEDRIVELOCAL"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "onedrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether to keep or delete the local file stored in the website, after it has been transferred to the Microsoft OneDrive account. Default value is keep."
		),
		array(
			"name"		=> "Share/List OneDrive File",
			"attribute"	=> "onedriveshare",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_ONEDRIVESHARE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "onedrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then sharing of OneDrive files will be activated and the files will also be included in front-end/back-end file viewers regardless of whether local copies of the files are preserved or not."
		),
		array(
			"name"		=> "Conflict Policy",
			"attribute"	=> "onedriveconflict",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("fail", "replace", "rename"),
			"value"		=> WFU_VAR("WFU_ONEDRIVECONFLICT"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "onedrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines what happens if a file with the same name already exists at Microsoft OneDrive destination. It can either fail, or replace the existing file or be renamed."
		)
	);
	
	return wfu_insert_attributes($attributes, "interoperability", "", "last", $onedrive_defs);
}