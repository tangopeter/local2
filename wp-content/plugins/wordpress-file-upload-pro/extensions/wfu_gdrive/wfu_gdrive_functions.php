<?php

add_filter('_wfu_get_all_plugin_options', 'wfu_gdrive_get_all_plugin_options', 10, 1);

function wfu_gdrive_get_all_plugin_options($options) {
	array_push($options,
		//stored Google Client object 
		array( "wfu_GDrive_Client", "session", true, false )
	);
	return $options;
}

function wfu_gdrive_create_folder($foldername, $parentID, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_transfer_createfolder_start:".$foldername);
	$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'name'		=> $foldername,
		'mimeType'	=> 'application/vnd.google-apps.folder',
		'parents'	=> array($parentID)
	));
	$file = $GService->files->create($fileMetadata, array(
		'fields'	=> 'id'
	));
	wfu_tf_LOG("gdrive_transfer_createfolder_file_ok:".$file->id);
	
	wfu_tf_LOG("gdrive_transfer_createfolder_end");
	return $file->id;
}

function wfu_gdrive_search_file($filename, $parentID, $mimeType, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_transfer_searchfolder_start:".$filename);
	$pageToken = null;
	$folders = array();
	$args = array(
		'q' 		=> "name = '".$filename."' and mimeType = '".$mimeType."' and trashed = false and '".( $parentID == "" ? "root" : $parentID )."' in parents",
		'spaces'	=> 'drive',
		'pageToken'	=> $pageToken,
		'fields'	=> 'nextPageToken, files(id, name)'
	);
	do {
		$response = $GService->files->listFiles($args);
		foreach ( $response->files as $file ) array_push($folders, $file->id);
		$args['pageToken'] = $response->nextPageToken;
	} while ($pageToken != null);
	
	wfu_tf_LOG("gdrive_transfer_searchfolder_end");
	return $folders;
}

function wfu_gdrive_search_folder($foldername, $parentID, $GService) {
	return wfu_gdrive_search_file($foldername, $parentID, 'application/vnd.google-apps.folder', $GService);
}

function wfu_locate_destination_id($destination, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_transfer_locatedest_start:".$destination);
	//remove any leading and trailing slashes from destination
	if ( substr($destination, 0, 1) == '/' ) $destination = substr($destination, 1);
	if ( substr($destination, -1) == '/' ) $destination = substr($destination, 0, -1);
	//split path into individual folders
	$fparts_raw = explode('/', $destination);
	$fparts = array();
	//remove any folders with empty names
	foreach ( $fparts_raw as $partname )
		if ( trim($partname) != '' ) array_push($fparts, $partname);
	//construct chain of folder part ids, including the root folder
	$chain0 = array( 'root' );
	foreach ( $fparts as $partname ) array_push($chain0, '');
	//initialize array that will hold all chains found during search
	$chains = array( $chain0 );
	$ind = 0;
	$pos = 0;
	$deepest_level = 0;
	$deepest_ind = 0;
	//iterate through all folder parts
	while ( $ind < count($fparts) ) {
		$nextpart = $fparts[$ind];
		$newchains = array();
		$chainlength = count($chains);
		//iterate through the added chains
		for ( $i = $pos; $i < $chainlength; $i++ ) {
			//search for contained folder parts within this chain; add then to
			//a new list of chains
			$ids = wfu_gdrive_search_folder($nextpart, $chains[$i][$ind], $GService);
			foreach ( $ids as $id ) {
				$newchain = $chains[$i];
				$newchain[$ind + 1] = $id;
				array_push($newchains, $newchain);
				if ( $ind + 1 > $deepest_level ) {
					$deepest_level = $ind + 1;
					$deepest_ind = $chainlength + count($newchains) - 1;
				}
			}
		}
		if ( count($newchains) == 0 ) break;
		$pos = $chainlength;
		//append the new chains to the existing ones
		$chains = array_merge($chains, $newchains);
		$ind ++;
	}
	$id = $chains[$deepest_ind][$deepest_level];
	//create subfolders for the folder parts that do not exist
	while ( $deepest_level < count($fparts) ) {
		$nextpart = $fparts[$deepest_level];
		$id = wfu_gdrive_create_folder($nextpart, $id, $GService);
		$deepest_level ++;
	}
	
	wfu_tf_LOG("gdrive_transfer_locatedest_end");
	return $id;
}

function wfu_gdrive_trash_duplicates($filename, $parentID, $mimeType, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$duplicates = wfu_gdrive_search_file($filename, $parentID, $mimeType, $GService);
	foreach ( $duplicates as $id ) {
		$filemetadata = new Google_Service_Drive_DriveFile(array( 'trashed' => true ));
		$file = $GService->files->update($id, $filemetadata, array( 'fields' => 'id' ));
	}
}

