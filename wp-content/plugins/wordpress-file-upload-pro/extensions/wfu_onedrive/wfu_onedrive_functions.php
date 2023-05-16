<?php

add_filter('_wfu_get_all_plugin_options', 'wfu_onedrive_get_all_plugin_options', 10, 1);

function wfu_onedrive_get_all_plugin_options($options) {
	return $options;
}

function wfu_onedrive_post_upload_actions($response, $params, $Graph) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$id = ( is_array($response) && isset($response['id']) ? $response['id'] : '' );
	$ret = array(
		'id' => $id,
		'shared' => false,
		'filename' => '',
		'destination' => '',
		'modifiedTime' => 0,
		'thumbnailLink' => '',
		'downloadLink' => '',
		'viewLink' => ''
	);
	if ( $params["share_file"] && $id != '' ) {
		$url = wfu_onedrive_share_file($id, $params, $Graph);
		$ret = array(
			'id' => $id,
			'shared' => ( $url != '' ),
			'filename' => $response['name'],
			'destination' => wfu_basedir($params['destfile']),
			'modifiedTime' => strtotime($response['lastModifiedDateTime']),
			'thumbnailLink' => '',
			'downloadLink' => '',
			'viewLink' => $url
		);
	}
	return $ret;
}

function wfu_onedrive_share_file($fileid, $params, $Graph) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("onedrive_share_file_start:".$fileid);
	$url = '';
	try {
		$request = $Graph->createRequest("POST", ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? '' : '/me' )."/drive/items/".$fileid."/".( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? 'oneDrive.' : '' )."createLink");
		$request->attachBody([ 'type' => 'view', 'scope' => 'anonymous' ]);
		$rawresponse = $request->execute();
		$response = $rawresponse->getBody();
		$url = ( is_array($response) && isset($response['link']) ? $response['link']['webUrl'] : '' );
	}
	catch (Exception $e) {
		wfu_tf_LOG("onedrive_share_file_error: ".$e->getMessage());
	}
	wfu_tf_LOG("onedrive_share_file_end:".$fileid);
	return $url;
}

function wfu_onedrive_simple_upload_file($filepath, $destination, $params, $Graph) {
	wfu_tf_LOG("onedrive_simple_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	wfu_store_service_transfer_data($fileid, $jobid, "onedrive", array( "upload_type" => "simple" ));

	$request = $Graph->createRequest("PUT", ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? '' : '/me' )."/drive/root:".$destination.wfu_basename($filepath).":/content");
	$rawresponse = $request->upload($filepath);
	$response = $rawresponse->getBody();
	
	wfu_tf_LOG("onedrive_simple_transfer_file_end:".$response['id']);
	return $response;
}

function wfu_onedrive_multipart_upload_file($filepath, $destination, $params, $Graph) {
	wfu_tf_LOG("onedrive_simple_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	wfu_store_service_transfer_data($fileid, $jobid, "onedrive", array( "upload_type" => "simple" ));

	$options = array(
		'conflict_policy' => $params["conflict_policy"]
	);
	if ( isset($params["userdata"]) && count($params["userdata"]) > 0 ) $options['userdata'] = $params["userdata"];
	$request = $Graph->createRequest("POST", ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? '' : '/me' )."/drive/root:".$destination.":/children");
	$rawresponse = $request->upload_multipart($filepath, null, $options);
	$response = $rawresponse->getBody();
	
	wfu_tf_LOG("onedrive_simple_transfer_file_end:".$response['id']);
	return $response;
}

