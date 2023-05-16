<?php

/**
 * Remote Files Page in Dashboard Area of Plugin
 *
 * This file contains functions related to Remote Files page of plugin's
 * Dashboard area.
 *
 * @link /lib/wfu_admin_remotefiles.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 4.16.0
 */

/**
 * Display the List of Remote Files.
 *
 * This function displays the list of remote files (files located in FTP/SFTP or
 * cloud services).
 *
 * @since 4.16.0
 *
 * @redeclarable
 *
 * @param bool $only_table_rows Optional. Return only the HTML code of the table
 *        rows.
 *
 * @return string The HTML output of the list of remote files.
 */
function wfu_manage_remote_files($sort, $page = 1, $only_table_rows = false, $filter = "all") {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $wpdb;
	
	$siteurl = site_url();
	$table_name1 = $wpdb->prefix . "wfu_log";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !current_user_can( 'manage_options' ) ) return;
	
	//get all remote locations
	$all_locations = wfu_get_transfer_locations();
	$locations = array();
	if ( $filter == "all" ) $locations = $all_locations;
	else {
		$locations0 = explode("_", $filter);
		foreach ( $locations0 as $location )
			if ( isset($all_locations[$location]) )
				$locations[$location] = $all_locations[$location];
	}
	//initialize filters
	$filters = array(
		array(
			"code" => "all",
			"title" => "All",
			"count" => 0,
			"checked" => ( $filter == "all" )
		)
	);
	foreach ( $all_locations as $location => $title ) {
		$filter0 = array(
			"code" => $location,
			"title" => $title,
			"count" => 0,
			"checked" => isset($locations[$location])
		);
		array_push($filters, $filter0);
	}
	
	//adjust sorting
	if ( $sort == "" ) $sort = 'name';
	if ( substr($sort, 0, 1) == '-' ) $order = SORT_DESC;
	else $order = SORT_ASC;
	//define referer (with sort data) to point to this url for use by the
	//elements
	$referer = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=remote_files&sort='.$sort.'&pageid='.$page.'&filter='.$filter;
	$referer_code = wfu_safe_store_filepath($referer);

	//get remote files from database
	$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' WHERE action <> \'other\' AND action <> \'datasubmit\' AND date_to = 0 AND (filepath LIKE \'ftp://%\' OR filepath LIKE \'sftp://%\' OR filepath LIKE \'ftps://%\' OR filepath LIKE \'remote:%\') ORDER BY date_from DESC');
	
	$filelist = array();
	foreach ( $filerecs as $filerec ) {
		$include = false;
		$filepath = wfu_path_rel2abs($filerec->filepath);
		$location = "remote";
		$to_be_included = array();
		if ( substr($filepath, 0, 6) == "ftp://" ) $location = "ftp";
		elseif ( substr($filepath, 0, 7) == "sftp://" ) $location = "sftp";
		elseif ( substr($filepath, 0, 7) == "ftps://" ) $location = "ftps";
		//check if the file is remote from cloud; in this case get file
		//properties from remote file metadata
		$service = wfu_check_file_remote($filepath);
		if ( $service !== false ) {
			$filedata = wfu_get_filedata_from_rec($filerec);
			$metadata = wfu_get_remote_file_metadata_allservices($filedata);
			if ( $metadata != null ) {
				$include = true;
				foreach ( $metadata as $metaservice => $servicedata ) {
					$filename = $servicedata['filename'];
					$destination = $servicedata['destination'];
					if ( substr($destination, -1) != "/" ) $destination .= "/";
					$fullpath = $metaservice.":".$destination.$filename;
					array_push($to_be_included, array(
						'filename' => $filename,
						'fullpath' => $fullpath,
						'location' => $metaservice,
						'metadata' => $servicedata
					));
				}
				//$link = ( $metadata['downloadLink'] != "" ? $metadata['downloadLink'] :  $metadata['viewLink'] );
			}
		}
		elseif ( $service === false ) {
			$include = true;
			array_push($to_be_included, array(
				'filename' => wfu_basename($filepath),
				'fullpath' => wfu_hide_credentials_from_ftpurl($filepath),
				'location' => $location,
				'metadata' => null
			));
		}		
		if ( $include ) {
			//add item in the corresponding filter
			foreach ( $to_be_included as $data ) {
				foreach ( $filters as $key => $item ) {
					if ( $item["code"] == $data['location'] || $item["code"] == "all" ) {
						$filters[$key]["count"]++;
					}
				}
				if ( isset($locations[$data['location']]) ) {
					array_push($filelist, array( 'name' => $data['filename'], 'fullpath' => $data['fullpath'], 'location' => $data['location'], 'size' => $filerec->filesize, 'udate' => $filerec->uploadtime, 'filedata' => $filerec, 'metadata' => $data['metadata'] ));
				}
			}
		}
	}
	$filesort = substr($sort, -4);
	switch ( $filesort ) {
		case "name": $filesort = "name:s"; break;
		case "loct": $filesort = "location:s"; break;
		case "dest": $filesort = "fullpath:s"; break;
		case "size": $filesort = "size:n"; break;
		case "date": $filesort = "udate:n"; break;
	}
	$filelist = wfu_array_sort($filelist, $filesort, $order);
	$files_total = count($filelist);
	//narrow list if pagination is activated
	$maxrows = (int)WFU_VAR("WFU_REMOTEFILES_TABLE_MAXROWS");
	if ( $maxrows > 0 ) {
		$pages = ceil($files_total / $maxrows);
		if ( $page > $pages ) $page = $pages;
		$filelist = array_slice($filelist, ($page - 1) * $maxrows, $maxrows);
	}
	//clean filters list
	foreach ( $filters as $key => $item ) {
		if ( $item["count"] == 0 ) unset($filters[$key]);
	}
	
	$echo_str = "";
	
	if ( !$only_table_rows ) {
		$echo_str .= "\n".'<div class="wrap">';
		$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
		$echo_str .= "\n\t".'<div style="margin-top:20px;">';
		$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Remote");
		$echo_str .= "\n\t".'<h3 style="margin-bottom: 10px;">Remote Files</h3>';
		$echo_str .= "\n\t".'<div style="position:relative;">';
		$echo_str .= "\n\t\t".'<input id="wfu_remotefiles_cursort" type="hidden" value="'.$sort.'" />';
		$echo_str .= "\n\t\t".'<input id="wfu_remotefiles_filter" type="hidden" value="'.$filter.'" />';
		$echo_str .= wfu_add_loading_overlay("\n\t\t", "remotefiles");
		$remotefiles_nonce = wp_create_nonce( 'wfu-remotefiles-page' );
		$echo_str .= "\n\t\t".'<div class="wfu_remotefiles_header" style="width: 100%;">';
		$bulkactions = array(
			array( "name" => "unlink", "title" => "Unlink" )
		);
		$echo_str .= wfu_add_bulkactions_header("\n\t\t\t", "remotefiles", $bulkactions);
		$echo_str .= wfu_add_multifilter_header("\n\t\t\t", "remotefiles", $filters, false);
		if ( $maxrows > 0 ) {
			$echo_str .= wfu_add_pagination_header("\n\t\t\t", "remotefiles", $page, $pages, $remotefiles_nonce);
		}
		$echo_str .= "\n\t\t\t".'<input id="wfu_remotefiles_action_url" type="hidden" value="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" />';
		$echo_str .= "\n\t\t\t".'<input id="wfu_remotefiles_referer" type="hidden" value="'.$referer_code.'" />';
		$echo_str .= "\n\t\t\t".'<input id="wfu_remotefiles_nonce" type="hidden" value="'.$remotefiles_nonce.'" />';
		$echo_str .= "\n\t\t\t".'<input id="wfu_download_file_nonce" type="hidden" value="'.wp_create_nonce('wfu_download_file_invoker').'" />';
		$echo_str .= "\n\t\t".'</div>';
		$echo_str .= "\n\t\t".'<table id="wfu_remotefiles_table" class="wfu-remotefiles wp-list-table widefat fixed striped">';
		$echo_str .= "\n\t\t\t".'<thead>';
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<td width="5%" class="manage-column check-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<input id="wfu_select_all_visible" type="checkbox" />';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column column-primary">';
		$sort_param = ( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? '-name' : 'name' ) : 'name' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_remotefiles_page(\''.$remotefiles_nonce.'\', \'sort:'.$sort_param.'\');">File'.( substr($sort, -4) == 'name' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" class="manage-column">';
		$sort_param = ( substr($sort, -4) == 'loct' ? ( $order == SORT_ASC ? '-loct' : 'loct' ) : 'loct' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_remotefiles_page(\''.$remotefiles_nonce.'\', \'sort:'.$sort_param.'\');">Location'.( substr($sort, -4) == 'loct' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" class="manage-column">';
		$sort_param = ( substr($sort, -4) == 'dest' ? ( $order == SORT_ASC ? '-dest' : 'dest' ) : 'dest' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_remotefiles_page(\''.$remotefiles_nonce.'\', \'sort:'.$sort_param.'\');">Destination Path'.( substr($sort, -4) == 'dest' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
		$sort_param = ( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? '-size' : 'size' ) : 'size' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_remotefiles_page(\''.$remotefiles_nonce.'\', \'sort:'.$sort_param.'\');">Size'.( substr($sort, -4) == 'size' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
		$sort_param = ( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? '-date' : 'date' ) : 'date' );
		$echo_str .= "\n\t\t\t\t\t\t".'<a href="javascript:wfu_goto_remotefiles_page(\''.$remotefiles_nonce.'\', \'sort:'.$sort_param.'\');">Transfer Date'.( substr($sort, -4) == 'date' ? ( $order == SORT_ASC ? ' &uarr;' : ' &darr;' ) : '' ).'</a>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Actions</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$echo_str .= "\n\t\t\t".'</thead>';
		$echo_str .= "\n\t\t\t".'<tbody>';
	}

	if ( $only_table_rows ) $echo_str .= "\n\t\t\t\t".'<!-- wfu_remotefiles_referer['.$referer_code.'] -->';
	//show contained files
	$nopagecode = wfu_safe_store_browser_params('no_referer');
	$defactions = wfu_init_remotefiles_actions();
	$ii = 1;
	foreach ( $filelist as $file ) {
		// store filepath that we need to pass to other functions in user state,
		//instead of exposing it in the url; we include the file ID and the
		//location in the stored path
		$fileid = $file['filedata']->idlog;
		$file_code = wfu_prepare_to_batch_safe_store_filepath($file['filedata']->filepath.'[{'.$fileid.'}](('.$file['location'].'))');
		//prepare actions
		$actions = array();
		$actions['details'] = $defactions['details'];
		$actions['details']['href'] = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file=byID:'.$fileid.'&invoker='.$nopagecode;
		$actions['historylog'] = $defactions['historylog'];
		$actions['historylog']['href'] = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=view_log&invoker='.$fileid;
		$download_href = "";
		if ( $file['metadata'] != null ) {
			$view_href = ( isset($file['metadata']['viewLink']) ? $file['metadata']['viewLink'] : '' );
			if ( $view_href != '' ) {
				$actions['link'] = $defactions['link'];
				$actions['link']['href'] = $view_href;
			}
			$download_href = ( isset($file['metadata']['downloadLink']) ? $file['metadata']['downloadLink'] : '' );
		}
		else {
			$download_href = 'javascript:wfu_download_file(\''.$file_code.'\', '.$ii.');';
		}
		if ( $download_href != '' ) {
			$actions['download'] = $defactions['download'];
			$actions['download']['href'] = $download_href;
		}
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="row" class="check-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<input name="'.$fileid.'" class="wfu_selectors wfu_selcode_'.$file_code.'" type="checkbox"  />';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="File" class="column-primary">';
		$echo_str .= "\n\t\t\t\t\t".$file['name'];
		$echo_str .= "\n\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Location">'.$locations[$file['location']].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Destination Path">'.$file['fullpath'].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Size">'.$file['size'].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Transfer Date">'.date_i18n(get_option('date_format', 'd/m/Y'), strtotime(get_date_from_gmt(date("Y-m-d H:i:s", $file['udate'])))).'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Actions"><div id="wfu_file_download_container_'.$ii.'" style="display: block;"></div>'.wfu_render_remotefiles_actions($actions).'</td>';
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

/**
 * Get All Remote Transfer Locations.
 *
 * This function returns all remote transfer locations (FTP and cloud).
 *
 * @since 4.16.0
 *
 * @redeclarable
 *
 * @return string An associative array of all remote locations.
 */
function wfu_get_transfer_locations() {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$locations = array( "ftp" => "FTP", "sftp" => "sFTP", "ftps" => "FTPS" );
	/**
	 * Get List of All Transfer Services.
	 *
	 * This filter must be implemented by all transfer services so that the
	 * plugin knows which are available.
	 *
	 * @since 4.1.0
	 *
	 * @param array $services An array of strings. Each string is a service
	 *        name. Every service must add its own name in the array.
	*/
	$services = apply_filters("_wfu_filetransfer_services", array());
	foreach ( $services as $service )
		$locations[$service] = wfu_service_get_name($service);
	
	return $locations;
}

/**
 * Generate Default List of Actions of a Remote File.
 *
 * This function generates the list of default actions of a remote file. Each
 * action has an icon, a title (when the mouse hovers over the icon) and a link
 * URL (the action itself).
 *
 * @since 4.16.0
 *
 * @return array An array of default properties of a remote file.
 */
function wfu_init_remotefiles_actions() {
	$def_actions["details"] = array(
		"icon"		=> "dashicons-info",
		"title"		=> "View file details",
		"href"		=> "",
		"newtab"	=> true,
		"color"		=> "default"
	);
	$def_actions["historylog"] = array(
		"icon"		=> "dashicons-backup",
		"title"		=> "Locate file record in View Log",
		"href"		=> "",
		"newtab"	=> true,
		"color"		=> "default"
	);
	$def_actions["link"] = array(
		"icon"		=> "dashicons-external",
		"title"		=> "Open file link",
		"href"		=> "",
		"newtab"	=> true,
		"color"		=> "default"
	);
	$def_actions["download"] = array(
		"icon"		=> "dashicons-download",
		"title"		=> "Download file",
		"href"		=> "",
		"newtab"	=> false,
		"color"		=> "default"
	);
	
	//get visible actions and their order
	$actions = array();
	foreach ( $def_actions as $action => $props ) $actions[$action] = $props;
	
	return $actions;
}

/**
 * Display Actions of a Remote File.
 *
 * This function generates the HTML code of the actions of a remote file that
 * will be shown in Actions column.
 *
 * @since 4.16.0
 *
 * @redeclarable
 *
 * @param array $actions The actions of the remote file.
 *
 * @return string The HTML code of the actions of a remote file.
 */
function wfu_render_remotefiles_actions($actions) {
	$a = func_get_args(); switch(WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out)) { case 'X': break; case 'R': return $out; break; case 'D': die($out); break; }
	$i = 0;
	$echo_str = "";
	foreach ( $actions as $key => $action ) {
		$iconclass = $action['icon'];
		$title = $action['title'];
		$echo_str .= '<a class="dashicons '.$iconclass.( $i == 0 ? '' : ' wfu-dashicons-after' ).'" href="'.$action['href'].'" target="'.( !isset($action['newtab']) || $action['newtab'] ? '_blank' : '_self' ).'" title="'.$title.'"'.( isset($action['color']) && $action['color'] != 'default' ? ' style="color:'.$action['color'].';"' : '' ).'></a>';
		$i ++;
	}
	
	return $echo_str;
}

/**
 * Confirm Unlinking of Remote File.
 *
 * This function shows a page to confirm unlinking of a remote file.
 *
 * @since 4.16.0
 *
 * @param string $file_code A code corresponding to the file to be unlinked.
 * @param string $referer The page that initiated the unlinking of the file.
 * @param string $nonce A nonce to check for validity of the operation.
 *
 * @return string The HTML code of the confirmation page.
 */
function wfu_unlink_remotefile_prompt($file_code, $referer, $nonce) {
	$siteurl = site_url();

	if ( !wp_verify_nonce($nonce, 'wfu-remotefiles-page') ) return;
	
	if ( !current_user_can( 'manage_options' ) ) return;

	if ( !is_array($file_code) ) $file_code = array( $file_code );
	$locations = wfu_get_transfer_locations();
	$names = array();
	foreach ( $file_code as $index => $code ) {
		$file_code[$index] = wfu_sanitize_code($code);
		$dec_file = wfu_get_filepath_from_safe($file_code[$index]);
		if ( $dec_file === false ) unset($file_code[$index]);
		else {
			$parts = wfu_extract_sortdata_from_path($dec_file);
			$path_parts = pathinfo($parts['path']);
			array_push($names, $path_parts['basename'].' (stored in '.$locations[$parts['filter']].')');
		}
	}
	if ( count($file_code) == 0 ) return;
	$file_code_list = "list:".implode(",", $file_code);

	$referer_url = wfu_get_filepath_from_safe(wfu_sanitize_code($referer));

	$echo_str = "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$referer_url.'" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px;">Unlink Remote File'.( count($names) == 1 ? '' : 's' ).'</h2>';
	$echo_str .= "\n\t".'<form enctype="multipart/form-data" name="unlinkremotefile" id="unlinkremotefile" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" class="validate">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="action" value="unlinkremotefile">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="referer" value="'.$referer.'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="nonce" value="'.$nonce.'">';
	$echo_str .= "\n\t\t".'<input type="hidden" name="file" value="'.$file_code_list.'">';
	if ( count($names) == 1 )
		$echo_str .= "\n\t\t".'<label>Are you sure that you want to unlink file <strong>'.$names[0].'</strong>?</label><br/>';
	else {
		$echo_str .= "\n\t\t".'<label>Are you sure that you want to unlink files:';
		$echo_str .= "\n\t\t".'<ul style="padding-left: 20px; list-style: initial;">';
		foreach ( $names as $name )
			$echo_str .= "\n\t\t\t".'<li><strong>'.$name.'</strong></li>';
		$echo_str .= "\n\t\t".'</ul>';
	}
	$echo_str .= "\n\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Unlink">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Cancel">';
	$echo_str .= "\n\t\t".'</p>';
	$echo_str .= "\n\t".'</form>';
	$echo_str .= "\n".'</div>';
	return $echo_str;
}

/**
 * Execute Unlinking of Remote File.
 *
 * This function unlinks a remote file.
 *
 * @since 4.16.0
 *
 * @param string $file_code A code corresponding to the file to be unlinked.
 * @param string $nonce A nonce to check for validity of the operation.
 */
function wfu_unlink_remotefile($file_code, $nonce) {
	if ( !wp_verify_nonce($nonce, 'wfu-remotefiles-page') ) return;
	
	if ( !current_user_can( 'manage_options' ) ) return;
	$user = wp_get_current_user();

	if ( !is_array($file_code) ) $file_code = array( $file_code );
	$dec_files = array();
	foreach ( $file_code as $index => $code ) {
		$file_code[$index] = wfu_sanitize_code($code);
		$dec_file = wfu_get_filepath_from_safe($file_code[$index]);
		if ( $dec_file !== false ) {
			$dec_file = wfu_path_rel2abs($dec_file);
			array_push($dec_files, $dec_file);
		}
	}
	if ( count($dec_files) == 0 ) return;

	if ( isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Unlink" ) {
			foreach ( $dec_files as $dec_file ) {
				$ret = wfu_extract_sortdata_from_path($dec_file);
				$dec_file = $ret['path'];
				$retid = $ret['id'];
				if ( $retid != '' && ( $location = $ret['filter'] ) != '' && ( $filerec = wfu_get_file_rec_from_id($retid) ) != null ) {
					//we need to check if there are any linked remote files
					//after removal of this one
					$remaining_links = 0;
					$must_be_renamed = false;
					$filepath_service = wfu_check_file_remote($dec_file);
					$new_filepath_service = "";
					$new_filepath_metadata = null;
					$filedata = wfu_get_filedata_from_rec($filerec);
					$metadata = wfu_get_remote_file_metadata_allservices($filedata);
					if ( $metadata != null && is_array($metadata) ) {
						foreach ( $metadata as $metaservice => $servicedata ) {
							//we want to remove the service matching $location
							if ( $metaservice == $location ) {
								//if the service name matches the one in the
								//filepath then we need to rename the filepath
								$must_be_renamed = ( $metaservice == $filepath_service );
								unset($filedata[$metaservice]);
							}
							else {
								$remaining_links++;
								//if this is the first remaining link then we
								//may need to rename the filepath based on this
								//service
								if ( $remaining_links == 1 ) {
									$new_filepath_service = $metaservice;
									$new_filepath_metadata = $servicedata;
								}
							}
						}
					}
					//in case there are no remaining links then we need to
					//delete the file from the database
					if ( $remaining_links == 0 ) {
						wfu_log_action('delete', $dec_file, $user->ID, '', 0, 0, '', null, $filerec, false);
					}
					//in case there are remaining links the we do not delete the
					//file; we just rename the file if necessary and remove this
					//service from its filedata
					else {
						if ( $must_be_renamed ) {
							$retid = wfu_log_action('rename:remote:'.$new_filepath_service.':'.$new_filepath_metadata["destination"].$new_filepath_metadata["filename"], $dec_file, $user->ID, '', 0, 0, '', null, $filerec, false);
						}
						wfu_save_filedata_from_id($retid, $filedata, false);						
					}
				}
			}
		}
	}
}