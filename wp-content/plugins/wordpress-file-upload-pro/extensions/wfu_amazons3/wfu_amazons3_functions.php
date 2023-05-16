<?php

add_filter('_wfu_get_all_plugin_options', 'wfu_amazons3_get_all_plugin_options', 10, 1);

function wfu_amazons3_get_all_plugin_options($options) {
	return $options;
}

function wfu_amazons3_simple_upload_file($filepath, $bucket, $destfile, $params, $s3Client) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("amazons3_simple_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array( "upload_type" => "simple" ));
	
	//prepare upload params
	$args = array(
		'Bucket' => $bucket,
		'Key'           => $destfile,
		'ACL'           => ( $params['file_access'] == 'public' ? 'public-read' : 'private' ),
		'SourceFile'    => $filepath
	);
	//add userdata if userdata exist
	if ( isset($params["userdata"]) ) {
		$args['Metadata'] = array();
		foreach ( $params["userdata"] as $item )
			$args['Metadata'][$item["property"]] = $item["value"];
	}
	$args = apply_filters("_wfu_amazons3_putobject_args", $args);
	$response = $s3Client->putObject($args);
	$metadata = wfu_amazons3_post_upload_actions($response, $bucket, $destfile, $params, $s3Client);
	
	wfu_tf_LOG("amazons3_simple_transfer_file_end:");
	return $metadata;
}

function wfu_amazons3_multipart_upload_file($filepath, $bucket, $destfile, $params, $s3Client) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("amazons3_multipart_transfer_file_start:".$filepath);
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	//We create a unique ID only valid within this routine to check whether this
	//routine is still transferring the file or another one has gotten control.
	//This way we make sure that only one single routine will be transfering the
	//file.
	$upload_uid = wfu_create_random_string(8);
	$fileSize = wfu_filesize($filepath, "wfu_amazons3_multipart_upload_file");
	//prepare upload params
	$args = array(
		'bucket' => $bucket,
		'key' => $destfile,
		'acl' => ( $params['file_access'] == 'public' ? 'public-read' : 'private' ),
	);	
	//add userdata if userdata exist
	if ( isset($params["userdata"]) ) {
		$args['params'] = array( 'Metadata' => array() );
		foreach ( $params["userdata"] as $item )
			$args['params']['Metadata'][$item["property"]] = $item["value"];
	}
	$uploadId = null;
	//if this is a resumed upload then resume upload state
	//and then clean $params from the additional values
	if ( isset($params["resumed"]) && isset($params["uploadId"]) && $params["uploadId"] != null ) {
		$uploadId = $params["uploadId"];
		$uploadState = null;
		try {
			$uploadState = Aws\S3\MultipartUploader::getStateFromService($s3Client, $bucket, $destfile, $uploadId);
		}
		catch (Exception $e) {
			wfu_tf_LOG("amazons3_multipart_transfer_file_error: ".$e->getMessage());
		}
		if ( $uploadState != null ) $args['state'] = $uploadState;
		unset($params["resumed"]);
		unset($params["uploadId"]);
	}
	$uploaded = 0;
	if ( $uploadId != null )
		$uploaded = wfu_amazons3_multipart_get_uploaded_size($bucket, $destfile, $uploadId, $s3Client);
	$remaining = $fileSize - $uploaded;
	//We store upload data that are necessary in order to revive and continue
	//the upload in case that this routine times out.
	wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array(
		"upload_type"     => "chunked",
		"upload_uid"      => $upload_uid,
		"filepath"        => $filepath,
		"bucket"          => $bucket,
		"destfile"        => $destfile,
		"fileSize"        => $fileSize,
		"params"          => $params,
		"uploadId"        => $uploadId,
		"uploaded"        => $uploaded,
		"remaining"       => $remaining
	));	
	//filter upload params
	$args = apply_filters("_wfu_amazons3_multipartupload_args", $args);
	//add continue callback
	$args['before_upload'] = function (Aws\Command $command) use ($filepath, $params, $s3Client) {
		wfu_amazons3_multipart_upload_file_continue($filepath, $command['Bucket'], $command['Key'], $params, $command['UploadId'], $s3Client);
    };
	try {
		$uploader = new Aws\S3\MultipartUploader($s3Client, $filepath, $args);
	}
	catch (Exception $e) {
		wfu_tf_LOG("amazons3_multipart_transfer_file_error: ".$e->getMessage());
	}
	
	//perform the multipart upload in a loop, in order to auto-recover from
	//errors
	do {
		try {
			$result = $uploader->upload();
		} catch (Aws\Exception\MultipartUploadException $e) {
			wfu_tf_LOG("amazons3_multipart_transfer_file_error: upload loop");
			$args['state'] = $e->getState();
			$uploadId = $args['state']->getId();
			$uploaded = wfu_amazons3_multipart_get_uploaded_size($bucket, $destfile, $uploadId, $s3Client);
			$remaining = $fileSize - $uploaded;
			wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array(
				"uploadId"        => $uploadId,
				"uploaded"        => $uploaded,
				"remaining"       => $remaining
			));
			$uploader = new Aws\S3\MultipartUploader($s3Client, $source, $args);
		}
	} while (!isset($result));
	
	//complete upload
	wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array( "remaining" => 0 ));
	$metadata = wfu_amazons3_post_upload_actions($result, $bucket, $destfile, $params, $s3Client);
	wfu_set_transfer_result($fileid, $jobid, "amazons3", true, "", $filepath, $metadata);	
	wfu_tf_LOG("amazons3_multipart_transfer_file_end:".$destfile);
	
	return true;
}

