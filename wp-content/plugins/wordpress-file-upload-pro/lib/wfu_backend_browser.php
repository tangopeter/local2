<?php

/**
 * Back-End File Browser Page of Plugin
 *
 * This file contains functions related to Back-End File Browser page of plugin.
 *
 * @link /lib/wfu_backend_browser.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 3.0.0
 */
/**
 * Process Back-End File Browser Page Requests for Non-Admin Users.
 *
 * This function processes back-end file browser page requests and shows the
 * back-nd browser to users that are not admins.
 *
 * @since 3.0.0
 */
function wordpress_file_upload_manage_dashboard_noadmin() {
	//execute check for unfinished files
	wfu_checkdelete_unfinished_files();
	
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$action = (!empty($_POST['action']) ? $_POST['action'] : (!empty($_GET['action']) ? $_GET['action'] : ''));
	$file = (!empty($_POST['file']) ? $_POST['file'] : (!empty($_GET['file']) ? $_GET['file'] : ''));
	$referer = (!empty($_POST['referer']) ? $_POST['referer'] : (!empty($_GET['referer']) ? $_GET['referer'] : ''));
	$sort = (!empty($_POST['sort']) ? $_POST['sort'] : (!empty($_GET['sort']) ? $_GET['sort'] : ''));
	$page = (!empty($_POST['pageid']) ? $_POST['pageid'] : (!empty($_GET['pageid']) ? $_GET['pageid'] : 1));
	$echo_str = "";

	if ( $action == 'uploaded_files_browser' ) {
		$echo_str = wfu_browse_uploaded_files($sort, $page);
	}
	elseif ( $action == 'rename_file' && $file != "" ) {
		$echo_str = wfu_rename_file_prompt($file, 'file', false);
	}
	elseif ( $action == 'renamefile' && $file != "" ) {
		if ( wfu_rename_file($file, 'file') ) $echo_str = wfu_browse_uploaded_files($sort, $page);
		else $echo_str = wfu_rename_file_prompt($file, 'file', true);
	}
	elseif ( $action == 'delete_file' && $file != "" && $referer != "" ) {
		if ( substr($file, 0, 5) == "list:" ) $file = explode(",", substr($file, 5));
		$echo_str = wfu_delete_file_prompt($file, 'file', $referer);
	}
	elseif ( $action == 'deletefile' && $file != "" ) {
		if ( substr($file, 0, 5) == "list:" ) $file = explode(",", substr($file, 5));
		wfu_delete_file($file, 'file');
		$referer_url = wfu_get_filepath_from_safe(wfu_sanitize_code($referer));
		if ( $referer_url === false ) $referer_url = "";
		$match = array();
		preg_match("/\&sort=(.*?)\&/", $referer_url, $match);
		$sort = ( isset($match[1]) ? $match[1] : '' );
		preg_match("/\&pageid=([0-9]*)/", $referer_url, $match);
		$page = ( isset($match[1]) ? $match[1] : 1 );
		$echo_str = wfu_browse_uploaded_files($sort, $page);
	}
	elseif ( $action == 'remove_remfile' && $file != "" && $referer != "" ) {
		if ( substr($file, 0, 5) == "list:" ) $file = explode(",", substr($file, 5));
		$echo_str = wfu_remove_remote_file_prompt($file, $referer);
	}
	elseif ( $action == 'removeremfile' && $file != "" ) {
		if ( substr($file, 0, 5) == "list:" ) $file = explode(",", substr($file, 5));
		wfu_remove_remote_file($file);
		$referer_url = wfu_get_filepath_from_safe(wfu_sanitize_code($referer));
		if ( $referer_url === false ) $referer_url = "";
		$match = array();
		preg_match("/\&sort=(.*?)\&/", $referer_url, $match);
		$sort = ( isset($match[1]) ? $match[1] : '' );
		preg_match("/\&pageid=([0-9]*)/", $referer_url, $match);
		$page = ( isset($match[1]) ? $match[1] : 1 );
		$echo_str = wfu_browse_uploaded_files($sort, $page);
	}
	elseif ( $action == 'file_details' && $file != "" ) {
		$echo_str = wfu_file_details($file, false);
	}
	elseif ( $action == 'edit_filedetails' && $file != "" ) {
		wfu_edit_filedetails($file);
		$echo_str = wfu_file_details($file, false);
	}
	else {
		$echo_str = wfu_browse_uploaded_files($sort, $page);		
	}

	echo $echo_str;
}

