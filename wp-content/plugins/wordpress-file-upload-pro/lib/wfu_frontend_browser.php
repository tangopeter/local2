<?php

/**
 * Front-End File Browser of Plugin
 *
 * This file contains functions related to front-end file browser (or front-end
 * file viewer) of the plugin.
 *
 * @link /lib/wfu_frontend_browser.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 3.1.0
 */
/**
 * Generate the HTML code of front-end file browser.
 *
 * It receives the processed attributes of a front-end file browser shortcode
 * and returns the HTML code of the generated file browser.
 *
 * @since 3.1.0
 *
 * @global object $post The current post
 *
 * @param array $incomingfromhandler An associative array of shortcode
 *        attributes (array keys) and their values (array values).
 *
 * @return string The HTML code of the generated upload form
 */
function wordpress_file_upload_browser_function($incomingfromhandler) {
	global $post;
	$shortcode_tag = 'wordpress_file_upload_browser';
	$params = wfu_plugin_parse_array($incomingfromhandler);
	//sanitize params
	$params = wfu_sanitize_shortcode_array($params, $shortcode_tag);
	//add postid information in browser
	$params["currentpostid"] = $post->ID;
	return wordpress_file_upload_render_browser($params);
}

/**
 * Display the Front-End File Browser.
 *
 * This function displays the front-end file browser of the plugin.
 *
 * @since 3.8.5
 *
 * @redeclarable
 *
 * @global object $post The current post
 * @global array $WFU_BLOCK_INLINE_JS Keeps blocks of inline Javascript code.
 *
 * @param array $params The shortcode attributes of the front-end file browser.
 * @param integer $page Optional. The page to display in case browser contents
 *        are paginated.
 * @param bool $only_table_rows Optional. Return only the HTML code of the table
 *        rows.
 * @param string|array $filters Optional. {
 *        An array of search filters to apply on the files.
 *
 *        @type array $filter {
 *              An individual search filter.
 *
 *              @type array $colfilters {
 *                    An array of column filters of this search filter.
 *
 *                    @type array $colfilter {
 *                          An individual column filter.
 *
 *                          @type string $colname The name of the column.
 *                          @type string $mode The matching mode. Can be
 *                                'strict', 'wildcard', 'fuzzy' or 'loose'.
 *                          @type string $value The column value to match.
 *                    }
 *              }
 *        }
 * }
 *
 * @return string The HTML output of the plugin's Front-End File Browser page.
 */
