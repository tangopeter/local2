<?php

/**
 * Plugin Hooks
 *
 * This file contains functions related to hooks functionality of the plugin.
 * Hooks are custom code blocks that enable customization of plugin's operation.
 *
 * @link /lib/wfu_admin_hooks.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 3.6.0
 */
 

/**
 * Display the Hooks Page.
 *
 * This function displays the Hooks page in Dashboard area of the plugin.
 *
 * @since 3.6.0
 *
 * @param string $error_message Optional. An error message to show on top of
 *        Hooks page.
 *
 * @return string The HTML output of the Hooks page.
 */
function wfu_manage_hooks($error_message = "") {
	if ( !current_user_can( 'manage_options' ) ) return;
	$siteurl = site_url();
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
//	update_option( "wordpress_file_upload_hooks", "" );
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	$plugin_hooks = array_reverse($plugin_hooks);

	$echo_str = "";
	$echo_str .= "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	if ( $error_message != "" ) {
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.$error_message.'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Hooks");
	if ( WFU_VAR("WFU_IGNORE_HOOK_USERSTATECHECK") == "false" && ( $plugin_options["userstatehandler"] == "dboption" || WFU_VAR("WFU_US_SESSION_LEGACY") == "false" ) && wfu_active_hooks_using_session() ) {
		$echo_str .= "\n\t\t".'<div class="wfu_hook_warning">';
		$echo_str .= "\n\t\t\t".'<p>There are active hooks that may not work properly with the current User State configuration! Press on them to see details.</p>';
		$echo_str .= "\n\t\t".'</div>';
	}
	$echo_str .= "\n\t\t".'<form enctype="multipart/form-data" name="edithooks" id="edithooks" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=edit_hooks" class="validate">';
	$nonce = wp_nonce_field('wfu_edit_hooks', '_wpnonce', false, false);
	$nonce_ref = wp_referer_field(false);
	$echo_str .= "\n\t\t\t".$nonce;
	$echo_str .= "\n\t\t\t".$nonce_ref;
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="action" value="edit_hooks" />';
	$echo_str .= "\n\t\t\t".'<div class="tablenav top">';
	$echo_str .= "\n\t\t\t\t".'<div class="alignleft actions bulkactions">';
	$echo_str .= "\n\t\t\t\t\t".'<select name="bulkaction" id="bulk-action-selector-top">';
	$echo_str .= "\n\t\t\t\t\t\t".'<option value="-1" selected="selected">Bulk Actions</option>';
	$echo_str .= "\n\t\t\t\t\t\t".'<option value="delete">Delete</option>';
	$echo_str .= "\n\t\t\t\t\t\t".'<option value="activate">Activate</option>';
	$echo_str .= "\n\t\t\t\t\t\t".'<option value="deactivate">Deactivate</option>';
	$echo_str .= "\n\t\t\t\t\t".'</select>';
	$echo_str .= "\n\t\t\t\t\t".'<input type="submit" id="doaction" name="doaction" class="button action" value="Apply" />';
	$echo_str .= "\n\t\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=add_hook" class="button" title="add new hook" style="float:right;">Add New Hook</a>';
	$echo_str .= "\n\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t".'<table class="wp-list-table widefat fixed striped">';
	$echo_str .= "\n\t\t\t\t".'<thead>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td scope="col" width="5%" class="manage-column check-column">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input id="cb-select-all" type="checkbox" onchange="var actions=document.getElementsByName(\'hook[]\'); for (var i=0; i<actions.length; i++) {actions[i].checked=this.checked;}" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="30%" class="manage-column column-primary">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Title</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="50%" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Description</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label>Status</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'</thead>';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	if ( count($plugin_hooks) == 0 ) {
		$echo_str .= "\n\t\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t\t".'<td colspan="4">';
		$echo_str .= "\n\t\t\t\t\t\t\t".'<span>No hooks have been defined. Press the "Add New Hook" button to add a new one.</span>';
		$echo_str .= "\n\t\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'</tr>';
	}
	else {
		$ii = 1;
		foreach( $plugin_hooks as $key => $hook ) {
			$wfu_active_using_session_var = false;
			if ( $hook["status"] == 1 && WFU_VAR("WFU_IGNORE_HOOK_USERSTATECHECK") == "false" && ( $plugin_options["userstatehandler"] == "dboption" || WFU_VAR("WFU_US_SESSION_LEGACY") == "false" ) )
				$wfu_active_using_session_var = ( strpos(strtolower(wfu_plugin_decode_string($hook["code"])), '$_session') !== false );
			$echo_str .= "\n\t\t\t\t\t".'<tr onmouseover="var actions=document.getElementsByName(\'wfu_hook_actions\'); for (var i=0; i<actions.length; i++) {actions[i].style.visibility=\'hidden\';} document.getElementById(\'wfu_hook_actions_'.$ii.'\').style.visibility=\'visible\'" onmouseout="var actions=document.getElementsByName(\'wfu_hook_actions\'); for (var i=0; i<actions.length; i++) {actions[i].style.visibility=\'hidden\';}">';
			$echo_str .= "\n\t\t\t\t\t\t".'<th>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<input id="cb-select-'.$ii.'" type="checkbox" name="hook[]" value="'.$key.'">';
			$echo_str .= "\n\t\t\t\t\t\t".'</th>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td class="column-primary" data-colname="Title">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<a class="row-title'.( $wfu_active_using_session_var ? ' wfu_tab_warning' : '' ).'" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=edit_hook&key='.$key.'" title="'.$hook["title"].'">'.$hook["title"].'</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<div id="wfu_hook_actions_'.$ii.'" name="wfu_hook_actions" style="visibility:hidden;">';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=edit_hook&key='.$key.'" title="Edit this hook">Edit</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'</span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=delete_hook&key='.$key.'" title="Delete this hook">Delete</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".' | ';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'</span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'<span>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action='.( $hook["status"] == 1 ? 'deactivate' : 'activate' ).'_hook&key='.$key.'" title="'.( $hook["status"] == 1 ? 'Deactivate' : 'Activate' ).' this hook">'.( $hook["status"] == 1 ? 'Deactivate' : 'Activate' ).'</a>';
			$echo_str .= "\n\t\t\t\t\t\t\t\t".'</span>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td data-colname="Description">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>'.$hook["description"].'</span>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t\t".'<td data-colname="Status">';
			$echo_str .= "\n\t\t\t\t\t\t\t".'<span>'.( $hook["status"] == 1 ? "Active" : "Inactive" ).'</span>';
			$echo_str .= "\n\t\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'</tr>';
			$ii++;
		}
	}
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	$echo_str .= "\n\t\t".'</form>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n".'</div>';
	
	return $echo_str;
}