function wfu_amazons3_multipart_upload_file_continue($filepath, $bucket, $destfile, $params, $uploadId, $s3Client) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("amazons3_multipart_continued_transfer_file_continue:".$filepath);
	
	gc_collect_cycles();
	
	$t0 = time();
	$fileid = $params["fileid"];
	$jobid = $params["jobid"];
	//first check if this transfer is still valid
	$check = wfu_check_transfer_job($fileid, $jobid, "amazons3");
	$valid = $check["valid"];
	if ( !$valid ) {
		wfu_tf_LOG("amazons3_multipart_continued_transfer_file_invalid");
		$s3Client->abortMultipartUpload($uploadId);
		return false;
	}
	
	$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "amazons3");
	//we do not need to check if this process is valid or another one has taken
	//over; even in the case that another one has taken over, MultipartUploader
	//will handle upload of chunks correctly
	/*$valid = ( isset($upload_data["upload_uid"]) && $upload_data["upload_uid"] == $upload_uid );
	if ( !$valid ) {
		wfu_tf_LOG("amazons3_multipart_continued_transfer_file_invalid");
		return false;
	}*/
	
	//if uploadId is null, which denotes an upload start, then fill it
	if ( $upload_data["uploadId"] == null ) {
		$valid = wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array(
			"uploadId" => $uploadId
		));
		if ( !$valid ) {
			wfu_tf_LOG("amazons3_multipart_continued_transfer_file_invalid");
			$s3Client->abortMultipartUpload($uploadId);
			return false;
		}
	}
	
	$uploaded = wfu_amazons3_multipart_get_uploaded_size($bucket, $destfile, $uploadId, $s3Client);
	$remaining = $upload_data["fileSize"] - $uploaded;
	$valid = wfu_store_service_transfer_data($fileid, $jobid, "amazons3", array(
		"last_chunk_time" => time(),
		"uploaded"        => $uploaded,
		"remaining"       => $remaining
	));
	if ( !$valid ) {
		wfu_tf_LOG("amazons3_multipart_continued_transfer_file_invalid");
		$s3Client->abortMultipartUpload($uploadId);
		return false;
	}

	$progress = round($uploaded / $upload_data["fileSize"] * 100);
	wfu_set_transfer_progress($fileid, $jobid, "amazons3", $progress);
	$dif = time() - $t0;
	wfu_tf_LOG("wfu_heartbeat: ".$dif." uploaded: ".$uploaded);

	return true;
}

function wfu_amazons3_multipart_get_uploaded_size($bucket, $key, $uploadId, $s3Client) {
	$partNumberMarker = 0;
	$totalSize = 0;
	while ( true ) {
		$result = $s3Client->listParts(array(
			'Bucket' => $bucket,
			'Key' => $key,
			'PartNumberMarker' => $partNumberMarker,
			'UploadId' => $uploadId
		));
		if ( isset($result['Parts']) && is_array($result['Parts']) )
			foreach ( $result['Parts'] as $part )
				$totalSize += $part['Size'];
		if ( !$result['IsTruncated'] ) break;
		$partNumberMarker = $result['NextPartNumberMarker'];
	}
	return $totalSize;
}

function wfu_amazons3_post_upload_actions($response, $bucket, $key, $params, $s3Client) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$fullKey = array( "Bucket" => $bucket, "Key" => $key );
	$id = wfu_encode_array_to_string($fullKey);
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
	if ( $params["file_access"] == "public" && $params["share_file"] ) {
		$metadata = $s3Client->headObject($fullKey);
		$ret = array(
			'id' => $id,
			'shared' => true,
			'filename' => wfu_basename($key),
			'destination' => wfu_basedir($key),
			'modifiedTime' => strtotime($metadata['LastModified']),
			'thumbnailLink' => '',
			'downloadLink' => '',
			'viewLink' => $response['ObjectURL']
		);
	}
	return $ret;
}

function wfu_amazons3_check_upload($fileid, $jobid) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("amazons3_check_file_start:".$fileid);
	$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "amazons3");
	if ( isset($upload_data["upload_type"]) && $upload_data["upload_type"] == "chunked" && $upload_data["remaining"] > 0 ) {
		$since_last_time = time() - $upload_data["last_chunk_time"];
		$timeout = (int)WFU_VAR("WFU_AMAZONS3_MAX_CHUNKTIME");
		if ( $timeout > -1 && $since_last_time > $timeout ) {
			//we need to resume the upload
			wfu_tf_LOG("amazons3_check_file_resume");
			wfu_tf_LOG("amazons3_check_file_remaining:".$upload_data["remaining"]);
			//retrieve stored parameters
			$filepath = $upload_data["filepath"];
			$bucket = $upload_data["bucket"];
			$destination = wfu_basedir($upload_data["destfile"]);
			$params = $upload_data["params"];
			//add resumed item in params to denote that this is a resumed upload
			$params["resumed"] = 1;
			//include uploadId
			$params["uploadId"] = $upload_data["uploadId"];
			//call wfu_amazons3_upload_file again
			return wfu_amazons3_upload_file($filepath, $bucket, $destination, $params);
		}
	}
}