function wordpress_file_upload_render_browser($params, $page = 0, $sort = "", $only_table_rows = false, $filters = "") {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $post;
	global $WFU_BLOCK_INLINE_JS;
	$shortcode_tag = 'wordpress_file_upload_browser';
	$user = wp_get_current_user();
	$is_admin = current_user_can( 'manage_options' );
	$can_open_composer = ( WFU_VAR("WFU_SHORTCODECOMPOSER_NOADMIN") == "true" && ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) ) );
	$can_open_composer = ( $is_admin || $can_open_composer );
	/** This filter is documented in wfu_loader.php */
	$can_open_composer = apply_filters("_wfu_can_open_composer", $can_open_composer, $params);
	
	//check if user is allowed to view plugin, otherwise do not generate it
	$browserroles = explode(",", $params["browserrole"]);
	foreach ( $browserroles as $key => $browserrole ) {
		$browserroles[$key] = trim($browserrole);
	}
	$plugin_browser_user_role = wfu_get_user_role($user, $browserroles);		
	if ( $plugin_browser_user_role == 'nomatch' ) return;
	//check if user is included in uploaduser list
	$browserusers = explode(",", $params["browseruser"]);
	foreach ( $browserusers as $key => $browseruser ) {
		$browserusers[$key] = trim($browseruser);
	}
	if ( !( ( $user->ID > 0 && ( $plugin_browser_user_role == 'administrator' || in_array('all', $browserusers) || in_array($user->user_login, $browserusers) ) ) || ( $user->ID == 0 && in_array('guests', $browserusers) ) ) ) return;
	
	//read the columns that will be shown in the table
	$coldefs_adjusted = explode(",", preg_replace("/(\*?)(.*?)(:[^\/,]*)?(\/[^\/,]*)?(\/[^\/,]*)(,|$)/", "$2$3$5$6", WFU_VAR("WFU_FRONTENDBROWSER_COLUMN_DEFS")));
	$coldefs_flat = explode(",", preg_replace("/(:|\/).*?(,|$)/", "$2", WFU_VAR("WFU_FRONTENDBROWSER_COLUMN_DEFS")));
	$cols = array();
	$cols_raw = array();
	$cols_flat = array();
	if ( trim($params["columns"]) != "" ) {
		$cols_raw = explode(",", trim($params["columns"]));
		$cols_flat = explode(",", preg_replace("/(:|\/).*?(,|$)/", "$2", trim($params["columns"])));
	}
	//make sure that mandatory columns are included
	$index_pos = 0;
	foreach ( $coldefs_flat as $ind => $coldef_name ) {
		if ( substr($coldef_name, 0, 1) == "*" ) {
			$coldef_name = substr($coldef_name, 1);
			$coldefs_flat[$ind] = $coldef_name;
			if ( !in_array($coldef_name, $cols_flat) ) {
				array_splice($cols_raw, $index_pos, 0, array($coldefs_adjusted[$ind]));
				$index_pos++;
			}
		}
	}
	//read the sortable columns
	$defaultsort = "";
	foreach ( $cols_raw as $ind => $col_raw ) {
		$col_parts = array();
		preg_match("/(.*?)(:[^\/]*)?(\/[^\/,]*)?($)/", $col_raw, $col_parts);
		$flat_name = $col_parts[1];
		if ( substr($flat_name, 0, 6) == "custom" ) $flat_name = "custom";
		$defind = array_search($flat_name, $coldefs_flat);
		if ( $defind !== null && $defind !== false && $col_parts[1] != "custom" ) {
			$coldef_parts = array();
			preg_match("/(.*?)(:[^\/]*)?(\/[^\/,]*)?($)/", $coldefs_adjusted[$defind], $coldef_parts);
			$col = array();
			$col["name"] = $col_parts[1];
			if ( $flat_name == "custom" ) $col["sort"] = substr($col_parts[2], 1, 1);
			else $col["sort"] = ( substr($col_parts[2], 1) != "" ? substr($coldef_parts[2], 1, 1) : "" );
			if ( substr($col_parts[2], 2, 1) != "" ) $defaultsort = ( substr($col_parts[2], 2, 1) == "+" ? "" : "-" ).$col["name"];
			$col["title"] = substr($col_parts[3], 1);
			if ( $col["title"] == "" ) $col["title"] = $params[$col["name"]."title"];
			if ( $col["title"] == "" ) $col["title"] = substr($coldef_parts[3], 1);
			array_push($cols, $col);
		}
	}
	//insert bulk selector column if bulk actions (delete) are enabled
	if ( $params["bulkactions"] == "true" && $params["candelete"] == "true" ) {
		$selcol = array( "name" => "sel", "title" => "sel", "sort" => "" );
		array_splice($cols, 0, 0, array($selcol));
	}
	//set the unique id of this browserrole
	$bid = $params["browserid"];
	$id = "wfu_browser-".$bid;
	//get reload-on-update state
	$reload_on_update = ( $params["reloadonupdate"] == "true" );
	//check if additional actions, such as download or delete are activated
	$actions_exist = ( $params["candownload"] == "true" || $params["candelete"] == "true" );
	//safely store in session the available params for this browser
	$browser_code = wfu_safe_store_browser_params($params);
	//get filelist of user's uploaded files
	$filelist = array();
	$filerecs = wfu_get_filtered_recs(wfu_prepare_browser_filter($params));
	//first insert dummy record in array, that will be used to keep one template hidden row in the table
	array_push($filelist, array( 'name' => '', 'fullpath' => '', 'size' => 0, 'mdate' => 0, 'filedata' => null, 'deletable' => true, 'remote' => false ));
	foreach ( $filerecs as $filerec ) {
		$include = false;
		$filepath = wfu_path_rel2abs($filerec->filepath);
		//check if the file is remote; in this case get file properties from
		//remote file metadata
		$service = wfu_check_file_remote($filepath);
		if ( $service !== false && $params["showremote"] == "true" ) {
			$filedata = wfu_get_filedata_from_rec($filerec);
			$metadata = wfu_get_remote_file_metadata($filedata, $service);
			if ( $metadata != null ) {
				$include = true;
				$filename = $metadata['filename'];
				$fullpath = array( ( $metadata['downloadLink'] != '' ? $metadata['downloadLink'] : $metadata['viewLink'] ), $metadata['viewLink']);
				$filesize = $filerec->filesize;
				$filedate = $metadata['modifiedTime'];
			}
		}
		elseif ( $service === false ) {
			$include = true;
			$stat = wfu_stat($filepath, "wordpress_file_upload_render_browser");
			$filename = wfu_basename($filepath);
			$fullpath = $filepath;
			$filesize = $stat['size'];
			$filedate = $stat['mtime'];
		}
		if ( $include ) {
			$deletable = ( wfu_user_owns_file($user->ID, $filerec) || $params["whodelete"] == "all" || $params["deletestrictmode"] != "true" );
			array_push($filelist, array( 'name' => $filename, 'fullpath' => $fullpath, 'size' => $filesize, 'mdate' => $filedate, 'filedata' => $filerec, 'deletable' => $deletable, 'remote' => ( $service !== false ) ));
		}
	}
	
	//prepare the file properties that will be displayed; this is the first pass
	//of processing that will process all files of all pages using
	//wfu_frontend_browser_preprocess_fileprops function; this function creates
	//the full $props structure with full sorting data, so that it can be sorted
	//correctly; cell contents that take too long to process (like thumbnails)
	//are left empty and will be processed later on, only for the files
	//belonging to the current page
	$fileprops = array();
	$props0 = array();
	$i = 0;
	foreach ( $filelist as $file ) {
		$props = wfu_frontend_browser_preprocess_fileprops($file, $i, $cols, $params);
		if ( $i == 0 ) array_push($props0, $props);
		//before adding the props to the array we check if it matches any
		//search filters that have been added in the file viewer
		elseif ( wfu_frontend_browser_apply_search_filters($props, $cols, $params, $filters) ) array_push($fileprops, $props);
		$i++;
	}
	//store file paths to safe
	wfu_batch_safe_store_filepaths();
	//sort the file properties
	if ( $sort == "" ) $sort = $defaultsort;
	$sortorder = SORT_ASC;
	if ( substr($sort, 0, 1) == "-" ) {
		$sortorder = SORT_DESC;
		$sort = substr($sort, 1);
	}
	$sorttype = "";
	foreach ( $cols as $col )
		if ( $col["name"] == $sort ) {
			$sorttype = $col["sort"];
			break;
		}
	if ( $sorttype == "" ) {
		foreach ( $cols as $col )
			if ( $col["name"] == "file" ) {
				$sorttype = $col["sort"];
				break;
			}
	}
	if ( $sorttype == "" ) $sort = "";
	if ( $sort != "" ) $fileprops = wfu_array_sort($fileprops, $sort."_sort:".$sorttype, $sortorder, true);
	array_splice($fileprops, 0, 0, $props0);

	$files_total = count($fileprops) - 1;
	$pagerows = 0;
	$rowmin = 0;
	$rowmax = 0;
	if ( $params["pagination"] == "true" && (int)$params["pagerows"] > 0 ) {
		$pagerows = (int)$params["pagerows"];
		$pages = ceil($files_total / $pagerows);
		$page = max($page, 1);
		$page = min($page, $pages);
		$rowmin = ($page - 1) * $pagerows + 1;
		$rowmax = $page * $pagerows;
	}
	
	/* set the template that will be used, default is empty (the original) */
	$params["browsertemplate"] = "";
	/**
	 * Filter To Define Custom Front-End File Browser Template.
	 *
	 * This filter is used to define a custom front-end file browser template
	 * that will be used to generate the file browser.
	 *
	 * @since 4.0.0
	 *
	 * @param string $ret The front-end file browser template to use. The
	 *        default is "".
	 * @param array $params An associative array with the shortcode attributes.
	 */
	$params["browsertemplate"] = apply_filters("_wfu_browser_template", $params["browsertemplate"], $params);
	/* Compose the html code for the plugin */
	$echo_str = '';
	if ( !$only_table_rows ) {
		$echo_str .= wfu_init_run_js_script();
		$echo_str .= '<div class="wfu_browser_container '.$id.'">';
		//add custom styles from browsercss attribute
		if ( $params['browsercss'] != '' ) {
			$echo_str .= "\n\t".'<style>';
			$css = str_replace(array( '%dq%', '%brl%', '%brr%' ), array( '"', '[', ']' ), $params['browsercss']);
			$css_lines = explode('%n%', $css);
			foreach ( $css_lines as $css_line ) 
				$echo_str .= "\n\t\t".$css_line;
			$echo_str .= "\n\t".'</style>';
		}
		//initialize the object of the file viewer
		$init_params["shortcode_id"] = $bid;
		$init_params["shortcode_tag"] = $shortcode_tag;
		//add params related to visual editor button
		if ( $can_open_composer ) {
			$init_params["post_id"] = $post->ID;
			/** This filter is described in wfu_loader.php */
			$content = apply_filters("_wfu_get_post_content", $post->post_content, $post);
			$init_params["post_hash"] = hash('md5', $content);
		}
		$init_params["reload_on_update"] = $reload_on_update;
		$init_params["paginated"] = ( $pagerows > 0 );
		$wfub_js = 'var WFUB_JS_'.$bid.' = function() {';
		$wfub_js .= "\n".'GlobalData.WFUB['.$bid.'] = '.wfu_PHP_array_to_JS_object($init_params).'; GlobalData.WFUB.n.push('.$bid.');';
		$wfub_js .= "\n".'}';
		$wfub_js .= "\n".'wfu_run_js("window", "WFUB_JS_'.$bid.'");';
		$wfub_js_html = wfu_js_to_HTML($wfub_js);
		$bbtc = $params["browserblockcompatibility"];
		if ( $bbtc == "on" || ( $bbtc == "auto" && wfu_theme_is_block_based() ) ) {
			$instanceid = ( $post == null ? "" : $post->ID )."__".$bid."b";
			if ( !is_array($WFU_BLOCK_INLINE_JS) ) $WFU_BLOCK_INLINE_JS = array();
			$WFU_BLOCK_INLINE_JS[$instanceid] = $wfub_js_html;
			$wfub_js_html = '[wfu_block_inline_js instanceid="'.$instanceid.'"]';
		}
		$echo_str .= "\n".$wfub_js_html;
		//add visual editor button if the current user is administrator
		if ( $can_open_composer ) $echo_str .= wfu_add_visual_editor_button($shortcode_tag, $params);
		if ( $reload_on_update ) $echo_str .= wfu_add_loading_overlay("\n\t", "browser_".$bid);
		$echo_str .= "\n\t".'<input type="hidden" class="wfu_browser_id" value="'.$bid.'" />';
		$echo_str .= "\n\t".'<input type="hidden" id="wfu_browser_code_'.$bid.'" value="'.$browser_code.'" />';
		$echo_str .= "\n\t".'<input type="hidden" id="wfu_browser_nonce_'.$bid.'" value="'.wp_create_nonce('wfu_download_file_invoker').'" />';
		$echo_str .= "\n\t".'<input type="hidden" id="wfu_browser_guesttitle_'.$bid.'" value="'.esc_html($params['guesttitle']).'" />';
		$echo_str .= "\n\t".'<input type="hidden" id="'.$id.'_sort" value="'.( $sortorder == SORT_ASC ? "" : "-" ).$sort.":".$sorttype.'" />';
		$echo_str .= "\n\t".'<div class="wfu_browser_header '.$id.'" style="width: 100%;">';
		if ( $params["bulkactions"] == "true" && $params["candelete"] == "true" ) {
			$bulkactions = array(
				array( "name" => "delete", "title" => WFU_DELETELABEL ),
				array( "name" => "remove_remote", "title" => WFU_REMOVEREMOTELABEL )
			);
			$echo_str .= wfu_add_bulkactions_header("\n\t\t", "browser_".$bid, $bulkactions);
		}
		if ( $params["pagination"] == "true" && (int)$params["pagerows"] > 0 ) {
			$echo_str .= wfu_add_pagination_header("\n\t\t", "browsernav_".$bid, 1, $pages);
			$echo_str .= wfu_inject_js_code('window["wfu_goto_browsernav_'.$bid.'_page"] = function(token, go_to) { wfu_goto_browsernav_page('.$bid.', go_to); }');
		}
		$echo_str .= "\n\t".'</div>';
		$echo_str .= "\n\t".'<input type="hidden" id="wfu_browser_pagerows_'.$bid.'" value="'.$pagerows.'" />';
		$echo_str .= "\n\t".'<table class="wfu_browser_table '.$id.'">';
		$echo_str .= "\n\t\t".'<thead>';
		$echo_str .= "\n\t\t\t".'<tr class="wfu_browser_tr wfu_head_row '.$id.'">';
		$i = 1;
		foreach ( $cols as $col ) {
			if ( $col["title"] != "" ) {
				$echo_str .= "\n\t\t\t\t".'<th class="wfu_browser_th wfu_col-'.$i.' '.$id.'">';
				if ( $col["name"] == "sel" ) {
					$echo_str .= "\n\t\t\t\t\t".'<input id="wfu_browser_select_all_visible_'.$bid.'" type="checkbox" class="wfu_browser_sel_visible wfu_col-'.$i.' '.$id.'" onchange="wfu_browser_select_all_visible_changed('.$bid.');" style="-webkit-appearance:checkbox;" />';
				}
				else {
					if ( $params["sortable"] == "true" && $col["sort"] != "" ) $echo_str .= "\n\t\t\t\t\t".'<a href="javascript:wfu_browser_sort('.$bid.', \''.$col["name"].'\', \''.$col["sort"].'\');" title="'.esc_html($params["sorttitle"]).'">'.esc_html($col["title"]).'</a>';
					else $echo_str .= "\n\t\t\t\t\t".'<span>'.esc_html($col["title"]).'</span>';
				}
				$echo_str .= "\n\t\t\t\t\t".'<input type="hidden" class="'.$id.'_columns" value="'.$col["name"].'" />';
				$echo_str .= "\n\t\t\t\t".'</th>';
				$i++;
			}
		}
		$echo_str .= "\n\t\t\t".'</tr>';
		$echo_str .= "\n\t\t".'</thead>';
		$echo_str .= "\n\t\t".'<tbody>';
	}
	if ( $params["pagination"] == "true" && (int)$params["pagerows"] > 0 ) {
		$echo_str .= "\n\t\t\t".'<!-- wfu_browser_page['.$page.'] -->';
	}
	$i = 0;
	foreach ( $fileprops as $props ) {
		if ( $i == 0 || !( $reload_on_update && $pagerows > 0 && ( $i < $rowmin || $i > $rowmax ) ) ) {
			//this is the second pass of file properties in order to populate
			//cell contents that take too long to process (like thumbnails) or
			//run custom filters on cell contents
			$props = wfu_frontend_browser_finalprocess_fileprops($props, $cols, $params);
			$echo_str .= "\n\t\t\t".'<tr class="wfu_browser_tr'.( $i == 0 ? '_template' : '' ).( $i > 0 ? ( $pagerows > 0 && ( $i < $rowmin || $i > $rowmax ) ? ' wfu_included wfu_hidden' : ' wfu_included wfu_visible' ) : '' ).' wfu_row-'.$i.' '.$id.'" onmouseover="wfu_browser_mouseover('.$bid.', '.$i.');" onmouseout="wfu_browser_mouseout('.$bid.');"'.( $i > 0 ? ' style="display:'.( $pagerows > 0 && ( $i < $rowmin || $i > $rowmax ) ? 'none' : 'table-row' ).';"' : '' ).'>';
			$ii = 1;
			foreach ( $cols as $col ) {
				$colfound = true;
				$col_str = "\n\t\t\t\t".'<td class="wfu_browser_td wfu_col-'.$ii.' '.$id.'">';
				//insert additional browser info in template row
				if ( $i == 0 && $ii == 1 ) {
					$col_str .= "\n\t\t\t".'<input type="hidden" id="wfu_browser_totalincluded_'.$bid.'" value="'.(count($fileprops) - 1).'" />';
				}
				if ( $col["name"] == "sel" ) {
					$col_str .= "\n\t\t\t\t\t".'<input type="checkbox" name="'.$props['id'].'" class="wfu_display_sel wfu_selcode_'.str_replace(":", "_", $props['file_code']).'" onchange="wfu_browser_selector_changed('.$bid.', this);" />';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_sel" value="" />';
				}
				elseif ( $col["name"] == "inc" ) {
					$col_str .= "\n\t\t\t\t\t".'<span class="wfu_display_inc">'.$i.'</span>';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_inc" value="" />';
				}
				elseif ( $col["name"] == "file" ) {
					if ( $params["candownload"] == "true" ) $col_str .= "\n\t\t\t\t\t".'<a class="wfu_display_file" '.( $props['remote'] ? 'href="'.$props['filestructure']['fullpath'][0].'"' : 'href="" onclick="javascript:wfu_download_file_frontend(this); return false;"' ).' title="'.esc_html($params["downloadtitle"]).'">'.$props['file'].'</a>';
					else $col_str .= "\n\t\t\t\t\t".'<span class="wfu_display_file">'.$props['file'].'</span>';
					$col_str .= "\n\t\t\t\t\t".'<div class="wfu_actions" style="visibility:hidden; display:'.( $actions_exist ? "block" : "none" ).';">';
					if ( $params["candownload"] == "true" ) {
						$col_str .= "\n\t\t\t\t\t\t".'<span>';
						$col_str .= "\n\t\t\t\t\t\t\t".'<a '.( $props['remote'] ? 'href="'.$props['filestructure']['fullpath'][0].'"' : 'href="" onclick="javascript:wfu_download_file_frontend(this); return false;"' ).' title="'.esc_html($params["downloadtitle"]).'">'.esc_html($params["downloadlabel"]).'</a>';
						if ( $params["candelete"] == "true" && $props["deletable"] ) $col_str .= "\n\t\t\t\t\t\t\t".' | ';
						$col_str .= "\n\t\t\t\t\t\t".'</span>';
					}
					if ( $params["candelete"] == "true" && $props["deletable"] ) {
						$col_str .= "\n\t\t\t\t\t\t".'<span>';
						if ( !$props['remote'] )
							$col_str .= "\n\t\t\t\t\t\t\t".'<a href="" onclick="javascript:wfu_delete_file_frontend('.$bid.', this, true, false); return false;" title="'.esc_html($params["deletetitle"]).'">'.esc_html($params["deletelabel"]).'</a>';
						else $col_str .= "\n\t\t\t\t\t\t\t".'<a href="" onclick="javascript:wfu_delete_file_frontend('.$bid.', this, true, true); return false;" title="'.esc_html($params["removeremotetitle"]).'">'.esc_html($params["removeremotelabel"]).'</a>';
						$col_str .= "\n\t\t\t\t\t\t".'</span>';
					}
					$col_str .= "\n\t\t\t\t\t".'</div>';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_file" value="'.wfu_plugin_encode_string($props['file']).'" />';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_file_id" value="'.$props['id'].'" />';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_file_code" value="'.$props['file_code'].'" />';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_file_id0" value="'.$props['id0'].'" />';
					if ( $col["sort"] != "" ) $col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_file_sort" value="'.$props['file_sort'].'" />';
					$col_str .= "\n\t\t\t\t\t".'<div class="wfu_file_download_container" style="display: block;"></div>';
				}
				elseif ( $col["name"] == "date" || $col["name"] == "size" || $col["name"] == "user" || $col["name"] == "post" || $col["name"] == "thumbnail" || $col["name"] == "link" || $col["name"] == "remotelink" || substr($col["name"], 0, 6) == "custom" ) {
					$col_str .= "\n\t\t\t\t\t".'<span class="wfu_display_'.$col["name"].'">'.$props[$col["name"]].'</span>';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_'.$col["name"].'" value="'.wfu_plugin_encode_string($props[$col["name"]]).'" />';
					if ( $col["sort"] != "" ) $col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_'.$col["name"].'_sort" value="'.$props[$col["name"]."_sort"].'" />';
				}
				elseif ( $col["name"] == "fields" ) {
					$col_str .= "\n\t\t\t\t\t".'<div class="wfu_display_fields">';
					$col_str .= $props['fields'];
					$col_str .= "\n\t\t\t\t\t".'</div>';
					$col_str .= "\n\t\t\t\t\t".'<input type="hidden" class="wfu_data_fields" value="'.wfu_plugin_encode_string($props['fields']).'" />';
				}
				else {
					$colfound = false;
					$ii--;
				}
				$col_str .= "\n\t\t\t\t".'</td>';
				if ( $colfound ) $echo_str .= $col_str;
				$ii++;
			}
			$echo_str .= "\n\t\t\t".'</tr>';
		}
		$i++;
	}
	if ( !$only_table_rows ) {
		$echo_str .= "\n\t\t".'</tbody>';
		$echo_str .= "\n\t".'</table>';

		/* Pass constants to javascript and initialize bulk actions */
		$consts = wfu_set_javascript_constants();
		$handler = 'function() { wfu_Initialize_Consts("'.$consts.'"); wfu_browser_load_action('.$bid.'); }';
		$wfu_js = 'if (typeof wfu_addLoadHandler == "undefined") function wfu_addLoadHandler(handler) { if(window.addEventListener) { window.addEventListener("load", handler, false); } else if(window.attachEvent) { window.attachEvent("onload", handler); } else { window["onload"] = handler; } }';
		$wfu_js .= "\n".'wfu_addLoadHandler('.$handler.');';
		$echo_str .= "\n".wfu_js_to_HTML($wfu_js);

		$echo_str .= "\n".'</div>';
	}
	
	/**
	 * Filter To Customise Front-End File Browser Output.
	 *
	 * This filter is used to customise the HTML code generated by the
	 * plugin for showing the front-end file browser.
	 *
	 * @since 3.9.6
	 *
	 * @param string $echo_str The HTML output.
	 * @param array $params An associative array with shortcode attributes.
	 */
	$echo_str = apply_filters("_wfu_file_browser_output", $echo_str, $params);
	return $echo_str;
}

