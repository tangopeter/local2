<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_amazons3_uploader_attributes', 10, 1);

function wfu_amazons3_uploader_attributes($attributes) {
	$amazons3_defs = array(
		array(
			"name"		=> "Upload To Amazon S3",
			"attribute"	=> "amazons3",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_AMAZONS3"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "",
			"dependencies"	=> array("amazons3bucket", "amazons3path", "amazons3userdata", "amazons3local", "amazons3share"),
			"variables"	=> null,
			"help"		=> "If enabled then the uploaded files will be transferred to the Amazon S3 account that has been configured in the plugin's settings. Default is false."
		),
		array(
			"name"		=> "Amazon S3 Bucket",
			"attribute"	=> "amazons3bucket",
			"type"		=> "ltext",
			"validator"	=> "path",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_AMAZONS3BUCKET"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> array("%userdataXXX%"),
			"help"		=> "It defines the Amazon S3 bucket that the uploaded file will be transferred. It must be an existing bucket and have public access enabled. This attribute accepts variables."
		),
		array(
			"name"		=> "Amazon S3 Path",
			"attribute"	=> "amazons3path",
			"type"		=> "ltext",
			"validator"	=> "path",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_AMAZONS3PATH"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the path inside the Amazon S3 bucket that the uploaded file will be transferred. If the path does not exist it will be created. This attribute accepts variables, just like the uploadpath attribute. Default value is empty (root folder of bucket)."
		),
		array(
			"name"		=> "File Access",
			"attribute"	=> "amazons3access",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("private", "public"),
			"value"		=> WFU_VAR("WFU_AMAZONS3ACCESS"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether the trasferred file to Amazon S3 will be private (accessible only by the owner) or public (accessible for view by everyone). Default value is private. Please note that file sharing is affected by this option."
		),
		array(
			"name"		=> "Include Userdata",
			"attribute"	=> "amazons3userdata",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_AMAZONS3USERDATA"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then the uploaded file's userdata will be included in the transferred Amazon S3 file metadata."
		),
		array(
			"name"		=> "Local File Action",
			"attribute"	=> "amazons3local",
			"type"		=> "radio",
			"validator"	=> "text",
			"listitems"	=> array("keep", "delete"),
			"value"		=> WFU_VAR("WFU_AMAZONS3LOCAL"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether to keep or delete the local file stored in the website, after it has been transferred to the Amazon S3 account. Default value is keep."
		),
		array(
			"name"		=> "Share/List Amazon S3 File",
			"attribute"	=> "amazons3share",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_AMAZONS3SHARE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Cloud Services",
			"parent"	=> "amazons3",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If it is enabled then sharing of Amazon S3 files will be activated and the files will also be included in front-end/back-end file viewers regardless of whether local copies of the files are preserved or not. Please note that File Access needs to be public for this to work."
		)
	);
	
	return wfu_insert_attributes($attributes, "interoperability", "", "last", $amazons3_defs);
}