/**
 * Show Hook Edit Page.
 *
 * This function displays the hook editor from where admins can create a new
 * hook or edit an existing one.
 *
 * @since 3.6.0
 *
 * @param string|array $key Optional. If this function was called for an
 *        existing hook, this param holds data of the hook. If it was called for
 *        a new hook, it contains an empty string.
 * @param string $error_status Optional. It displays an error message on top of
 *        the page.
 *
 * @return string The HTML output of the hook editor page.
 */
function wfu_edit_hook($key = "", $error_status = "") {
	$siteurl = site_url();
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();

	if ( !current_user_can( 'manage_options' ) ) return;
	if ( $key != "" && !array_key_exists($key, $plugin_hooks) ) return;

	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	
	if ( $key == "" ) {
		$title = "";
		$description = "";
		$code = "";
		$status = 0;
		$scope = 0;
	}
	else {
		$title = $plugin_hooks[$key]["title"];
		$description = $plugin_hooks[$key]["description"];
		$code = wfu_plugin_decode_string($plugin_hooks[$key]["code"]);
		$status = $plugin_hooks[$key]["status"];
		$scope = $plugin_hooks[$key]["scope"];
	}

	$echo_str = "";
	$echo_str = '<div class="wrap">';
	$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
	if ( $error_status == "error" ) {
		$key = WFU_USVAR("wfu_hook_data_key");
		$title = WFU_USVAR("wfu_hook_data_title");
		$description = WFU_USVAR("wfu_hook_data_description");
		$code = wfu_plugin_decode_string(WFU_USVAR("wfu_hook_data_code"));
		$status = WFU_USVAR("wfu_hook_data_status");
		$scope = WFU_USVAR("wfu_hook_data_scope");
		$echo_str .= "\n\t".'<div class="error">';
		$echo_str .= "\n\t\t".'<p>'.WFU_USVAR("wfu_hook_data_message").'</p>';
		$echo_str .= "\n\t".'</div>';
	}
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=manage_hooks" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t\t".'<h2 style="margin-bottom: 10px; margin-top: 20px;">'.( $key == "" ? 'Add New Hook' : 'Edit Hook <strong>'.$title.'</strong>' ).'</h2>';

	$wfu_active_using_session_var = false;
	if ( $status == 1 && WFU_VAR("WFU_IGNORE_HOOK_USERSTATECHECK") == "false" && ( $plugin_options["userstatehandler"] == "dboption" || WFU_VAR("WFU_US_SESSION_LEGACY") == "false" ) )
		$wfu_active_using_session_var = ( strpos(strtolower($code), '$_session') !== false );
	if ( $wfu_active_using_session_var ) {
		$echo_str .= "\n\t\t".'<div class="wfu_hook_warning">';
		$echo_str .= "\n\t\t\t".'<p>The hook\'s code contains the word $_SESSION, which means that is using session. It may not work properly with the current User State configuration! Please read this <a href="https://www.iptanus.com/wordpress-file-upload-hooks-and-session/" target="_blank">article</a> for resolution instructions.</p>';
		$echo_str .= "\n\t\t".'</div>';
	}

	$echo_str .= "\n\t\t".'<form enctype="multipart/form-data" name="updatehook" id="updatehook" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=updatehook" class="validate">';
	$nonce = wp_nonce_field('wfu_edit_hook', '_wpnonce', false, false);
	$nonce_ref = wp_referer_field(false);
	$echo_str .= "\n\t\t\t".$nonce;
	$echo_str .= "\n\t\t\t".$nonce_ref;
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="action" value="updatehook">';
	$echo_str .= "\n\t\t\t".'<input type="hidden" name="wfu_key" value="'.$key.'">';
	$echo_str .= "\n\t\t\t".'<table class="form-table">';
	$echo_str .= "\n\t\t\t\t".'<tbody>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_title">Title</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input name="wfu_title" id="wfu_title" type="text" value="'.$title.'" style="width:90%;" />';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_description">Description</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<textarea name="wfu_description" id="wfu_description" rows="2" value="" style="width:90%;">'.$description.'</textarea>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_code">Code</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<div class="alignleft actions" style="width:90%; text-align:right;">';
	$templates = wfu_load_hook_templates();
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<select id="wfu_templates" style="vertical-align:baseline;">';
	if ( count($templates) == 0 ) $echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<option value="-1">No templates available</option>';
	else {
		$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<option value="-1">Add from Template</option>';
		foreach ( $templates as $template )
			$echo_str .= "\n\t\t\t\t\t\t\t\t\t".'<option value="'.$template["name"].'">'.$template["title"].'</option>';
	}
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'</select>';
	$echo_str .= "\n\t\t\t\t\t\t\t\t".'<input type="button" id="wfu_add_code" class="button action" value="Add Code" onclick="wfu_get_hook_code_from_template();" /><span id="wfu_add_code_spinner" class="spinner" style="float:right;"></span>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'</div>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<textarea name="wfu_code" id="wfu_code" rows="20" value="" style="width:90%;">'.$code.'</textarea>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_status">Status</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="radio" name="wfu_status" id="wfu_status_on"'.( $status == 1 ? ' checked="checked"' : '' ).' value="active" /><label for="wfu_status_on">Active</label><input type="radio" name="wfu_status" id="wfu_status_off"'.( $status != 1 ? ' checked="checked"' : '' ).' value="inactive" style="margin-left:10px;" /><label for="wfu_status_off">Inactive</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t\t".'<tr>';
	$echo_str .= "\n\t\t\t\t\t\t".'<th scope="row">';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<label for="wfu_scope">Scope</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</th>';
	$echo_str .= "\n\t\t\t\t\t\t".'<td>';
	$echo_str .= "\n\t\t\t\t\t\t\t".'<input type="radio" name="wfu_scope" id="wfu_scope_all"'.( $scope != 1 && $scope != -1 ? ' checked="checked"' : '' ).' value="all" /><label for="wfu_scope_all">Everywhere</label><input type="radio" name="wfu_scope" id="wfu_scope_back"'.( $scope == -1 ? ' checked="checked"' : '' ).' value="back" style="margin-left:10px;" /><label for="wfu_scope_back">Dashboard</label><input type="radio" name="wfu_scope" id="wfu_scope_front"'.( $scope == 1 ? ' checked="checked"' : '' ).' value="front" style="margin-left:10px;" /><label for="wfu_scope_front">Front-End</label>';
	$echo_str .= "\n\t\t\t\t\t\t".'</td>';
	$echo_str .= "\n\t\t\t\t\t".'</tr>';
	$echo_str .= "\n\t\t\t\t".'</tbody>';
	$echo_str .= "\n\t\t\t".'</table>';
	$echo_str .= "\n\t\t\t".'<div class="submit">';
	$echo_str .= "\n\t\t\t\t".'<input type="submit" id="submitcancel" class="button" name="submitform" value="Cancel" />';
	$echo_str .= "\n\t\t\t\t".'<input type="submit" id="submitsave" class="button-primary" name="submitform" value="Save" />';
	$echo_str .= "\n\t\t\t".'</div>';
	$echo_str .= "\n\t\t".'</form>';
	$echo_str .= "\n\t".'</div>';
	$handler = 'function() { wfu_Attach_Snippet_PHP_Editor(); }';
	$echo_str .= "\n\t".'<script type="text/javascript">if(window.addEventListener) { window.addEventListener("load", '.$handler.', false); } else if(window.attachEvent) { window.attachEvent("onload", '.$handler.'); } else { window["onload"] = '.$handler.'; }</script>';
	$echo_str .= "\n".'</div>';
	
	return $echo_str;
}