/**
 * Prepare HTML Output of Userdata.
 *
 * This function prepares the HTML output of userdata fields to be shown in the
 * front-end file browser.
 *
 * @since 3.1.0
 *
 * @redeclarable
 *
 * @param array $userdata_list An array of userdata. Each item is an object
 *        holding the database record of each userdata.
 *
 * @return string The HTML output of the userdata.
 */
function wfu_prepare_userdata_list($userdata_list) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$inner_html = "";
	if ( is_array($userdata_list) && count($userdata_list) > 0 ) {
		$inner_html .= "\n\t\t\t\t\t\t".'<select multiple="multiple" size="'.count($userdata_list).'">';
		foreach ( $userdata_list as $userdata )
			$inner_html .= "\n\t\t\t\t\t\t\t".'<option>'.$userdata->property.': '.$userdata->propvalue.'</option>';
		$inner_html .= "\n\t\t\t\t\t\t".'</select>';
	}
	return $inner_html;
}

/**
 * Prepare Front-End File Browser Filters.
 *
 * This function parses the filter attributes of the front-end file browser
 * shortcode into an array.
 *
 * @since 3.2.1
 *
 * @redeclarable
 *
 * @global int $blog_id The ID of the current blog
 *
 * @param array $params The shortcode attributes of the front-end file browser.
 *
 * @return array An array of parsed filters.
 */