function wfu_onedrive_chunked_upload_file($filepath, $destination, $params, $Graph) {
	wfu_tf_LOG("onedrive_chunked_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	$fileSize = wfu_filesize($filepath, "wfu_onedrive_chunked_upload_file");
	//We create a unique ID only valid within this routine to check whether this
	//routine is still transferring the file or another one has gotten control.
	//This way we make sure that only one single routine will be transfering the
	//file.
	$upload_uid = wfu_create_random_string(8);
	//Store unique ID in params to pass it in
	//wfu_onedrive_chunked_upload_file_continue function
	$params["upload_uid"] = $upload_uid;
	$file = new \WFU\WFUGraphSessionFile($filepath, $destination, $params, $Graph);
	$chunkSize = $file->getchunkSize();
	//if this is a resumed upload then resume file object based on uploadURI and
	//calculate $uploaded and $remaining
	if ( isset($params["resumed"]) ) {
		$uploadURI = $params["uploadURI"];
		$file->resume_session($uploadURI);
	}
	//otherwise just get and store uploadURI
	else {
		$file->create_session();
		$uploadURI = $file->getUploadURL();
	}
	$uploaded = $file->getProgress();
	$remaining = $fileSize - $uploaded;
	//if this is a resumed upload then clean $params from the additional values
	if ( isset($params["resumed"]) ) {
		unset($params["resumed"]);
		unset($params["uploadURI"]);
	}
	//We store upload data that are necessary in order to revive and continue
	//the upload in case that this routine times out.
	wfu_store_service_transfer_data($fileid, $jobid, "onedrive", array(
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
	wfu_tf_LOG("onedrive_chunked_transfer_file_end:".$uploadURI);
	return wfu_onedrive_chunked_upload_file_continue($file, $filepath, $params, $upload_uid, $chunkSize, $uploaded, $remaining);
}

function wfu_onedrive_chunked_upload_file_continue($file, $filepath, $params, $upload_uid, $chunkSize, $uploaded, $remaining) {
	wfu_tf_LOG("onedrive_chunked_continued_transfer_file_start:".$filepath);
	$t0 = time();
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	$fileSize = wfu_filesize($filepath, "wfu_onedrive_chunked_upload_file_continue");
	$check = wfu_check_transfer_job($fileid, $jobid, "onedrive");
	$valid = $check["valid"];

	if ( !$valid ) {
		wfu_tf_LOG("onedrive_chunked_continued_transfer_file_invalid");
		return false;
	}

	$finished = false;
	while ( !$finished && $valid ) {
		//send the next chunk
		$status = $file->next_chunk();
		$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "onedrive");
		if ( !$status || !isset($upload_data["upload_uid"]) || $upload_data["upload_uid"] != $upload_uid ) {
			$valid = false;
			break;
		}

		//Update remaining and uploaded
		$finished = $file->is_finished();
		$uploaded = $file->getProgress();
		$remaining = $fileSize - $uploaded;
		$valid = wfu_store_service_transfer_data($fileid, $jobid, "onedrive", array(
			"last_chunk_time" => time(),
			"uploaded"        => $uploaded,
			"remaining"       => $remaining
		));
		$progress = round($uploaded / $fileSize * 100);
		wfu_set_transfer_progress($fileid, $jobid, "onedrive", $progress);
		$dif = time() - $t0;
		wfu_tf_LOG("wfu_heartbeat: ".$dif." uploaded: ".$uploaded);
		//sleep(5);
   }
	
	if ( !$valid ) {
		wfu_tf_LOG("onedrive_chunked_continued_transfer_file_invalid");
		return false;
	}

	wfu_store_service_transfer_data($fileid, $jobid, "onedrive", array( "remaining" => 0 ));
	$metadata = wfu_onedrive_post_upload_actions($file->get_response(), $params, $file->getGraph());
	wfu_set_transfer_result($fileid, $jobid, "onedrive", true, "", $filepath, $metadata);
	wfu_tf_LOG("onedrive_chunked_continued_transfer_file_end");

	return true;
}

function wfu_onedrive_check_upload($fileid, $jobid) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("onedrive_check_file_start:".$fileid);
	$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "onedrive");
	if ( isset($upload_data["upload_type"]) && $upload_data["upload_type"] == "chunked" && $upload_data["remaining"] > 0 ) {
		$since_last_time = time() - $upload_data["last_chunk_time"];
		$timeout = (int)WFU_VAR("WFU_ONEDRIVE_MAX_CHUNKTIME");
		if ( $timeout > -1 && $since_last_time > $timeout ) {
			//we need to resume the upload
			wfu_tf_LOG("onedrive_check_file_resume");
			wfu_tf_LOG("onedrive_check_file_remaining:".$upload_data["remaining"]);
			//retrieve stored parameters
			$filepath = $upload_data["filepath"];
			$destination = wfu_basedir($upload_data["destfile"]);
			$params = unserialize($upload_data["params"]);
			//add resumed item in params to denote that this is a resumed upload
			$params["resumed"] = 1;
			//include uploadURI
			$params["uploadURI"] = $upload_data["uploadURI"];
			//call wfu_onedrive_upload_file again
			return wfu_onedrive_upload_file($filepath, $destination, $params);
		}
	}
}