/**
 * Load Hook Templates.
 *
 * This function loads a list of ready hook templates from file
 * lib/_wfu_hook_templates.php.
 *
 * @since 3.6.0
 *
 * @param string|array $key Optional. If this function was called for an
 *        existing hook, this param holds data of the hook. If it was called for
 *        a new hook, it contains an empty string.
 * @param string $error_status Optional. It displays an error message on top of
 *        the page.
 *
 * @return array An associative array containing hook templates.
 */
function wfu_load_hook_templates() {
	$templates = array();
	$file = ABSWPFILEUPLOAD_DIR."lib/_wfu_hook_templates.php";
	if ( file_exists($file) ) {
		$data = file_get_contents($file);
		$n = ( substr($data, 0, 7) == "<?php\r\n" ? "\r\n" : ( substr($data, 0, 6) == "<?php\r" ? "\r" : ( substr($data, 0, 6) == "<?php\n" ? "\n" : "\r\n" ) ) );
		$matches = array();
		preg_match_all("/\/\*<(\w*?)>\s*?title:(.*?)$n(\s*?scope:(.*?)$n|)(.*?)\*\/$n(.*?)$n\/\*<\/(\w*?)>/s", $data, $matches);
		if ( count($matches) == 8 ) {
			foreach ( $matches[1] as $index => $name )
				if ( $name != "" && trim($matches[2][$index]) != "" && $matches[7][$index] == $name ) {
					$template["name"] = $name;
					$template["title"] = trim($matches[2][$index]);
					$template["scope"] = ( trim($matches[4][$index]) == "everywhere" ? "everywhere" : ( trim($matches[4][$index]) == "dashboard" ? "dashboard" : ( trim($matches[4][$index]) == "frontend" ? "frontend" : "nothing" ) ) );
					$template["description"] = $matches[5][$index];
					$template["body"] = $matches[6][$index];
					$templates[$name] = $template;
				}
		}
	}
	
	return $templates;
}