function wfu_prepare_browser_filter($params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $blog_id;
	$user = wp_get_current_user();
	$filter = array();
	
	// filter based on roles / users
	$roles = explode(",", $params['rolefilter']);
	foreach ( $roles as $key => $role ) $roles[$key] = trim($role);
	$usernames = explode(",", $params['userfilter']);
	foreach ( $usernames as $key => $username ) $usernames[$key] = trim($username);
	if ( !in_array('all', $roles) || !in_array('guests', $roles) || !in_array('all', $usernames) || !in_array('guests', $usernames) ) {
		$filter['user'] = array();
		$filter['user']['guests'] = ( in_array('guests', $roles) && in_array('guests', $usernames) );
		if ( in_array('guests', $roles) ) unset($roles[array_search('guests', $roles)]);
		if ( in_array('guests', $usernames) ) unset($usernames[array_search('guests', $usernames)]);
		if ( $params['userfilter'] == "current" ) {
			$filter['user']['all'] = false;
			$filter['user']['ids'] = array( ( $user->ID > 0 ? $user->ID : 'guest'.wfu_get_session_id() ) );
		}
		elseif ( in_array('all', $roles) && in_array('all', $usernames) ) $filter['user']['all'] = true;
		else {
			$users = array();
			foreach ( $usernames as $username ) {
				$user_obj = get_user_by('login', $username);
				if ( $user_obj ) array_push($users, $user_obj->ID);
			}
			if ( in_array('all', $roles) ) {
				$filter['user']['all'] = false;
				$filter['user']['ids'] = $users;
			}
			else {
				$filter['user']['all'] = false;
				$roleusers = array();
				$args = array( 'role__in' => $roles, 'fields' => 'ID' );
				/** This filter is documented in lib/wfu_admin_browser.php. */
				$args = apply_filters("_wfu_get_users", $args, "fileviewer_filterusers");
				$args2 = $args;
				unset($args2["role__in"]);
				foreach ( $args["role__in"] as $roleid ) {
					$args2["role"] = $roleid;
					$roleusers = array_merge($roleusers, get_users($args2));
				}
				$roleusers = array_unique($roleusers);
				if ( in_array('all', $usernames) ) $filter['user']['ids'] = $roleusers;
				else $filter['user']['ids'] = array_intersect($roleusers, $users);
			}
		}
	}
	// filter based on upload form IDs; comma-separated list with ranges
	$sidsraw = trim($params['uploaderids']);
	if ( $sidsraw != "" ) {
		$sidarr = array();
		$sidarr_raw = explode(",", $sidsraw);
		foreach ( $sidarr_raw as $sidraw ) {
			$sidraw = trim($sidraw);
			$m = array();
			if ( preg_match("/^\s*(([0-9]+)\s*-\s*)?([0-9]+)\s*$/", $sidraw, $m) ) {
				$isid2 = intval($m[3]);
				if ( $isid2 > 0 ) {
					$isid1 = intval($m[2]);
					if ( $isid1 > 0 ) {
						if ( $isid1 <= $isid2 ) {
							for ( $i = $isid1; $i <= $isid2; $i++ )
								$sidarr[$i] = 1;
						}
					}
					else $sidarr[$isid2] = 1;
				}
			}
		}
		if ( count($sidarr) > 0 ) {
			$sids = array_keys($sidarr);
			sort($sids);
			$filter['uploaderids'] = $sids;
		}
	}
	// filter based on file size
	$size_lower = trim($params['minsizefilter']);
	$size_upper = trim($params['maxsizefilter']);
	if ( $size_lower != "" || $size_upper != "" ) {
		$filter['size'] = array();
		if ( $size_lower != "" ) $filter['size']['lower'] = (int)$size_lower * 1048576;
		if ( $size_upper != "" ) $filter['size']['upper'] = (int)$size_upper * 1048576;
	}
	// filter based on upload date
	$date_lower = trim($params['fromdatefilter']);
	$date_upper = trim($params['todatefilter']);
	if ( $date_lower != "" || $date_upper != "" ) {
		$filter['date'] = array();
		if ( $date_lower != "" ) $filter['date']['lower'] = date("U", strtotime($date_lower));
		if ( $date_upper != "" ) $filter['date']['upper'] = date("U", strtotime($date_upper));
	}
	// filter based on file pattern
	if ( trim($params['patternfilter']) != "" && trim($params['patternfilter']) != "*.*" ) $filter['pattern'] = str_replace(array( "%brl%", "%brr%" ), array( "[", "]" ), trim($params['patternfilter']));
	// filter based on post / page
	if ( $params['postfilter'] != "all" ) {
		$filter['post'] = array();
		$filter['post']['ids'] = array();
		if ( $params['postfilter'] == "current" ) array_push($filter['post']['ids'], $params['currentpostid']);
		else {
			$postlist = explode(",", $params['postfilter']);
			foreach ( $postlist as $postid ) {
				$postid = trim($postid);
				if ( substr($postid, 0, 3) == "all" && get_post_type_object( substr($postid, 3) ) != null ) {
					$postargs = array( 'post_type' => substr($postid, 3), 'post_status' => "publish,private,draft", 'posts_per_page' => -1 );
					/** This filter is documented in lib/wfu_admin.php. */
					$postargs = apply_filters("_wfu_get_posts", $postargs, "browser_filter");
					$posts = get_posts($postargs);
					foreach ( $posts as $postitem ) array_push($filter['post']['ids'], $postitem->ID);
				}
				else array_push($filter['post']['ids'], $postid);
			}
		}
	}
	// filter based on blog
	if ( isset($params['blogfilter']) && $params['blogfilter'] != "all" && function_exists('wp_get_sites') ) {
		$filter['blog'] = array();
		$filter['blog']['ids'] = array();
		if ( $params['blogfilter'] == "current" ) array_push($filter['blog']['ids'], $blog_id);
		else {
			$bloglist = explode(",", $params['blogfilter']);
			foreach ( $bloglist as $key => $blogid ) $bloglist[$key] = trim($blogid);
			$filter['blog']['ids'] = $bloglist;
		}
	}
	// filter based on userdata
	preg_match('/^field:(.*?);\s*criterion:(.*?)\s*;\s*value:(.*)$/', $params['userdatafilter'], $matches);
	if ( count($matches) == 4 && trim($matches[1]) != "" ) {
		$filter['userdata'] = array();
		$filter['userdata']['field'] = $matches[1];
		$filter['userdata']['criterion'] = $matches[2];
		$filter['userdata']['value'] = $matches[3];
	}
	
	return $filter;
}