/**
 * Display the Back-End File Browser.
 *
 * This function displays the back-end file browser of the plugin.
 *
 * @since 3.0.0
 *
 * @redeclarable
 *
 * @param string $sort The column name to perform sort. If the first character
 *        is a dash (-) then sorting will be done in descending order.
 * @param integer $page Optional. The page to display in case browser contents
 *        are paginated.
 * @param bool $only_table_rows Optional. Return only the HTML code of the table
 *        rows.
 *
 * @return string The HTML output of the plugin's Back-End File Browser page.
 */
function wfu_browse_uploaded_files($sort, $page = 1, $only_table_rows = false) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	// check that user can see the files
	$permissions = wfu_get_current_user_browser_permissions();
	if ( !$permissions['view'] ) return;
	//clean session array holding file paths if it is too big
	if ( WFU_USVAR_exists('wfu_filepath_safe_storage') && count(WFU_USVAR('wfu_filepath_safe_storage')) > WFU_VAR("WFU_PHP_ARRAY_MAXLEN") ) WFU_USVAR_store('wfu_filepath_safe_storage', array());
	//basic variables
	$siteurl = site_url();
	$userid = get_current_user_id();
	//determine if bulk actions will be shown
	$bulkactions_visible = $permissions['delete'];
	//adjust sorting
	if ( $sort == "" ) $sort = 'name';
	if ( substr($sort, 0, 1) == '-' ) $order = SORT_DESC;
	else $order = SORT_ASC;
	//define referer (with sort data) to point to this url for use by the elements
	$referer = $siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=uploaded_files_browser&sort='.$sort.'&pageid='.$page;
	$referer_code = wfu_safe_store_filepath($referer.'[['.$sort.']]');
	//get list of files
	$filelist = array();
	$filerecs = wfu_get_recs_of_user($userid);
	foreach ( $filerecs as $filerec ) {
		$include = false;
		$filepath = wfu_path_rel2abs($filerec->filepath);
		//check if the file is remote; in this case get file properties from
		//remote file metadata
		$service = wfu_check_file_remote($filepath);
		if ( $service !== false && WFU_VAR("WFU_BACKENDBROWSER_SHOWREMOTE") == "true" ) {
			$filedata = wfu_get_filedata_from_rec($filerec);
			$metadata = wfu_get_remote_file_metadata($filedata, $service);
			if ( $metadata != null ) {
				$include = true;
				$filename = $metadata['filename'];
				$fullpath = ( $metadata['downloadLink'] != '' ? $metadata['downloadLink'] : $metadata['viewLink'] );
				$filesize = $filerec->filesize;
				$filedate = $metadata['modifiedTime'];
			}
		}
		elseif ( $service === false ) {
			$include = true;
			$stat = wfu_stat($filepath, "wfu_browse_uploaded_files");
			$filename = wfu_basename($filepath);
			$fullpath = $filepath;
			$filesize = $stat['size'];
			$filedate = $stat['mtime'];
		}
		if ( $include ) {
			array_push($filelist, array( 'name' => $filename, 'fullpath' => $fullpath, 'size' => $filesize, 'mdate' => $filedate, 'filedata' => $filerec, 'remote' => ( $service !== false ) ));
		}
	}
	$filesort = ( substr($sort, -4) == 'date' ? 'mdate' : substr($sort, -4) );
	switch ( $filesort ) {
		case "name": $filesort .= ":s"; break;
		case "size": $filesort .= ":n"; break;
		case "mdate": $filesort .= ":n"; break;
	}
	$filelist = wfu_array_sort($filelist, $filesort, $order);
	$files_total = count($filelist);
	//narrow list if pagination is activated
	if ( WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS") > 0 ) {
		$pages = ceil($files_total / WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS"));
		if ( $page > $pages ) $page = $pages;
		$filelist = array_slice($filelist, ($page - 1) * (int)WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS"), WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS"));
	}
	
	$echo_str = "";
	if ( !$only_table_rows ) {
		$echo_str .= "\n".'<div class="wrap">';
		$echo_str .= "\n\t".'<h2>Uploaded Files Browser</h2>';
		//file browser header
		$echo_str .= "\n\t".'<div style="margin-top:20px; position:relative;">';
		$echo_str .= "\n\t\t".'<input id="wfu_filebrowser_cursort" type="hidden" value="'.$sort.'" />';
		$echo_str .= wfu_add_loading_overlay("\n\t\t", "filebrowser");
		$filebrowser_nonce = wp_create_nonce( 'wfu-filebrowser-page' );
		$echo_str .= "\n\t\t".'<div class="wfu_filebrowser_header" style="width: 100%;">';
		if ( $bulkactions_visible ) {
			$bulkactions = array(
				array( "name" => "delete", "title" => "Delete" ),
				array( "name" => "remove_remote", "title" => "Remove Remote" )
			);
			$echo_str .= wfu_add_bulkactions_header("\n\t\t\t", "filebrowser", $bulkactions);
		}
		if ( WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS") > 0 ) {
			$echo_str .= wfu_add_pagination_header("\n\t\t\t", "filebrowser", $page, $pages, $filebrowser_nonce);
		}
		$echo_str .= "\n\t\t\t".'<input id="wfu_filebrowser_action_url" type="hidden" value="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload" />';
		$echo_str .= "\n\t\t\t".'<input id="wfu_filebrowser_referer" type="hidden" value="'.$referer_code.'" />';
		$echo_str .= "\n\t\t\t".'<input id="wfu_download_file_nonce" type="hidden" value="'.wp_create_nonce('wfu_download_file_invoker').'" />';
		$echo_str .= "\n\t\t".'</div>';
		$echo_str .= "\n\t\t".'<table id="wfu_filebrowser_table" class="widefat">';
		$echo_str .= "\n\t\t\t".'<thead>';
		$echo_str .= "\n\t\t\t\t".'<tr>';
		if ( $bulkactions_visible ) {
			$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="5%" style="text-align:center;">';
			$echo_str .= "\n\t\t\t\t\t\t".'<input id="wfu_select_all_visible" type="checkbox" onchange="wfu_filebrowser_select_all_visible_changed();" style="-webkit-appearance:checkbox;" />';
			$echo_str .= "\n\t\t\t\t\t".'</th>';
		}
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" style="text-align:left;">';
		$sort_param = ( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? '-name' : 'name' ) : 'name' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_filebrowser_page(\''.$filebrowser_nonce.'\', \'sort:'.$sort_param.'\');">Name'.( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" style="text-align:right;">';
		$sort_param = ( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? '-size' : 'size' ) : 'size' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_filebrowser_page(\''.$filebrowser_nonce.'\', \'sort:'.$sort_param.'\');">Size'.( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="20%" style="text-align:left;">';
		$sort_param = ( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? '-date' : 'date' ) : 'date' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_filebrowser_page(\''.$filebrowser_nonce.'\', \'sort:'.$sort_param.'\');">Date'.( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="40%" style="text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>User Data</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$echo_str .= "\n\t\t\t".'</thead>';
		$echo_str .= "\n\t\t\t".'<tbody>';
	}
	
	if ( $only_table_rows ) $echo_str .= "\n\t\t\t\t".'<!-- wfu_filebrowser_referer['.$referer_code.'] -->';
	//show contained files
	$ii = 1;
	foreach ( $filelist as $file ) {
		// store filepath that we need to pass to other functions in session, instead of exposing it in the url
		$file_code = ( $file['remote'] ? 'remote:'.$file['filedata']->idlog : wfu_prepare_to_batch_safe_store_filepath(wfu_path_abs2rel($file['fullpath']).'[['.$sort.']]' ) );
		$fileid = ( $file['filedata'] != null ? $file['filedata']->idlog : "0" );
		$echo_str .= "\n\t\t\t\t".'<tr onmouseover="var actions=document.getElementsByName(\'wfu_file_actions\'); for (var i=0; i<actions.length; i++) {actions[i].style.visibility=\'hidden\';} document.getElementById(\'wfu_file_actions_'.$ii.'\').style.visibility=\'visible\'" onmouseout="var actions=document.getElementsByName(\'wfu_file_actions\'); for (var i=0; i<actions.length; i++) {actions[i].style.visibility=\'hidden\';}">';
		if ( $bulkactions_visible ) {
			$echo_str .= "\n\t\t\t\t\t".'<td width="5%" style="padding: 5px 5px 5px 10px; text-align:center;">';
			$echo_str .= "\n\t\t\t\t\t\t".'<input name="'.$fileid.'" class="wfu_selectors wfu_selcode_'.str_replace(":", "_", $file_code).'" type="checkbox" onchange="wfu_filebrowser_selector_changed(this);" />';
			$echo_str .= "\n\t\t\t\t\t".'</td>';
		}
		$echo_str .= "\n\t\t\t\t\t".'<td width="'.( $bulkactions_visible ? '25' : '30' ).'%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		if ( $file['filedata'] != null )
			$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=file_details&file='.$file_code.'" title="View and edit file details" style="font-weight:normal;">'.$file['name'].'</a>';
		else
			$echo_str .= "\n\t\t\t\t\t\t".'<span>'.$file['name'].'</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'<div id="wfu_file_actions_'.$ii.'" name="wfu_file_actions" style="visibility:hidden;">';
		if ( $file['filedata'] != null ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=file_details&file='.$file_code.'" title="View'.( $permissions['edit'] ? ' and edit' : '' ).' file details">Details</a>';
			if ( $permissions['edit'] || $permissions['delete'] || $permissions['download'] ) $echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		}
		if ( $permissions['edit'] && !$file['remote'] ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=rename_file&file='.$file_code.'" title="Rename this file">Rename</a>';
			if ( ( $permissions['delete'] && !$file['remote'] ) || $permissions['download'] ) $echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		}
		if ( $permissions['delete'] ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
			if ( !$file['remote'] ) $echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=delete_file&file='.$file_code.'&referer='.$referer_code.'" title="Delete this file">Delete</a>';
			else $echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/admin.php?page=wordpress_file_upload&action=remove_remfile&file='.$file_code.'&referer='.$referer_code.'" title="Remove this remote file">Remove Remote</a>';
			if ( $permissions['download'] ) $echo_str .= "\n\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		}
		if ( $permissions['download'] ) {
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<a href="'.( $file['remote'] ? $file['fullpath'] : 'javascript:wfu_download_file(\''.$file_code.'\', '.$ii.');' ).'" title="Download this file">Download</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</span>';
		}
		$echo_str .= "\n\t\t\t\t\t\t".'</div>';
		$echo_str .= "\n\t\t\t\t\t\t".'<div id="wfu_file_download_container_'.$ii.'" style="display: block;"></div>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="10%" style="padding: 5px 5px 5px 10px; text-align:right;">'.$file['size'].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="20%" style="padding: 5px 5px 5px 10px; text-align:left;">'.date("d/m/Y H:i:s", $file['mdate']).'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="30%" style="padding: 5px 5px 5px 10px; text-align:left;">';
		if ( $file['filedata'] != null ) {
			if ( is_array($file['filedata']->userdata) && count($file['filedata']->userdata) > 0 ) {
				$echo_str .= "\n\t\t\t\t\t\t".'<select multiple="multiple" style="width:100%; height:40px; background:none; font-size:small;">';
				foreach ( $file['filedata']->userdata as $userdata )
					$echo_str .= "\n\t\t\t\t\t\t\t".'<option>'.$userdata->property.': '.$userdata->propvalue.'</option>';
				$echo_str .= "\n\t\t\t\t\t\t".'</select>';
			}
		}
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$ii ++;
	}
	//store file paths to safe
	wfu_batch_safe_store_filepaths();
	
	if ( !$only_table_rows ) {
		$echo_str .= "\n\t\t\t".'</tbody>';
		$echo_str .= "\n\t\t".'</table>';
		$echo_str .= "\n\t\t".'<iframe id="wfu_download_frame" style="display: none;"></iframe>';
		$echo_str .= "\n\t".'</div>';
		$echo_str .= "\n".'</div>';
	}

	return $echo_str;
}