/**
 * Process Hooks Bulk Actions.
 *
 * This function processes bulk actions on hooks selected by the admin.
 *
 * @since 3.6.0
 *
 * @return string The HTML output of the Hooks page.
 */
function wfu_edit_hooks() {
	if ( !current_user_can( 'manage_options' ) ) return;
	if ( !check_admin_referer('wfu_edit_hooks') ) return;

	$error_message = "";
	if ( isset($_POST['bulkaction']) && isset($_POST['hook']) && isset($_POST['doaction']) ) {
		if ( $_POST['doaction'] == "Apply" && is_array($_POST['hook']) && count($_POST['hook']) > 0 ) {
			if ( $_POST['bulkaction'] == "delete" ) return wfu_delete_hook_prompt($_POST['hook']);
			elseif ( $_POST['bulkaction'] == "activate" ) $error_message = wfu_toggle_hook($_POST['hook'], 1);
			elseif ( $_POST['bulkaction'] == "deactivate" ) $error_message = wfu_toggle_hook($_POST['hook'], 0);
		}
	}
	
	return wfu_manage_hooks($error_message);
}

/**
 * Validate Hook Code.
 *
 * This function checks the hook code for errors that could break the website.
 *
 * @since 3.6.0
 *
 * @param string $key An alphanumeric representing the specific hook.
 * @param string $code The hook code.
 *
 * @return bool True if the code is Ok, false otherwise.
 */