/**
 * Prepare File Display Properties.
 *
 * This function prepares the file properties that will be displayed. This is
 * the first pass of processing. This function creates the full $props structure
 * with full sorting data, so that it can be sorted correctly; cell contents
 * that take too long to process (like thumbnails) are left empty and will be
 * processed later on, only for the files belonging to the current page.
 *
 * @since 4.3.0
 *
 * @redeclarable
 *
 * @param array $file {
 *        The file properties to process.
 *
 *        @type string $name The file name.
 *        @type string $fullpath The file full path.
 *        @type integer $size The file size.
 *        @type integer $mdate The file creation timestamp.
 *        @type object $filedata The database record of the file.
 *        @type bool $deletable The file can be deleted or not.
 * }
 * @param integer $index The index of the file in the file browser list.
 * @param array $cols {
 *        The column definitions of the file browser.
 *
 *        @type string $name The column simple name (slug).
 *        @type string $title The column displayed name.
 *        @type integer $sort Sort type of this column. If it is empty, this
 *              column is not sortable. If it is 's' then it is sorted as
 *              string. If it is 'n' it is sorted as number.
 * }
 * @param array $params The shortcode attributes of the front-end file browser.
 *
 * @return array {
 *         An array of file display properties.
 *
 *         @type integer $id The ID of the database record of the file.
 *         @type string $file_code A code that corresponds to stored file path
 *               data of the file in the back-end.
 *         @type integer $id0 The file browser index of the file.
 *         @type bool deletable The file can be deleted or not.
 *         @type array $filestructure The properties of the file. See $file
 *               param above for syntax.
 *         @type string $file The contents to show in file column.
 *         @type string $file_sort Optional. The text to sort based on file
 *               column. It exists only if file column is set as sortable.
 *         @type string $date The contents to show in date column.
 *         @type string $date_sort Optional. The text to sort based on date
 *               column. It exists only if date column is set as sortable.
 *         @type string $size The contents to show in size column.
 *         @type string $size_sort Optional. The text to sort based on size
 *               column. It exists only if size column is set as sortable.
 *         @type string $user The contents to show in user column.
 *         @type string $user_sort Optional. The text to sort based on user
 *               column. It exists only if user column is set as sortable.
 *         @type string $post The contents to show in post column.
 *         @type string $post_sort Optional. The text to sort based on post
 *               column. It exists only if post column is set as sortable.
 *         @type string $thumbnail The contents to show in thumbnail column.
 *         @type string $link The contents to show in link column.
 *         @type string $remotelink The contents to show in remote link column.
 *         @type string $customX The contents to show in customX column. X is a
 *               number that corresponds to the index of a userdata field.
 *         @type string $customX_sort Optional. The text to sort based on
 *               customX column. It exists only if customX column is set as
 *               sortable.
 * }
 */
