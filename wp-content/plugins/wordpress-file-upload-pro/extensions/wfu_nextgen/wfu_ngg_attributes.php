<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_nextgen_attributes', 10, 1);

function wfu_nextgen_attributes($attributes) {
	$nextgen_defs = array(
		array(
			"name"		=> "Add Uploaded Files to NextGEN Gallery",
			"attribute"	=> "nextgen",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NEXTGEN"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "",
			"dependencies"	=> array("ngg_galleryid", "ngg_description", "ngg_alttext", "ngg_tags", "ngg_exclude"),
			"variables"	=> null,
			"help"		=> "If enabled then the uploaded files will be added to a NextGEN Gallery. Default is false."
		),
		array(
			"name"		=> "NGG Gallery ID",
			"attribute"	=> "ngg_galleryid",
			"type"		=> "integer",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NGG_GALLERYID"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "nextgen",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines the ID of the NextGEN Gallery where uploaded files will be added."
		),
		array(
			"name"		=> "NGG File Description",
			"attribute"	=> "ngg_description",
			"type"		=> "ltext",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NGG_DESCRIPTION"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "nextgen",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the description of the file added to NextGEN Gallery."
		),
		array(
			"name"		=> "NGG File Alt Text",
			"attribute"	=> "ngg_alttext",
			"type"		=> "ltext",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NGG_ALTTEXT"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "nextgen",
			"dependencies"	=> null,
			"variables"	=> array("%userid%", "%username%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%"),
			"help"		=> "It defines the alt/title text of the file added to NextGEN Gallery."
		),
		array(
			"name"		=> "NGG File Tags",
			"attribute"	=> "ngg_tags",
			"type"		=> "ltext",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NGG_TAGS"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "nextgen",
			"dependencies"	=> null,
			"variables"	=> array("%userdataXXX%"),
			"help"		=> "It defines a comma-separated list of tags for the file added to NextGEN Gallery."
		),
		array(
			"name"		=> "NGG Exclude File",
			"attribute"	=> "ngg_exclude",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_NGG_EXCLUDE"),
			"mode"		=> "commercial",
			"category"	=> "interoperability",
			"subcategory"	=> "Connection With Other Plugins",
			"parent"	=> "nextgen",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "It defines whether the added file in NextGEN Gallery will have its 'Exclude' property active. If it is active then the file will not be shown in Gallery until the admin deactivates it from Dashboard."
		)
	);
	
	return wfu_insert_attributes($attributes, "interoperability", "Connection With Other Plugins", "last", $nextgen_defs);
}