function wfu_validate_hook_code($key, $code) {
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options["modsecurity"] != "1" )
		return wfu_validate_hook_code_basic($key);
	else
		return wfu_validate_hook_code_alt($code);
}

/**
 * Code Validation Main Method.
 *
 * This function contains the main method to validate the hook code. It
 * validates the hook inside an internal AJAX request. This way the website will
 * not be broken if the hook contains errors. Details about the error will also
 * be retrieved. Validation is executed using PHP eval() function. The command
 * 'return 1;' is prepended to the code before eval. This way eval() will not
 * execute the code, it will just check for errors. If the result is 1, then the
 * code is Ok, otherwise result will contain the error.
 *
 * @since 3.8.0
 *
 * @param string $key An alphanumeric representing the specific hook.
 *
 * @return array An array containing the results of the validation.
 */
function wfu_validate_hook_code_basic($key) {
	$validation["Ok"] = false;
	$validation["error"] = "";
	$boundary = wfu_create_random_string(8);
	$params = array(
		'action'	=> 'wfu_ajax_action_load_hook_code',
		'token'		=> wfu_generate_global_short_token(10),
		'boundary'	=> $boundary,
		'key'		=> $key
	);
	$result = wfu_post_request(wfu_ajaxurl(), $params, false, true);
	$pos = strpos($result, $boundary."0");
	if ( $pos !== false ) {
		$result = substr($result, $pos + strlen($boundary) + 1);
		$pos = strpos($result, $boundary."1");
		if ( $pos !== false ) $result = substr($result, 0, $pos);
		if ( $result == "1" ) $validation["Ok"] = true;
		elseif ( trim($result) != "" ) $validation["error"] = trim($result);
	}
	else $validation["error"] = trim($result);
	
	return $validation;
}