function wfu_frontend_browser_preprocess_fileprops($file, $index, $cols, $params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$bid = $params["browserid"];
	$actions_exist = ( $params["candownload"] == "true" || $params["candelete"] == "true" );
	//store filepath that we need to pass to other functions in session,
	//instead of exposing it in the client browser
	$fid = 0;
	$file_code = "";
	if ( $file['filedata'] ) $fid = $file['filedata']->idlog;
	if ( $index > 0 && $actions_exist ) {
		if ( !$file['remote'] ) $file_code = wfu_prepare_to_batch_safe_store_filepath(wfu_path_abs2rel($file['fullpath']));
		else $file_code = "remote:".$fid;
	}
	$props = array( "id" => $fid, "file_code" => $file_code, "id0" => $index, "deletable" => $file['deletable'], "remote" => $file['remote'], "filestructure" => $file );
	foreach ( $cols as $col ) {
		if ( $col["name"] == "file" ) {
			$props["file"] = $file['name'];
			if ( $col["sort"] != "" ) $props["file_sort"] = $file['name'];
		}
		elseif ( $col["name"] == "date" ) {
			//$props["date"] = date(get_option('date_format', 'd/m/Y'), $file['mdate']);
			//$props["date"] = get_date_from_gmt(date("Y-m-d H:i:s", $file['mdate']), get_option('date_format', 'd/m/Y'));
			//$props["date"] = date_i18n(get_option('date_format', 'd/m/Y'), $file['mdate'], true);
			$props["date"] = date_i18n(get_option('date_format', 'd/m/Y'), strtotime(get_date_from_gmt(date("Y-m-d H:i:s", $file['mdate']))));
			if ( $col["sort"] != "" ) $props["date_sort"] = $file['mdate'];
		}
		elseif ( $col["name"] == "size" ) {
			$props["size"] = wfu_human_filesize($file['size']);
			if ( $col["sort"] != "" ) $props["size_sort"] = $file['size'];
		}
		elseif ( $col["name"] == "user" ) {
			$username = esc_html($params['guesttitle']);
			if ( $file['filedata'] && $file['filedata']->uploaduserid > 0 ) {
				$user_obj = get_user_by('id', $file['filedata']->uploaduserid);
				if ( $user_obj ) $username = $user_obj->user_login;
				else $username = esc_html($params['unknowntitle']);
			}
			$props["user"] = $username;
			if ( $col["sort"] != "" ) $props["user_sort"] = $username;
		}
		elseif ( $col["name"] == "post" ) {
			$post_obj = null;
			$posttitle = "";
			if ( $file['filedata'] ) $post_obj = get_post($file['filedata']->pageid);
			if ( $post_obj ) $posttitle = sanitize_text_field($post_obj->post_title);
			$props["post"] = $posttitle;
			if ( $col["sort"] != "" ) $props["post_sort"] = $posttitle;
		}
		//thumbnail column will be populated with data during final processing
		//of file properties
		elseif ( $col["name"] == "thumbnail" ) {
			$props["thumbnail"] = "";
		}
		elseif ( $col["name"] == "link" ) {
			if ( $file['remote'] ) $url = $file["fullpath"][1];
			else $url = esc_url(str_replace(ABSPATH, site_url()."/", $file["fullpath"]));
			if ( substr($url, 0, 4) == "http" ) $props["link"] = '<a class="wfu_file_link" href="'.$url.'">'.$file['name'].'</a>';
			else $props["link"] = $file['name'];
		}
		elseif ( $col["name"] == "remotelink" ) {
			$url = "";
			$filedata = wfu_get_filedata_from_rec($file["filedata"], false, true, false);
			if ( $filedata == null ) $filedata = array();
			foreach ( $filedata as $key => $data ) {
				if ( isset($data['type']) && $data['type'] == "transfer" ) {
					$metadata = wfu_get_remote_file_metadata($filedata, $key);
					if ( $metadata != null && $metadata['viewLink'] != '' ) {
						$url = $metadata['viewLink'];
						break;
					}
				}
			}
			if ( $url != "" ) $props["remotelink"] = '<a class="wfu_file_link" href="'.$url.'">'.$file['name'].'</a>';
			else $props["remotelink"] = "";
		}
		elseif ( $col["name"] == "fields" )
			$props["fields"] = ( $file['filedata'] == null ? "" : wfu_prepare_userdata_list($file['filedata']->userdata) );
		elseif ( substr($col["name"], 0, 6) == "custom" ) {
			$fieldid = (int)substr($col["name"], 6) - 1;
			$userdata_list = ( $file['filedata'] == null ? null : $file['filedata']->userdata );
			$fieldvalue = ( isset($userdata_list[$fieldid]) ? $userdata_list[$fieldid]->propvalue : "" );
			$props[$col["name"]] = $fieldvalue;
			if ( $col["sort"] != "" ) $props[$col["name"]."_sort"] = $fieldvalue;
		}
		//apply wfu_file_browser_edit_column-{column} filter in order to 
		//customize the contents of the file viewer columns, except thumbnails
		//column, which will be filtered in final process
		if ( $col["name"] != "sel" && $col["name"] != "inc" && $col["name"] != "thumbnail" ) {
			$cell = array( "contents" => $props[$col["name"]], "sort_value" => ( $col["sort"] != "" ? $props[$col["name"]."_sort"] : "" ) );
			$additional_data = array( "bid" => $bid, "column_sortable" => ( $col["sort"] != "" ) , "params" => $params );
			/**
			 * Customize Front-End File Browser Column Contents.
			 *
			 * This filter allows extensions or other scripts to customize the
			 * contents of a front-end file browser column. This function is
			 * called separately for each cell of the column. Set the column
			 * slug at the end of the filter name to define which column to
			 * customize.
			 *
			 * @since 4.0.0
			 *
			 * @see wfu_frontend_browser_preprocess_fileprops() For more
			 *      information on $file array format.
			 *
			 * @param array $cell {
			 *        The contents of a specific cell of the column.
			 *
			 *        @type string $contents The contents of the cell.
			 *        @type string $sort_value The text to sort based on this
			 *              column.
			 * }
			 * @param array $file The properties of the specific file.
			 * @param array $additional_data {
			 *        Additional information about the file browser and cell.
			 *
			 *        @type integer $bid The ID of the file browser.
			 *        @type bool $column_sortable The column is sortable or not.
			 * }
			 */
			$cell = apply_filters("wfu_file_browser_edit_column-".$col["name"], $cell, $file, $additional_data);
			$props[$col["name"]] = $cell["contents"];
			if ( $col["sort"] != "" ) $props[$col["name"]."_sort"] = $cell["sort_value"];
		}
	}
	
	return $props;
}

