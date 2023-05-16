<?php

add_filter('_wfu_after_upload', 'wfu_nextgen_add_files', 10, 3);

function wfu_nextgen_add_files($ret, $attr, $params) {
	$NextGEN_ok = ( class_exists("C_Gallery_Storage") && class_exists("C_Image_Mapper") && class_exists("C_Gallery_Mapper") );
	if ( $params["nextgen"] == "true" && $NextGEN_ok ) {
		$storage        = /*<wfu_ExTeRnAlReF*/C_Gallery_Storage/*wfu_ExTeRnAlReF/>*/::get_instance();
		$image_mapper   = /*<wfu_ExTeRnAlReF*/C_Image_Mapper/*wfu_ExTeRnAlReF/>*/::get_instance();
		$gallery_mapper = /*<wfu_ExTeRnAlReF*/C_Gallery_Mapper/*wfu_ExTeRnAlReF/>*/::get_instance();
		$gallery = $gallery_mapper->find( $params["ngg_galleryid"] );
		if ( $gallery->path ) {
			//get user data
			$user = wp_get_current_user();
			if ( 0 == $user->ID ) {
				$user_id = 0;
				$user_login = "guest";
			}
			else {
				$user_id = $user->ID;
				$user_login = $user->user_login;
			}
			//define basic search and replace arrays for variables
			$search = array ('/%userid%/', '/%username%/', '/%blogid%/', '/%pageid%/', '/%pagetitle%/');	
			$replace = array ($user_id, $user_login, $params['blogid'], $params['pageid'], sanitize_text_field(get_the_title($params['pageid'])));
			//process files
			$files = $attr["files"];
			foreach ( $files as $file ) {
				$uploaded = ( $file["upload_result"] == "warning" || $file["upload_result"] == "success" );
				$path = $file["filepath"];
				$filedata = wfu_get_filedata($path, true);
				if ( $uploaded && wfu_file_exists($path, "wfu_nextgen_add_files") && $filedata != null ) {
					$image = $storage->upload_base64_image($gallery, wfu_file_get_contents($path, "wfu_nextgen_add_files"), wfu_basename($path));
					//fix because it seems that latest versions of function
					//upload_base64_image return the image ID and not the object
					if ( $image && !is_object($image) ) $image = $image_mapper->find($image);
					if ( $image && is_object($image) ) {
						//first define search and replace arrays for userdata
						$search_userdata = array();
						$replace_userdata = array();
						foreach ( $file["user_data"] as $userdata_key => $userdata_field ) { 
							$ind = 1 + $userdata_key;
							array_push($search_userdata, '/%userdata'.$ind.'%/');  
							array_push($replace_userdata, $userdata_field["value"]);
						}
						//define complete search and replace arrays
						$search_full = array_merge($search, $search_userdata);
						$replace_full = array_merge($replace, $replace_userdata);
						//set NextGEN added image properties
						$image->exclude = ( $params["ngg_exclude"] == "true" ? 1 : 0 );
						$image->description = preg_replace($search_full, $replace_full, $params["ngg_description"]);
						$image->alttext = preg_replace($search_full, $replace_full, $params["ngg_alttext"]);
						$new_pid = $image_mapper->save( $image );
						$tags = explode(",", preg_replace($search_userdata, $replace_userdata, $params["ngg_tags"]));
						$tag_ids = array();
						foreach ( $tags as $tag ) {
							$res = term_exists(trim($tag), 'ngg_tag');
							if ( $res !== 0 && $res !== null && is_array($res) ) array_push($tag_ids, (int)$res['term_id']);
							else {
								$res = wp_insert_term(trim($tag), 'ngg_tag');
								if ( !is_wp_error($res) ) array_push($tag_ids, (int)$res['term_id']);
							}
						}
						if ( count($tag_ids) > 0 ) wp_set_object_terms($new_pid, $tag_ids, 'ngg_tag', true);
						$filedata['ngg_image'] = array(
							'type' => 'data',
							'pid' => $image->{$image->id_field}
						);
						wfu_save_filedata_from_id($filedata["general"]["idlog"], $filedata);
					}
				}
			}
		}
	}
	return $ret;
}