/**
 * Code Validation Alternative Method.
 *
 * This function contains the alternative method to validate the hook code and
 * is executed when ModSecurity Restrictions option in plugin's Settings is
 * activated. It executes eval() function on the hook's code directly and not
 * inside a POST request, because mod_security module usually does not allow
 * internal AJAX requests.
 *
 * @since 3.8.0
 *
 * @param string $code The hook code.
 *
 * @return array An array containing the results of the validation.
 */
function wfu_validate_hook_code_alt($code) {
	$validation["Ok"] = false;
	$validation["error"] = "";
	//prepend "return 1;" to the code, so that eval does not execute it but only check the syntax
	$code = "return 1; ".$code;
	//enable reporting and showing all errors
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
	$result = eval($code);
	if ( $result == "1" ) $validation["Ok"] = true;
	elseif ( trim($result) != "" ) $validation["error"] = trim($result);
	
	return $validation;
}

/**
 * Update a Hook.
 *
 * This function updates a hook's properties.
 *
 * @since 3.6.0
 *
 * @return bool True if update was successful, false otherwise.
 */
function wfu_update_hook() {
	if ( !current_user_can( 'manage_options' ) ) return;
	if ( !check_admin_referer('wfu_edit_hook') ) return;
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();

	$message = "";
	if ( isset($_POST['wfu_key']) && isset($_POST['wfu_title']) && isset($_POST['wfu_description']) && isset($_POST['wfu_code']) && isset($_POST['wfu_status']) && isset($_POST['wfu_scope']) ) {
		if ( !isset($_POST['submitform']) || $_POST['submitform'] != "Cancel" ) {
			$key = $_POST['wfu_key'];
			if ( trim($_POST['wfu_title']) == "" ) $message = "Error! Hook title cannot be empty. Please enter a title for this hook.";
			else if ( trim($_POST['wfu_code']) == "" ) $message = "Error! Hook code cannot be empty. Please enter some code for this hook.";
			else if ( $key != "" && !array_key_exists($key, $plugin_hooks) ) $message = "Error! Hook not found in database. Probably it was erased from another opened browser window. Please go back to hooks' list.";
			else {
				$hook = array();
				$hook["title"] = wp_strip_all_tags(trim($_POST['wfu_title']));
				$hook["description"] = wp_strip_all_tags(trim($_POST['wfu_description']));
				$hook["code"] = wfu_plugin_encode_string(trim($_POST['wfu_code']));
				$hook["status"] = 0;
				$hook["scope"] = ( $_POST['wfu_scope'] == "back" ? -1 : ( $_POST['wfu_scope'] == "front" ? 1 : 0 ) );
				if ( $key == "" ) $key = wfu_create_random_string(6);
				$plugin_hooks[$key] = $hook;
				update_option( "wordpress_file_upload_hooks", $plugin_hooks );
				
				$hook["status"] = ( $_POST['wfu_status'] == "active" ? 1 : 0 );
				$validation = wfu_validate_hook_code($key, trim($_POST['wfu_code']));
				if ( !$validation["Ok"] )
					$message = ( $validation["error"] == "" ? "Hook has been saved but cannot be activated because the code contains errors. Please check its syntax." : "Hook has been saved but cannot be activated because the code contains the following error: ".$validation["error"] );
				elseif ( $hook["status"] == 1 ) {
					$plugin_hooks[$key] = $hook;
					update_option( "wordpress_file_upload_hooks", $plugin_hooks );
				}
			}
		}
	}
	
	if ( $message != "" ) {
		WFU_USVAR_store("wfu_hook_data_message", $message);
		WFU_USVAR_store("wfu_hook_data_key", $key);
		WFU_USVAR_store("wfu_hook_data_title", $_POST['wfu_title']);
		WFU_USVAR_store("wfu_hook_data_description", $_POST['wfu_description']);
		WFU_USVAR_store("wfu_hook_data_code", wfu_plugin_encode_string($_POST['wfu_code']));
		WFU_USVAR_store("wfu_hook_data_status", $_POST['wfu_status']);
		WFU_USVAR_store("wfu_hook_data_scope", $_POST['wfu_scope']);
	}
	
	return ( $message == "" );
}