function wfu_gdrive_generate_filemetadata($filepath, $parentID, $params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$data = array(
		'name'		=> wfu_basename($filepath),
		'parents'	=> array($parentID)
	);
	//add userdata in file description (and optionally in properties) if
	//userdata exist
	if ( isset($params["userdata"]) ) {
		$description = "";
		$props = array();
		foreach ( $params["userdata"] as $item ) {
			$description .= ( $description == "" ? "" : "\n" ).$item["property"].": ".$item["value"];
			$props[$item["property"]] = $item["value"];
		}
		$data["description"] = $description;
		//add userdata also in file's properties, if this is enabled in plugin's
		//Advanced Options
		if ( WFU_VAR("WFU_GDRIVE_USERDATA_INPROPERTIES") == "true" ) $data["properties"] = $props;
	}

	return new Google_Service_Drive_DriveFile($data);
}

function wfu_gdrive_simple_upload_file($filepath, $parentID, $params, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_simple_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	wfu_store_service_transfer_data($fileid, $jobid, "gdrive", array( "upload_type" => "simple" ));

	$fileMetadata = wfu_gdrive_generate_filemetadata($filepath, $parentID, $params);
	$file = $GService->files->create($fileMetadata, array(
		'data' => wfu_file_get_contents($filepath, "wfu_gdrive_simple_upload_file"),
		'mimeType' => wfu_mime_content_type($filepath),
		'uploadType' => 'media',
		'fields' => 'id'
	));
	
	$metadata = wfu_gdrive_post_upload_actions($file->id, $params, $GService);
	wfu_tf_LOG("gdrive_simple_transfer_file_end:".$file->id);
	return $metadata;
}

function wfu_gdrive_post_upload_actions($remote_fileid, $params, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$ret = array(
		'id' => $remote_fileid,
		'shared' => false,
		'filename' => '',
		'destination' => '',
		'modifiedTime' => 0,
		'thumbnailLink' => '',
		'downloadLink' => '',
		'viewLink' => ''
	);
	if ( $params["share_file"] ) {
		wfu_gdrive_share_file($remote_fileid, $params, $GService);
		$metadata = $GService->files->get($remote_fileid, array('fields' => 'id,name,modifiedTime,shared,thumbnailLink,webContentLink,webViewLink'));
		$ret = array(
			'id' => $remote_fileid,
			'shared' => $metadata->shared,
			'filename' => $metadata->name,
			'destination' => wfu_basedir($params['destfile']),
			'modifiedTime' => strtotime($metadata->modifiedTime),
			'thumbnailLink' => $metadata->thumbnailLink,
			'downloadLink' => $metadata->webContentLink,
			'viewLink' => $metadata->webViewLink
		);
	}
	return $ret;
}

function wfu_gdrive_share_file($fileid, $params, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_share_file_start:".$fileid);
	$response = null;
	$args = array(
		'type' => 'anyone',
		'role' => 'reader'
	);
	$args = apply_filters('_wfu_gdrive_share_file_permissions', $args, $fileid, $params);
	$newPermission = new Google_Service_Drive_Permission($args);
	try {
		$response = $GService->permissions->create($fileid, $newPermission);
	}
	catch (Exception $e) { wfu_tf_LOG("gdrive_share_file_error: ".$e->getMessage()); }
	wfu_tf_LOG("gdrive_share_file_end:".$fileid);
	return $response;
}

function wfu_gdrive_chunked_generate_file($filepath, $parentID, $params, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$fileMetadata = wfu_gdrive_generate_filemetadata($filepath, $parentID, $params);
	$request = $GService->files->create($fileMetadata);
	$file = new Google_Http_MediaFileUpload(
		$GService->getClient(),
		$request,
		wfu_mime_content_type($filepath),
		null,
		true,
		WFU_VAR("WFU_GDRIVE_CHUNK_SIZE")
	);
	$file->setFileSize(wfu_filesize($filepath, "wfu_gdrive_chunked_generate_file"));
	
	return $file;
}

