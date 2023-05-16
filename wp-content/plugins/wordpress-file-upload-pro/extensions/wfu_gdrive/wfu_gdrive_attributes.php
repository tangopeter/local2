<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_gdrive_uploader_attributes', 10, 1);

function wfu_gdrive_uploader_attributes($attributes) {
	$gdrive_defs = array(
		array(
			"name"		=> "Upload To Google Drive",
			"attribute"	=> "gdrive",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_GDRIVE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "",
			"dependencies"	=> array("gdrivepath", "gdriveuserdata", "gdrivelocal", "gdriveshare", "gdriveduplicates"),
			"variables"	=> null,
			"help"		=> "If enabled then the uploaded files will be transferred to the Google Drive account that has been configured in the plugin's settings. Default is false."
		),
		array(
			"name"		=> "Google Drive Path",
			"attribute"	=> "gdrivepath",
			"type"		=> "ltext",
			"validator"	=> "path",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_GDRIVEPATH"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "gdrive",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the path inside the Google Drive account that the uploaded file will be transferred. If the path does not exist it will be created. This attribute accepts variables, just like the uploadpath attribute. Default value is empty (root folder of Google Drive account)."
		),
		array(
			"name"		=> "Include Userdata",
			"attribute"	=> "gdriveuserdata",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_GDRIVEUSERDATA"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "gdrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then the uploaded file's userdata will be included in the transferred Google Drive file description."
		),
		array(
			"name"		=> "Local File Action",
			"attribute"	=> "gdrivelocal",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("keep", "delete"),
			"value"		=> WFU_VAR("WFU_GDRIVELOCAL"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "gdrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether to keep or delete the local file stored in the website, after it has been transferred to the Google Drive account. Default value is keep."
		),
		array(
			"name"		=> "Share/List Google Drive File",
			"attribute"	=> "gdriveshare",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_GDRIVESHARE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "gdrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then sharing of Google Drive files will be activated and the files will also be included in front-end/back-end file viewers regardless of whether local copies of the files are preserved or not."
		),
		array(
			"name"		=> "Trash Duplicates",
			"attribute"	=> "gdriveduplicates",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_GDRIVEDUPLICATES"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "gdrive",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then any files with identical names in Google Drive destination folder will be trashed."
		)
	);
	
	return wfu_insert_attributes($attributes, "interoperability", "", "last", $gdrive_defs);
}