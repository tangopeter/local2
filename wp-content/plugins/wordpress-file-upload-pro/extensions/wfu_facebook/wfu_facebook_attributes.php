<?php

add_filter('_wfu_insert_uploader_attributes', 'wfu_facebook_uploader_attributes', 10, 1);

function wfu_facebook_uploader_attributes($attributes) {
	$facebook_defs = array(
		array(
			"name"		=> "Notify by Messenger",
			"attribute"	=> "messenger",
			"type"		=> "onoff",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_MESSENGER"),
			"mode"		=> "commercial",
			"category"	=> "notifications",
			"subcategory"	=> "Messenger Notifications",
			"parent"	=> "",
			"dependencies"	=> array("messengertext", "messengeruploaddetails", "messengerattachfile"),
			"variables"	=> null,
			"help"		=> "If activated then a Messenger message will be sent to inform about successful file uploads."
		),
		array(
			"name"		=> "Messenger Text",
			"attribute"	=> "messengertext",
			"type"		=> "mtext",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_MESSENGERTEXT"),
			"mode"		=> "commercial",
			"category"	=> "notifications",
			"subcategory"	=> "Messenger Notifications",
			"parent"	=> "messenger",
			"dependencies"	=> null,
			"variables"	=> array("%username%", "%useremail%", "%filename%", "%filepath%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%", "%uploaddetails%", "%n%", "%dq%"),
			"help"		=> "Defines the message that Wordpress File Upload facebook app will send to admin's Messenger to notify about new uploads. Can be dynamic by using variables. If %uploaddetails% variable is added in the message, it will be replaced by a link, which displays additional details about the upload when clicked."
		),
		array(
			"name"		=> "Upload Details",
			"attribute"	=> "messengeruploaddetails",
			"type"		=> "mtext",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_MESSENGERUPLOADDETAILS"),
			"mode"		=> "commercial",
			"category"	=> "notifications",
			"subcategory"	=> "Messenger Notifications",
			"parent"	=> "messenger",
			"dependencies"	=> null,
			"variables"	=> array("%username%", "%useremail%", "%filename%", "%filepath%", "%blogid%", "%pageid%", "%pagetitle%", "%userdataXXX%", "%n%", "%dq%"),
			"help"		=> "Defines details about the new upload that will be shown to the recipient if variable %uploaddetails% is included in Messenger Text. Can be dynamic by using variables."
		),
		array(
			"name"		=> "Attach Uploaded Files",
			"attribute"	=> "messengerattachfile",
			"type"		=> "hidden",
			"validator"	=> "text",
			"listitems"	=> null,
			"value"		=> WFU_VAR("WFU_MESSENGERATTACHFILE"),
			"mode"		=> "commercial",
			"category"	=> "notifications",
			"subcategory"	=> "Messenger Notifications",
			"parent"	=> "messenger",
			"dependencies"	=> null,
			"variables"	=> null,
			"help"		=> "If activated, then uploaded files will be included in Messenger message as attachments. Please use carefully."
		)
	);
	
	return wfu_insert_attributes($attributes, "notifications", "", "last", $facebook_defs);
}