/**
 * Confirm Deletion of Hook.
 *
 * This function shows a page to confirm deletion of a hook.
 *
 * @since 3.6.0
 *
 * @param string $key A key corresponding to the hook to be deleted.
 *
 * @return string The HTML code of the confirmation page.
 */
function wfu_delete_hook_prompt($key) {
	$siteurl = site_url();
	
	if ( !current_user_can( 'manage_options' ) ) return;

	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	if ( !is_array($key) ) $key = array( $key );
	foreach ( $key as $index => $item )
		if ( !array_key_exists($item, $plugin_hooks) ) unset($key[$index]);
	$count = count($key);
	if ( $count == 0 ) return;

	$echo_str = "\n".'<div class="wrap">';
	$echo_str .= "\n\t".'<div style="margin-top:20px;">';
	$echo_str .= "\n\t\t".'<a href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&amp;action=manage_hooks" class="button" title="go back">Go back</a>';
	$echo_str .= "\n\t".'</div>';
	$echo_str .= "\n\t".'<h2 style="margin-bottom: 10px;">Delete Hook</h2>';
	$echo_str .= "\n\t".'<form enctype="multipart/form-data" name="deletehook" id="deletehook" method="post" action="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload" class="validate">';
	$echo_str .= "\n\t\t".wp_nonce_field('wfu_delete_hook', '_wpnonce', false, false);
	$echo_str .= "\n\t\t".wp_referer_field(false);
	$echo_str .= "\n\t\t".'<input type="hidden" name="action" value="deletehook">';
	foreach ( $key as $item ) $echo_str .= "\n\t\t".'<input type="hidden" name="key[]" value="'.$item.'">';
	if ( $count == 1 )
		$echo_str .= "\n\t\t".'<label>Are you sure that you want to delete hook with title: <strong>'.$plugin_hooks[$key[0]]["title"].'</strong>?</label><br/>';
	else {
		$echo_str .= "\n\t\t".'<label>Are you sure that you want to delete hooks with title:</label><br/>';
		foreach ( $key as $item ) $echo_str .= "\n\t\t".'<label style="margin-left:20px;"><strong>'.$plugin_hooks[$item]["title"].'</strong></label><br/>';
	}
	$echo_str .= "\n\t\t".'<p class="submit">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Delete">';
	$echo_str .= "\n\t\t\t".'<input type="submit" class="button-primary" name="submit" value="Cancel">';
	$echo_str .= "\n\t\t".'</p>';
	$echo_str .= "\n\t".'</form>';
	$echo_str .= "\n".'</div>';
	return $echo_str;
}

/**
 * Execute Deletion of Hook.
 *
 * This function deletes a hook.
 *
 * @since 3.6.0
 *
 * @return bool Always true.
 */
function wfu_delete_hook() {
	if ( !current_user_can( 'manage_options' ) ) return;
	if ( !check_admin_referer('wfu_delete_hook') ) return;
	
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();

	if ( isset($_POST['key']) && isset($_POST['submit']) ) {
		if ( $_POST['submit'] == "Delete" ) {
			$key = $_POST['key'];
			if ( !is_array($key) ) $key = array( $key );
			foreach ( $key as $item )
				if ( array_key_exists($item, $plugin_hooks) ) unset($plugin_hooks[$item]);
			update_option( "wordpress_file_upload_hooks", $plugin_hooks );
		}
	}
	
	return true;
}

