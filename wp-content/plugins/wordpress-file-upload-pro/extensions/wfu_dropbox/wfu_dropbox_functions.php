<?php

add_filter('_wfu_get_all_plugin_options', 'wfu_dropbox_get_all_plugin_options', 10, 1);

function wfu_dropbox_get_all_plugin_options($options) {
	array_push($options,
		//stored Dropbox authorization object 
		array( "wfu_Dropbox_WebAuth", "session", true, false )
	);
	return $options;
}

function wfu_dropbox_post_upload_actions($metadata, $params, $dbxClient, $use_old_API) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$ret = array(
		'id' => ( /*$use_old_API ? $metadata->path :*/ $metadata->getId() ),
		'shared' => false,
		'filename' => '',
		'destination' => '',
		'modifiedTime' => 0,
		'thumbnailLink' => '',
		'downloadLink' => '',
		'viewLink' => ''
	);
	if ( $params["share_file"] ) {
		$url = wfu_dropbox_share_file($ret['id'], $params, $dbxClient, $use_old_API);
		$ret = array(
			'id' => ( /*$use_old_API ? $metadata->path :*/ $metadata->getId() ),
			'shared' => ( $url != '' ),
			'filename' => ( /*$use_old_API ? wfu_basename($metadata->path) :*/ $metadata->getName() ),
			'destination' => ( /*$use_old_API ? wfu_basedir($metadata->path) :*/ wfu_basedir($metadata->getPathDisplay()) ),
			'modifiedTime' => ( /*$use_old_API ? strtotime($metadata->modified) :*/ strtotime($metadata->getServerModified()) ),
			'thumbnailLink' => '',
			'downloadLink' => '',
			'viewLink' => $url
		);
	}
	return $ret;
}

function wfu_dropbox_share_file($fileid, $params, $dbxClient, $use_old_API) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("dropbox_share_file_start:".$fileid);
	$url = '';
	$get_existing_link = false;
	try {
		/*if ( $use_old_API ) {
			$url = $dbxClient->createShareableLink($fileid);
			if ( $url == null ) $url = '';
		}
		else */{
			$response = $dbxClient->postToAPI("/sharing/create_shared_link_with_settings", array( "path" => $fileid ));
			$body = $response->getDecodedBody();
			$url = $body['url'];
		}
	}
	catch (Exception $e) {
		$edec = json_decode($e->getMessage());
		//check if the error mentions that the link already exists; in this case
		//we retrieve it from the error's metadata, or if metadata do not
		//contain the link the we get it using wfu_dropbox_get_shared_link()
		//function
		if ( $edec != null &&
			isset($edec->error) &&
			isset($edec->error->shared_link_already_exists) ) {
			if ( isset($edec->error->shared_link_already_exists->metadata) &&
				isset($edec->error->shared_link_already_exists->metadata->url) &&
				$edec->error->shared_link_already_exists->metadata->url != '' )
				$url = $edec->error->shared_link_already_exists->metadata->url;
			else $get_existing_link = true;
		}
		else wfu_tf_LOG("dropbox_share_file_error: ".$e->getMessage());
	}
	if ( $get_existing_link ) $url = wfu_dropbox_get_shared_link($fileid, $dbxClient);
	wfu_tf_LOG("dropbox_share_file_end:".$fileid);
	return $url;
}

function wfu_dropbox_get_shared_link($fileid, $dbxClient) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	wfu_tf_LOG("dropbox_get_shared_link_start:".$fileid);
	$url = '';
	try {
		$response = $dbxClient->postToAPI("/sharing/list_shared_links", array( "path" => $fileid ));
		$body = $response->getDecodedBody();
		if ( isset($body['links']) && isset($body['links'][0]) && $body['links'][0]['url'] != '' )
			$url = $body['links'][0]['url'];
		if ( $url == '' ) wfu_tf_LOG("dropbox_get_shared_link_error_url_empty: ".json_encode($body));
	}
	catch (Exception $e) { wfu_tf_LOG("dropbox_get_shared_link_error: ".$e->getMessage()); }
	wfu_tf_LOG("dropbox_get_shared_link_end:".$fileid);
	return $url;
}