/**
 * Finalize File Display Properties.
 *
 * This function is the second pass of file properties in order to populate
 * cell contents that take too long to process (like thumbnails) or run custom
 * filters on cell contents.
 *
 * @since 4.3.0
 *
 * @redeclarable
 *
 * @see wfu_frontend_browser_preprocess_fileprops() For more information on
 *      $props and $cols array formats.
 *
 * @param array $props The file properties to process.
 * @param array $cols The column definitions of the file browser.
 * @param array $params The shortcode attributes of the front-end file browser.
 *
 * @return array The processed $props array.
 */
function wfu_frontend_browser_finalprocess_fileprops($props, $cols, $params) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$bid = $params["browserid"];
	$file = $props["filestructure"];
	foreach ( $cols as $col ) {
		if ( $col["name"] == "thumbnail" ) {
			$size = (int)$params["thumbsize"];
			$imgdata = wfu_get_file_thumbnail($props["id"], array($size, $size));
			if ( $imgdata[3] == "image" ) $props["thumbnail"] = '<img class="wfu_file_thumbnail" src="'.$imgdata[0].'" style="max-width: '.$size.'px;" />';
			elseif ( $imgdata[3] == "icon" ) $props["thumbnail"] = '<img class="wfu_file_thumbnail wfu_filetype_icon" src="'.$imgdata[0].'" />';
			//apply wfu_file_browser_edit_column-{column} filter in order to 
			//customize the contents of thumbnails column
			$cell = array( "contents" => $props[$col["name"]], "sort_value" => ( $col["sort"] != "" ? $props[$col["name"]."_sort"] : "" ) );
			$additional_data = array( "bid" => $bid, "column_sortable" => ( $col["sort"] != "" ) , "params" => $params );
			/** This filter is documented in
			    wfu_frontend_browser_preprocess_fileprops() function above. */
			$cell = apply_filters("wfu_file_browser_edit_column-".$col["name"], $cell, $file, $additional_data);
			$props[$col["name"]] = $cell["contents"];
			if ( $col["sort"] != "" ) $props[$col["name"]."_sort"] = $cell["sort_value"];
		}
	}
	
	return $props;
}