/**
 * Toogle Active Status of Hook.
 *
 * This function activates / deactivates a hook. In case of activation,
 * this function will first execute validation of the hook's code.
 *
 * @since 3.6.0
 *
 * @param string $key A key corresponding to the hook.
 * @param int $status The new status of the hook. 1: activated, 0: deactivated.
 *
 * @return string Empty string if status toggle was successful, otherwise the
 *         error message.
 */
function wfu_toggle_hook($key, $status) {
	$siteurl = site_url();
	
	if ( !current_user_can( 'manage_options' ) ) return "";

	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	if ( !is_array($key) ) $key = array( $key );
	foreach ( $key as $index => $item )
		if ( !array_key_exists($item, $plugin_hooks) ) unset($key[$index]);
	$count = count($key);
	if ( $count == 0 ) return "";
	
	$changed_count = 0;
	$error_message = "";
	$error_titles = array();
	foreach ( $key as $item ) {
		$itemstatus = $status;
		if ( $itemstatus == 1 ) {
			$validation = wfu_validate_hook_code($item, wfu_plugin_decode_string($plugin_hooks[$item]["code"]));
			if ( !$validation["Ok"] ) {
				array_push($error_titles, $plugin_hooks[$item]["title"]);
				$itemstatus = 0;
			}
		}
		if ( $itemstatus != $plugin_hooks[$item]["status"] ) {
			$plugin_hooks[$item]["status"] = $itemstatus;
			$changed_count++;
		}
	}
	if ( $changed_count > 0 ) update_option( "wordpress_file_upload_hooks", $plugin_hooks );
	if ( count($error_titles) == 1 ) $error_message = 'Hook <strong>'.$error_titles[0].'</strong> was not activated because its code contains errors.';
	elseif ( count($error_titles) > 1 ) {
		$error_message = 'The following hooks were not activated because their codes contain errors:';
		foreach ( $error_titles as $title ) $error_message .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$title.'</strong>';
	}

	return $error_message;
}

/**
 * Execute Active Hooks.
 *
 * This function will execute active hooks. If it is run from a Dashboard
 * operation, it will execute all active back-end hooks. If it is run from the
 * front-end, it will execute all active front-end hook. Active hooks having
 * 'Everywhere' scope will be executed in any case.
 *
 * @since 3.6.0
 */
function wfu_execute_hooks() {
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	
	foreach ( $plugin_hooks as $hook ) {
		if ( $hook["status"] == 1 ) {
			if ( $hook["scope"] == 0 || ( $hook["scope"] == -1 && is_admin() ) || ( $hook["scope"] == 1 && !is_admin() ) )
				eval(wfu_plugin_decode_string($hook["code"]));
		}
	}
}

/**
 * Check Whether Active Hooks Use Session.
 *
 * This function checks whether there are any active hooks that use session. In
 * this case the plugin will not allow User State handler to change to dboption.
 *
 * @since 4.12.0
 *
 * @return bool true if there are active hooks using session, false otherwise.
 */
function wfu_active_hooks_using_session() {
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	$found = false;
	foreach ( $plugin_hooks as $hook ) {
		if ( $hook["status"] == 1 ) {
			if ( strpos(strtolower(wfu_plugin_decode_string($hook["code"])), '$_session') !== false ) {
				$found = true;
				break;
			}
		}
	}
	return $found;
}

/**
 * Deactivate All Hooks.
 *
 * This function will deactivate all hooks. It is useful when a hook causes the
 * website to crash and a mechanism is required to deactivate them.
 *
 * @since 4.14.0
 */
function wfu_deactivate_all_hooks() {
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) return;
	
	$changed_count = 0;
	foreach ( $plugin_hooks as $ind => $hook ) {
		if ( $hook["status"] == 1 ) {
			$plugin_hooks[$ind]["status"] = 0;
			$changed_count ++;
		}
	}
	if ( $changed_count > 0 ) update_option( "wordpress_file_upload_hooks", $plugin_hooks );
}