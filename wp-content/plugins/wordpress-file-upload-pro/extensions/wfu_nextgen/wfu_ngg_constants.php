<?php

add_action('_wfu_globals_uploaderdefaults', 'wfu_nextgen_globals_uploaderdefaults');
add_action('_wfu_globals_additional', 'wfu_nextgen_globals_additional');
add_action('_wfu_after_constants', 'wfu_nextgen_constants');

function wfu_nextgen_globals_uploaderdefaults() {
	$GLOBALS["WFU_GLOBALS"] += array(
		"WFU_NEXTGEN" => array( "Default NextGEN Gallery State", "string", "false", "The default state of adding the uploaded files to a NextGEN Gallery. It can be 'true' or 'false'." ),
		"WFU_NGG_GALLERYID" => array( "Default NGG Gallery ID", "integer", 1, "The default ID of the NextGEN Gallery where uploaded files will be added. It can be apositive integer." ),
		"WFU_NGG_DESCRIPTION" => array( "Default NGG File Description", "string", "", "The default description of the file added to NextGEN Gallery." ),
		"WFU_NGG_ALTTEXT" => array( "Default NGG File Alt Text", "string", "", "The default alt/title text of the file added to NextGEN Gallery." ),
		"WFU_NGG_TAGS" => array( "Default NGG File Tags", "string", "", "The default comma-separated list of tags for the file added to NextGEN Gallery." ),
		"WFU_NGG_EXCLUDE" => array( "Default NGG File Exclude State", "string", "false", "The default state of 'Exclude' property of the file added to NextGEN Gallery. It can be 'true' or 'false'." )
	);
}

function wfu_nextgen_globals_additional() {
	$GLOBALS["WFU_GLOBALS"] += array(
	);
}

function wfu_nextgen_constants() {
}