function wfu_gdrive_chunked_upload_file($filepath, $parentID, $params, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_chunked_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	$fileSize = wfu_filesize($filepath, "wfu_gdrive_chunked_upload_file");
	$chunkSize = WFU_VAR("WFU_GDRIVE_CHUNK_SIZE");
	//We create a unique ID only valid within this routine to check whether this
	//routine is still transferring the file or another one has gotten control.
	//This way we make sure that only one single routine will be transfering the
	//file.
	$upload_uid = wfu_create_random_string(8);
	//Store unique ID in params to pass it in
	//wfu_gdrive_chunked_upload_file_continue function
	$params["upload_uid"] = $upload_uid;
	$file = wfu_gdrive_chunked_generate_file($filepath, $parentID, $params, $GService);
	//if this is a resumed upload then resume file object based on uploadURI and
	//calculate $uploaded and $remaining
	if ( isset($params["resumed"]) ) {
		$uploadURI = $params["uploadURI"];
		$file->resume($uploadURI);
		$uploaded = $file->getProgress();
	}
	//otherwise just get and store uploadURI
	else {
		$uploadURI = $file->getResumeUri();
		$uploaded = 0;
	}
	$remaining = $fileSize - $uploaded;
	//if this is a resumed upload then clean $params from the additional values
	if ( isset($params["resumed"]) ) {
		unset($params["resumed"]);
		unset($params["uploadURI"]);
	}
	//We store upload data that are necessary in order to revive and continue
	//the upload in case that this routine times out.
	wfu_store_service_transfer_data($fileid, $jobid, "gdrive", array(
		"upload_type"     => "chunked",
		"upload_uid"      => $upload_uid,
		"filepath"        => $filepath,
		"destfile"        => $params["destfile"],
		"fileSize"        => $fileSize,
		"chunkSize"       => $chunkSize,
		"params"          => serialize($params),
		"uploadURI"       => $uploadURI,
		"uploaded"        => $uploaded,
		"remaining"       => $remaining
	));
	wfu_tf_LOG("gdrive_chunked_transfer_file_end:".$uploadURI);
	return wfu_gdrive_chunked_upload_file_continue($file, $filepath, $params, $upload_uid, $chunkSize, $uploaded, $remaining, $GService);
}

function wfu_gdrive_chunked_upload_file_continue($file, $filepath, $params, $upload_uid, $chunkSize, $uploaded, $remaining, $GService) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_chunked_continued_transfer_file_start:".$filepath);
	$t0 = time();
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	$fileSize = wfu_filesize($filepath, "wfu_gdrive_chunked_upload_file_continue");
	$check = wfu_check_transfer_job($fileid, $jobid, "gdrive");
	$valid = $check["valid"];

	if ( !$valid ) {
		wfu_tf_LOG("gdrive_chunked_continued_transfer_file_invalid");
		return false;
	}

	//open source file and move file pointer to correct position
	$f = wfu_fopen($filepath, "rb", "wfu_gdrive_chunked_upload_file_continue");
	fseek($f, $uploaded);
	$status = false;
	while ( !$status && !feof($f) && $valid ) {
		//send the next chunk
		$chunk = fread($f, $chunkSize);
		$status = $file->nextChunk($chunk);
		$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "gdrive");
		if ( !isset($upload_data["upload_uid"]) || $upload_data["upload_uid"] != $upload_uid ) {
			$valid = false;
			break;
		}

		//Update remaining and uploaded
		$uploaded = $file->getProgress();
		$remaining = $fileSize - $uploaded;
		$valid = wfu_store_service_transfer_data($fileid, $jobid, "gdrive", array(
			"last_chunk_time" => time(),
			"uploaded"        => $uploaded,
			"remaining"       => $remaining
		));
		$progress = round($uploaded / $fileSize * 100);
		wfu_set_transfer_progress($fileid, $jobid, "gdrive", $progress);
		$dif = time() - $t0;
		wfu_tf_LOG("wfu_heartbeat: ".$dif." uploaded: ".$uploaded);
		//sleep(5);
   }
   fclose($f);
	
	if ( !$valid ) {
		wfu_tf_LOG("gdrive_chunked_continued_transfer_file_invalid");
		return false;
	}

	wfu_store_service_transfer_data($fileid, $jobid, "gdrive", array( "remaining" => 0 ));
	$GService->getClient()->setDefer(false);
	$metadata = wfu_gdrive_post_upload_actions($status->id, $params, $GService);
	wfu_set_transfer_result($fileid, $jobid, "gdrive", true, "", $filepath, $metadata);
	wfu_tf_LOG("gdrive_chunked_continued_transfer_file_end");

	return true;
}

function wfu_gdrive_check_upload($fileid, $jobid) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("gdrive_check_file_start:".$fileid);
	$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "gdrive");
	if ( isset($upload_data["upload_type"]) && $upload_data["upload_type"] == "chunked" && $upload_data["remaining"] > 0 ) {
		$since_last_time = time() - $upload_data["last_chunk_time"];
		$timeout = (int)WFU_VAR("WFU_GDRIVE_MAX_CHUNKTIME");
		if ( $timeout > -1 && $since_last_time > $timeout ) {
			//we need to resume the upload
			wfu_tf_LOG("gdrive_check_file_resume");
			wfu_tf_LOG("gdrive_check_file_remaining:".$upload_data["remaining"]);
			//retrieve stored parameters
			$filepath = $upload_data["filepath"];
			$destination = wfu_basedir($upload_data["destfile"]);
			$params = unserialize($upload_data["params"]);
			//add resumed item in params to denote that this is a resumed upload
			$params["resumed"] = 1;
			//include uploadURI
			$params["uploadURI"] = $upload_data["uploadURI"];
			//call wfu_gdrive_upload_file again
			return wfu_gdrive_upload_file($filepath, $destination, $params);
		}
	}
}