/**
 * Apply Search Filters on File.
 *
 * This function performs matching of search filters with the file in order to
 * determine if the file will be included in the front-end file browser or not.
 *
 * @since 4.8.0
 *
 * @see wfu_frontend_browser_preprocess_fileprops() For more information on
 *      $props and $cols array formats.
 * @see wordpress_file_upload_render_browser() For more information on $filters
 *      array format.
 *
 * @param array $props The file properties to process.
 * @param array $cols The column definitions of the file browser.
 * @param array $params The shortcode attributes of the front-end file browser.
 * @param array $filters An array of search filters to apply on the files.
 *
 * @return bool Returns whether the file must be included in the front-end file
 *         browser or not.
 */
function wfu_frontend_browser_apply_search_filters($props, $cols, $params, $filters) {
	$included = true;
	if ( !is_array($filters) ) return $included;
	
	foreach ( $filters as $filter ) {
		foreach( $filter["colfilters"] as $active ) {
			$val1 = $active["value"];
			$val2 = $props[$active["colname"]];
			if ( !$filter["case_sensitive"] ) {
				$val1 = mb_strtolower($val1);
				$val2 = mb_strtolower($val2);
			}
			$no_match = false;
			if ( $active["mode"] == "strict" ) {
				$no_match = ( $val2 != $val1 );
			}
			elseif ( $active["mode"] == "loose" ) {
				$no_match = ( strpos($val2, $val1) === false );
			}
			elseif ( $active["mode"] == "fuzzy" ) {
				$no_match = ( strpos($val2, $val1) === false );
			}
			else {
				$no_match = ( ( substr($val1, 0, 1) == "*" && substr($val1, -1) == "*" && strpos($val2, substr($val1, 1, -1)) === false ) ||
					( substr($val1, 0, 1) == "*" && substr($val1, -1) != "*" && substr($val1, 1) != substr($val2, 1 - strlen($val1)) ) ||
					( substr($val1, 0, 1) != "*" && substr($val1, -1) == "*" && substr($val1, 0, -1) != substr($val2, 0, strlen($val1) - 1) ) ||
					( substr($val1, 0, 1) != "*" && substr($val1, -1) != "*" && $val1 != $val2) );
			}
			if ( $no_match ) {
				$included = false;
				break;
			}
		}
		if ( !$included ) break;
	}
	
	return $included;
}