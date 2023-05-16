<?php

/**
 * AJAX Handlers of the Plugin
 *
 * This file contains AJAX handlers of the plugin.
 *
 * @link /lib/wfu_ajaxactions.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 2.1.2
 */

/**
 * Execute Email Notification Dispatching
 *
 * This function sends an email notification after files have been uploaded.
 *
 * @since 2.1.2
 */
function wfu_ajax_action_send_email_notification() {
	$_POST = stripslashes_deep($_POST);

	$user = wp_get_current_user();
	if ( 0 == $user->ID ) $is_admin = false;
	else $is_admin = current_user_can('manage_options');

	$params_index = sanitize_text_field($_POST['params_index']);
	$session_token = sanitize_text_field($_POST['session_token']);

	$arr = wfu_get_params_fields_from_index($params_index, $session_token);
	//check referer using server sessions to avoid CSRF attacks
	$sid = $arr['shortcode_id'];
	if ( WFU_USVAR("wfu_token_".$sid) != $session_token ) die();
	if ( $user->user_login != $arr['user_login'] ) {
		$arr = wfu_get_params_fields_from_index($params_index.'[|][|]'.$arr['page_id'].'[|][|]'.$sid.'[|][|]'.$arr['user_login'], $session_token);
		if ( $user->user_login != $arr['user_login'] ) die();
	}

	$params_str = get_option('wfu_params_'.$arr['unique_id']);
	$params = wfu_decode_array_from_string($params_str);
	/**
	 * Customize Params Array.
	 *
	 * This is an internal filter which allows to modify params array before it
	 * is used by the function.
	 *
	 * @since 4.14.0
	 *
	 * @param array $params The params array
	 * @param array $arr {
	 *     Basic information about the upload.
	 *
	 *     @type string $unique_id Optional. The unique ID of the upload.
	 *     @type string $page_id Optional. The post ID of the upload form.
	 *     @type array $shortcode_id Optional. The ID of the upload form.
	 *     @type array $user_login Optional. The username of the upload user.
	 * }
	 * @param string $caller A string identifying the caller of this filter.
	 */
	$params = apply_filters("_wfu_get_params", $params, $arr, 'wfu_ajax_action_send_email_notification');
	
	//check whether email notifications are activated
	if ( $params["notify"] != "true" ) die();
	
	$uniqueid = ( isset($_POST['uniqueuploadid_'.$sid]) ? sanitize_text_field($_POST['uniqueuploadid_'.$sid]) : "" );
	//uniqueid cannot be empty and cannot be "no-ajax"
	if ( $uniqueid == "" || $uniqueid == "no-ajax" ) die();

	//retrieve the list of uploaded files from session
	$target_path_list = array();
	$all_files_count = 0;
	if ( WFU_USVAR_exists("filedata_".$uniqueid) && is_array(WFU_USVAR("filedata_".$uniqueid)) ) {
		$all_files_count = count(WFU_USVAR("filedata_".$uniqueid));
		foreach ( WFU_USVAR("filedata_".$uniqueid) as $file ) {
			if ( $file["upload_result"] == "success" || $file["upload_result"] == "warning" )
				array_push($target_path_list, $file["filepath"]);
		}
	}
	$uploaded_files_count = count($target_path_list);
	$nofileupload = ( $params["allownofile"] == "true" && $all_files_count == 0 );
	$force_notifications = ( WFU_VAR("WFU_FORCE_NOTIFICATIONS") == "true" );
	
	//in case no files have been uploaded and this is not a nofileupload
	//situation and Force Email Notifications option is not active then abort
	if ( $uploaded_files_count == 0 && !$nofileupload && !$force_notifications ) die();
	
	/* initialize return array */
	$params_output_array["version"] = "full";
	$params_output_array["general"]['shortcode_id'] = $params["uploadid"];
	$params_output_array["general"]['unique_id'] = $uniqueid;
	$params_output_array["general"]['state'] = 0;
	$params_output_array["general"]['files_count'] = 0;
	$params_output_array["general"]['update_wpfilebase'] = "";
	$params_output_array["general"]['redirect_link'] = "";
	$params_output_array["general"]['upload_finish_time'] = "";
	$params_output_array["general"]['message'] = "";
	$params_output_array["general"]['message_type'] = "";
	$params_output_array["general"]['admin_messages']['wpfilebase'] = "";
	$params_output_array["general"]['admin_messages']['notify'] = "";
	$params_output_array["general"]['admin_messages']['redirect'] = "";
	$params_output_array["general"]['admin_messages']['other'] = "";
	$params_output_array["general"]['errors']['wpfilebase'] = "";
	$params_output_array["general"]['errors']['notify'] = "";
	$params_output_array["general"]['errors']['redirect'] = "";
	$params_output_array["general"]['color'] = "black";
	$params_output_array["general"]['bgcolor'] = "#F5F5F5";
	$params_output_array["general"]['borcolor'] = "#D3D3D3";
	$params_output_array["general"]['notify_by_email'] = 0;
	$params_output_array["general"]['fail_message'] = WFU_ERROR_UNKNOWN;

	//retrieve userdata, first get default userdata from $params
	$userdata_fields = $params["userdata_fields"];
	//remove any honeypot fields and initialize default values
	foreach ( $userdata_fields as $userdata_key => $userdata_field )
		if ( $userdata_field["type"] == "honeypot" ) unset($userdata_fields[$userdata_key]);
		else $userdata_fields[$userdata_key]["value"] = "";
	//then retrieve userdata from session if files exist
	if ( $all_files_count > 0 && WFU_USVAR_exists("filedata_".$uniqueid) && is_array(WFU_USVAR("filedata_".$uniqueid)) ) {
		foreach ( WFU_USVAR("filedata_".$uniqueid) as $file ) {
			if ( isset($file["user_data"]) ) {
				$userdata_fields = array();
				foreach ( $file["user_data"] as $userdata_key => $userdata_field )
					$userdata_fields[$userdata_key] = array( "label" => $userdata_field["label"], "value" => $userdata_field["value"] );
				break;
			}
		}
	}
	//in case there are no files in session (because allownofile attribute is
	//active and the user did not select any files for upload) then retrieve
	//userdata from the database based on uploadid
	else {
		$userdata_saved = wfu_get_userdata_from_uploadid($uniqueid);
		if ( $userdata_saved != null && is_array($userdata_saved) ) {
			$userdata_fields = array();
			foreach ( $userdata_saved as $userdata_saved_rec )
				$userdata_fields[$userdata_saved_rec->propkey] = array( "label" => $userdata_saved_rec->property, "value" => $userdata_saved_rec->propvalue );
		}
	}

	$send_error = wfu_send_notification_email($user, $target_path_list, $userdata_fields, $params);

	/* suppress any errors if user is not admin */
	if ( !$is_admin ) $send_error = "";

	if ( $send_error != "" ) {
		$params_output_array["general"]['admin_messages']['notify'] = $send_error;
		$params_output_array["general"]['errors']['notify'] = "error";
	}

	/* construct safe output */
	$sout = "0;".WFU_VAR("WFU_DEFAULTMESSAGECOLORS").";0";

	$echo_str = "wfu_fileupload_success::".$sout.":".wfu_encode_array_to_string($params_output_array);
	/**
	 * Customise Email Notification Result.
	 *
	 * This filter allows scripts to customise the result of email notification
	 * operation.
	 *
	 * @since 4.0.0
	 *
	 * @param string $echo_str The result of email notification operation.
	 */
	$echo_str = apply_filters('_wfu_ajax_action_send_email_notification', $echo_str);
	
	die($echo_str); 
}

/**
 * Execute Pre-Upload Actions
 *
 * This function executes server-side actions before the upload starts, in order
 * to determine whether the upload will continue, or any other custom actions.
 *
 * @since 3.7.0
 */
function wfu_ajax_action_ask_server() {
	if ( !isset($_REQUEST['session_token']) || !isset($_REQUEST['sid']) || !isset($_REQUEST['unique_id']) ) die();

	$_REQUEST = stripslashes_deep($_REQUEST);

	$session_token = sanitize_text_field( $_REQUEST["session_token"] );
	$sid = sanitize_text_field( $_REQUEST["sid"] );
	$unique_id = wfu_sanitize_code($_REQUEST['unique_id']);
	if ( $session_token == "" ) die();
	//check referrer using Wordpress nonces and server sessions to avoid CSRF attacks
	check_ajax_referer( 'wfu-uploader-nonce', 'wfu_uploader_nonce' );
	if ( WFU_USVAR("wfu_token_".$sid) != $session_token ) die();
	
	//prepare parameters for before-upload filters
	$ret = array( "status" => "", "echo" => "" );
	//retrieve file names and sizes from request parameters
	$filenames_raw = ( isset($_REQUEST['filenames']) ? $_REQUEST['filenames'] : "" );
	$filenames = array();
	if ( trim($filenames_raw) != "" ) $filenames = explode(";", $filenames_raw);
	//use wfu_basename() function in order to avoid directory traversal attacks
	foreach ( $filenames as $ind => $filename ) $filenames[$ind] = wfu_basename(esc_attr(wfu_plugin_decode_string(trim($filename))));
	$filesizes_raw = ( isset($_REQUEST['filesizes']) ? $_REQUEST['filesizes'] : "" );
	$filesizes = array();
	if ( trim($filesizes_raw) != "" ) $filesizes = explode(";", $filesizes_raw);
	foreach ( $filesizes as $ind => $filesize ) $filesizes[$ind] = wfu_sanitize_int($filesize);
	$files = array();
	foreach ( $filenames as $ind => $filename ) {
		$filesize = "";
		if ( isset($filesizes[$ind]) ) $filesize = $filesizes[$ind];
		array_push($files, array( "filename" => $filename, "filesize" => $filesize ));
	}
	$attr = array( "sid" => $sid, "unique_id" => $unique_id, "files" => $files );
	//execute before upload filters
	$echo_str = "";
	//first execute any custom filters created by admin
	if ( has_filter("wfu_before_upload") ) {
		$changable_data = array( "error_message" => "", "js_script" => "" );
		/**
		 * Execute Custom Actions Before Upload Starts.
		 *
		 * This filter allows to execute custom actions before upload starts. It
		 * can cancel the upload returning an error message.
		 *
		 * @since 3.7.0
		 *
		 * @param array $changable_data {
		 *     Controls the upload.
		 *
		 *     @type string $error_message An error message to display if the
		 *           upload must be cancelled.
		 *     @type string $js_script Javascript code to execute on user's
		 *           browser after this filter finishes.
		 * }
		 * @param array $attr {
		 *     Various attributes of the upload.
		 *
		 *     @type string $sid The ID of the shortcode.
		 *     @type string $unique_id The unique ID of the upload.
		 *     @type array $files {
		 *         Contains an array of the uploaded files.
		 *
		 *         @type array $file {
		 *             Contains information for each uploaded file.
		 *
		 *             @type string $filename The filename of the file.
		 *             @type int $filesize The size of the file.
		 *         }
		 *     }
		 * }
		 */
		$changable_data = apply_filters("wfu_before_upload", $changable_data, $attr);
		if ( $changable_data["error_message"] == "" ) $ret["status"] = "success";
		else {
			$ret["status"] = "error";
			$echo_str .= "CBUV[".$changable_data["error_message"]."]";
		}
		if ( $changable_data["js_script"] != "" ) $echo_str .= "CBUVJS[".wfu_plugin_encode_string($changable_data["js_script"])."]";
	}
	/**
	 * Execute Custom Internal Actions Before Upload Starts.
	 *
	 * This filter allows to execute custom internal actions by extensions
	 * before upload starts. It can cancel the upload.
	 *
	 * @since 3.7.0
	 *
	 * @param array $ret {
	 *     Controls the upload and output of this function.
	 *
	 *     @type string $status The status of the upload. It must be 'success'
	 *           or 'error'.
	 *     @type string $echo Additional content to the echoed by the function.
	 * }
	 * @param array $attr {
	 *     Various attributes of the upload.
	 *
	 *     @type string $sid The ID of the shortcode.
	 *     @type string $unique_id The unique ID of the upload.
	 *     @type array $files {
	 *         Contains an array of the uploaded files.
	 *
	 *         @type array $file {
	 *             Contains information for each uploaded file.
	 *
	 *             @type string $filename The filename of the file.
	 *             @type int $filesize The size of the file.
	 *         }
	 *     }
	 * }
	 */
	$ret = apply_filters("_wfu_before_upload", $ret, $attr);
	$echo_str .= $ret["echo"];
	//in case that no filters were executed, because $ret["status"] is
	//empty, then this call to wfu_ajax_action_ask_server was erroneous
	if ( $ret["status"] == "" )	$ret["status"] = "die";
	//create an internal flag stored in session regarding the status of this
	//upload, that will be used to verify or not the upload
	if ( $ret["status"] == "success" ) WFU_USVAR_store("wfu_uploadstatus_".$attr["unique_id"], 1);
	else WFU_USVAR_store("wfu_uploadstatus_".$attr["unique_id"], 0);
	
	if ( $ret["status"] == "success" || $ret["status"] == "error" )
		echo "wfu_askserver_".$ret["status"].":".$echo_str;
	
	die();
}

/**
 * Execute Cancellation of Classic Upload
 *
 * This function sets the necessary User State variables to denote cancellation
 * of the upload that was requested by the user. This function applies only to
 * classic (non-AJAX) uploads.
 *
 * @since 4.0.0
 */
function wfu_ajax_action_cancel_upload() {
	if ( !isset($_REQUEST['session_token']) || !isset($_REQUEST['sid']) || !isset($_REQUEST['unique_id']) ) die();

	$_REQUEST = stripslashes_deep($_REQUEST);

	$session_token = sanitize_text_field( $_REQUEST["session_token"] );
	$sid = sanitize_text_field( $_REQUEST["sid"] );
	$unique_id = wfu_sanitize_code($_REQUEST['unique_id']);
	if ( $session_token == "" ) die();
	//check referrer using Wordpress nonces and server sessions to avoid CSRF attacks
	check_ajax_referer( 'wfu-uploader-nonce', 'wfu_uploader_nonce' );
	if ( WFU_USVAR("wfu_token_".$sid) != $session_token ) die();
	
	//setting status to 0 denotes cancelling of the upload
	WFU_USVAR_store("wfu_uploadstatus_".$unique_id, 0);
	
	die("success");
}

/**
 * Execute AJAX Upload
 *
 * This function is the main callback of an AJAX upload of a whole file or a
 * chunk. It performs security checks to verify the user, then it performs pre-
 * upload actions, then it executes wfu_process_files() function that processes
 * and saves the files and then performs post-upload actions and filters.
 *
 * @since 2.1.2
 *
 * @global string $wfu_user_state_handler The defined User State handler.
 */
function wfu_ajax_action_callback() {
	global $wfu_user_state_handler;
	if ( !isset($_REQUEST['session_token']) ) die();

	$_REQUEST = stripslashes_deep($_REQUEST);
	$_POST = stripslashes_deep($_POST);
	
	$session_token = sanitize_text_field( $_REQUEST["session_token"] );
	if ( $session_token == "" ) die();
	check_ajax_referer( 'wfu-uploader-nonce', 'wfu_uploader_nonce' );

	if ( !isset($_REQUEST['params_index']) ) die();
	
	$params_index = sanitize_text_field( $_REQUEST["params_index"] );
	
	if ( $params_index == "" ) die();
	
	$user = wp_get_current_user();
	$arr = wfu_get_params_fields_from_index($params_index, $session_token);
	$sid = $arr['shortcode_id'];
	//check referrer using server sessions to avoid CSRF attacks
	if ( WFU_USVAR("wfu_token_".$sid) != $session_token ) {
		$echo_str = "Session failed!<br/><br/>Session Data:<br/>";
		$echo_str .= print_r(wfu_sanitize(WFU_USALL()), true);
		$echo_str .= "<br/><br/>Post Data:<br/>";
		$echo_str .= print_r(wfu_sanitize($_REQUEST), true);
		$echo_str .= 'force_errorabort_code';
		/**
		 * Customise Output on Session Error.
		 *
		 * This filter allows scripts to customise output of the function in
		 * case of session error.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The output in case of session error.
		 */
		$echo_str = apply_filters('_wfu_upload_session_failed', $echo_str);
		die($echo_str);
	}

	if ( $user->user_login != $arr['user_login'] ) {
		$arr = wfu_get_params_fields_from_index($params_index.'[|][|]'.$arr['page_id'].'[|][|]'.$sid.'[|][|]'.$arr['user_login'], $session_token);
		if ( $user->user_login != $arr['user_login'] ) {
			$echo_str = "User failed!<br/><br/>User Data:<br/>";
			$echo_str .= print_r(wfu_sanitize($user), true);
			$echo_str .= "<br/><br/>Post Data:<br/>";
			$echo_str .= print_r(wfu_sanitize($_REQUEST), true);
			$echo_str .= "<br/><br/>Params Data:<br/>";
			$echo_str .= print_r(wfu_sanitize($arr), true);
			$echo_str .= 'force_errorabort_code';
			/**
			 * Customise Output on User Error.
			 *
			 * This filter allows scripts to customise output of the function in
			 * case of user error.
			 *
			 * @since 3.11.0
			 *
			 * @param string $echo_str The output in case of user error.
			 */
			$echo_str = apply_filters('_wfu_upload_user_failed', $echo_str);
			die($echo_str);
		}
	}

	//if force_connection_close is set, then the first pass to this callback
	//script is for closing the previous connection
	if ( isset($_POST["force_connection_close"]) && $_POST["force_connection_close"] === "1" ) {
		header("Connection: Close");
		/**
		 * Customise Output on Forced Connection Close.
		 *
		 * This filter allows scripts to customise return of the function in
		 * case of forced connection close.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return in case of forced connection
		 *        close.
		 */
		die(apply_filters('_wfu_upload_force_connection_close', 'success'));
	}
	
	//get the unique id of the upload
	$unique_id = ( isset($_POST['uniqueuploadid_'.$sid]) ? sanitize_text_field($_POST['uniqueuploadid_'.$sid]) : "" );
	if ( strlen($unique_id) != 10 ) {
		/**
		 * Customise Output on Unique ID Fail.
		 *
		 * This filter allows scripts to customise return of the function in
		 * case that the retrieved unique ID is invalid.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return in case of unique ID fail.
		 */
		die(apply_filters('_wfu_upload_uniqueid_failed', 'force_errorabort_code'));
	}
	
	//if before upload actions have been executed and they have rejected the 
	//upload, but for some reason (hack attempt) the upload continued, then
	//terminate it
	if ( WFU_USVAR_exists("wfu_uploadstatus_".$unique_id) && WFU_USVAR("wfu_uploadstatus_".$unique_id) == 0 ) die('force_errorabort_code');
	
	//get stored shortcode parameters
	$params_str = get_option('wfu_params_'.$arr['unique_id']);
	$params = wfu_decode_array_from_string($params_str);
	/** This filter is documented above. */
	$params = apply_filters("_wfu_get_params", $params, $arr, 'wfu_ajax_action_callback');
	
	//if upload has finished then perform post upload actions
	if ( isset($_POST["upload_finished"]) && $_POST["upload_finished"] === "1" ) {
		//remove any queues that were generated during the upload process
		wfu_remove_queue($unique_id);
		//execute routine for checking of unfinished chunked files
		wfu_checkdelete_unfinished_files();
		$echo_str = "";
		//execute after upload filters
		$ret = wfu_execute_after_upload_filters($sid, $unique_id, $params);
		if ( $ret["js_script"] != "" ) $echo_str = "CBUVJS[".wfu_plugin_encode_string($ret["js_script"])."]";
		die($echo_str);
	}
	
	//check if honeypot userdata fields have been added to the form and if they
	//contain any data; if wfu_check_remove_honeypot_fields returns true this
	//means that at least one honeypot field has beed filled with a value and
	//the upload must be aborted because it was not done by a human
	if ( $params["userdata"] == "true" && wfu_check_remove_honeypot_fields($params["userdata_fields"], 'hiddeninput_'.$sid.'_userdata_') ) die("force_abortsuccess_code");

	//apply filters to determine if the upload will continue or stop
	$ret = array( "status" => "", "echo" => "" );
	$attr = array( "sid" => $sid, "unique_id" => $unique_id, "params" => $params );
	/**
	 * Execute Pre-Upload Checks.
	 *
	 * This is an internal filter which allows to execute custom actions right
	 * before an upload starts. It can cancel the upload.
	 *
	 * @since 3.7.0
	 *
	 * @param array $ret {
	 *     Controls the Upload.
	 *
	 *     @type string $status Status of the upload. If it is 'die' then the
	 *           upload will be cancelled.
	 *     @type string $echo A message to return in case of upload
	 *           cancellation.
	 * }
	 * @param array $attr {
	 *     Various attributes of the upload.
	 *
	 *     @type string $sid The ID of the shortcode.
	 *     @type string $unique_id The unique ID of the upload.
	 *     @type array $params The shortcode parameters of the upload form.
	 */
	$ret = apply_filters("_wfu_pre_upload_check", $ret, $attr);
	if ( $ret["status"] == "die" ) die($ret["echo"]);

	//if this is the first pass of an upload attempt then perform pre-upload actions
	if ( !WFU_USVAR_exists('wfu_upload_first_pass_'.$unique_id) || WFU_USVAR('wfu_upload_first_pass_'.$unique_id) != 'true' ) {
		//execute routine for checking of unfinished chunked files
		wfu_checkdelete_unfinished_files();
		//execute routine for checking of pending file transfers
		wfu_schedule_transfermanager();
		WFU_USVAR_store('wfu_upload_first_pass_'.$unique_id, 'true');
	}

	if ( !isset($_POST["subdir_sel_index"]) ) die();
	$subdir_sel_index = sanitize_text_field( $_POST["subdir_sel_index"] );
	$params['subdir_selection_index'] = $subdir_sel_index;
	WFU_USVAR_store('wfu_check_refresh_'.$params["uploadid"], 'do not process');
	
	//update consent status of user
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	$params["consent_result"] = wfu_check_user_consent($user);
	$require_consent = ( $plugin_options["personaldata"] == "1" && ( $params["notrememberconsent"] == "true" || $params["consent_result"] == "" ) && $params["askconsent"] == "true" );
	if ( $require_consent ) {
		if ( !isset($_POST['consent_result']) ) die();
		$consent_result = ( $_POST['consent_result'] == "yes" ? "yes" : ( $_POST['consent_result'] == "no" ? "no" : "" ) );
		$params["consent_result"] = ( $_POST['consent_result'] == "yes" ? "1" : ( $_POST['consent_result'] == "no" ? "0" : "" ) );
		wfu_update_user_consent($user, $consent_result);
	}

	if ( $wfu_user_state_handler == "dboption" )
		$proc_ret = wfu_run_process_in_queue($unique_id, "wfu_process_files", array( $params, 'ajax' ));
	else {
		$proc_ret["result"] = true;
		$proc_ret["output"] = wfu_process_files($params, 'ajax');
	}
	$echo_str = "";
	if ( $proc_ret["result"] ) {
		$wfu_process_file_array = $proc_ret["output"];
		//extract safe_output from wfu_process_file_array and pass it as
		//separate part of the response text
		$safe_output = $wfu_process_file_array["general"]['safe_output'];
		unset($wfu_process_file_array["general"]['safe_output']);
		//get javascript code that has been defined in wfu_after_file_upload
		//action
		$js_script = wfu_plugin_encode_string($wfu_process_file_array["general"]['js_script']);
		unset($wfu_process_file_array["general"]['js_script']);

		$echo_str = "wfu_fileupload_success:".$js_script.":".$safe_output.":".wfu_encode_array_to_string($wfu_process_file_array);
	}
	elseif ( $proc_ret["error"] == "abort_thread" ) wfu_advance_queue($unique_id);
	/**
	 * Customise Output of Successful AJAX Upload.
	 *
	 * This filter allows scripts to customise return of the function in case
	 * that the AJAX upload was successful.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return in case of successful AJAX upload.
	 */
	$echo_str = apply_filters('_wfu_upload_callback_success', $echo_str);
	die($echo_str); 
}

/**
 * Execute Saving of Shortcode
 *
 * This function executes saving of a shortcode after it has been edited through
 * the shortcode composer.
 *
 * @since 2.1.3
 */
function wfu_ajax_action_save_shortcode() {
	$is_admin = current_user_can( 'manage_options' );
	$can_open_composer = ( WFU_VAR("WFU_SHORTCODECOMPOSER_NOADMIN") == "true" && ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) ) );
	if ( !$is_admin && !$can_open_composer ) die();
	if ( !isset($_POST['shortcode']) || !isset($_POST['shortcode_original']) || !isset($_POST['post_id']) || !isset($_POST['post_hash']) || !isset($_POST['shortcode_position']) || !isset($_POST['shortcode_tag']) || !isset($_POST['widget_id']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	//sanitize parameters
	$shortcode = wfu_sanitize_code($_POST['shortcode']);
	$shortcode_original = wfu_sanitize_code($_POST['shortcode_original']);
	$post_id = wfu_sanitize_int($_POST['post_id']);
	$post_hash = wfu_sanitize_code($_POST['post_hash']);
	$shortcode_position = wfu_sanitize_int($_POST['shortcode_position']);
	$shortcode_tag = wfu_sanitize_tag($_POST['shortcode_tag']);
	$widget_id = sanitize_text_field($_POST['widget_id']);

	$shortcode = wfu_sanitize_shortcode(wfu_plugin_decode_string($shortcode), $shortcode_tag);
	
	if ( $post_id == "" && $widget_id == "" ) {
		die();
	}
	else {
		$data['post_id'] = $post_id;
		$data['post_hash'] = $post_hash;
		$data['shortcode'] = wfu_plugin_decode_string($shortcode_original);
		$data['position'] = $shortcode_position;
		if ( $post_id != "" && !wfu_check_edit_shortcode($data) ) $echo_str = "wfu_save_shortcode:fail:post_modified";
		else {
			if ( $widget_id == "" ) {
				$new_shortcode = "[".$shortcode_tag." ".$shortcode."]";
				if ( wfu_replace_shortcode($data, $new_shortcode) ) {
					$post = get_post($post_id);
					/** This filter is described in wfu_loader.php */
					$content = apply_filters("_wfu_get_post_content", $post->post_content, $post);
					$hash = hash('md5', $content);
					$echo_str = "wfu_save_shortcode:success:".$hash;
				}
				else $echo_str = "wfu_save_shortcode:fail:post_update_failed";
			}
			else {
				$widget_obj = wfu_get_widget_obj_from_id($widget_id);
				if ( $widget_obj === false ) $echo_str = "wfu_save_shortcode:fail:post_update_failed";
				else {
					$widget_sidebar = is_active_widget(false, $widget_id, "wordpress_file_upload_widget");
					if ( !$widget_sidebar ) $echo_str = "wfu_save_shortcode:fail:post_update_failed";
					else {
						$widget_obj->update_external($shortcode);
						$hash = $data['post_hash'];
						$echo_str = "wfu_save_shortcode:success:".$hash;
					}
				}
			}
		}
	}

	/**
	 * Customise Output on Shortcode Saving.
	 *
	 * This filter allows scripts to customise return of the function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	$echo_str = apply_filters('_wfu_ajax_action_save_shortcode', $echo_str);
	die($echo_str);
}

/**
 * Execute Checking of Post Contents
 *
 * This function executes checking of post contents to determine whether they
 * are current or obsolete (they have changed).
 *
 * @since 2.6.0
 */
function wfu_ajax_action_check_page_contents() {
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( !isset($_POST['post_id']) || !isset($_POST['post_hash']) ) die();
	if ( $_POST['post_id'] == "" ) die();
	
	$_POST = stripslashes_deep($_POST);

	$data['post_id'] = wfu_sanitize_int($_POST['post_id']);
	$data['post_hash'] = wfu_sanitize_code($_POST['post_hash']);
	if ( wfu_check_edit_shortcode($data) ) $echo_str = "wfu_check_page_contents:current:";
	else $echo_str = "wfu_check_page_contents:obsolete:";

	/**
	 * Customise Output of Post Contents Checking Function.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	$echo_str = apply_filters('_wfu_ajax_action_check_page_contents', $echo_str);
	die($echo_str);
}

/**
 * Initiate Editing of Shortcode
 *
 * This function invokes the shortcode composer in order to edit a shortcode. It
 * applies when editing a shortcode from Main page of the plugin in Dashboard or
 * from a front-end post or page or from a sidebar.
 *
 * @since 2.6.0
 */
function wfu_ajax_action_edit_shortcode() {
	global $wp_registered_widgets;
	global $wp_registered_sidebars;
	
	$is_admin = current_user_can( 'manage_options' );
	$can_open_composer = ( WFU_VAR("WFU_SHORTCODECOMPOSER_NOADMIN") == "true" && ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) ) );
	if ( !$is_admin && !$can_open_composer ) die();
	if ( !isset($_POST['upload_id']) || !isset($_POST['post_id']) || !isset($_POST['post_hash']) || !isset($_POST['shortcode_tag']) || !isset($_POST['widget_id']) ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	//sanitize parameters
	$upload_id = sanitize_text_field($_POST['upload_id']);
	$widget_id = sanitize_text_field($_POST['widget_id']);
	$post_id = wfu_sanitize_int($_POST['post_id']);
	$post_hash = wfu_sanitize_code($_POST['post_hash']);
	$shortcode_tag = wfu_sanitize_tag($_POST['shortcode_tag']);

	$keyname = "uploadid";
	if ( $shortcode_tag == "wordpress_file_upload_browser" ) $keyname = "browserid";

	$data['post_id'] = $post_id;
	$data['post_hash'] = $post_hash;
	if ( wfu_check_edit_shortcode($data) ) {
		if ( $widget_id == "" ) {
			$post = get_post($data['post_id']);
			//get default value for uploadid
			if ( $shortcode_tag == "wordpress_file_upload_browser" ) $defs = wfu_browser_attribute_definitions();
			else $defs = wfu_attribute_definitions();
			$default = "";
			foreach ( $defs as $key => $def ) {
				if ( $def['attribute'] == $keyname ) {
					$default = $def['value'];
					break;
				}
			}
			//get page shortcodes
			$wfu_shortcodes = wfu_get_content_shortcodes($post, $shortcode_tag);
			//find the shortcodes' uploadid and the correct one
			$validkey = -1;
			foreach ( $wfu_shortcodes as $key => $data ) {
				$shortcode = trim(substr($data['shortcode'], strlen('['.$shortcode_tag), -1));
				$shortcode_attrs = wfu_shortcode_string_to_array($shortcode);
				if ( array_key_exists($keyname, $shortcode_attrs) ) $uploadid = $shortcode_attrs[$keyname];
				else $uploadid = $default;
				if ( $uploadid == $upload_id ) {
					$validkey = $key;
					break;
				}
			}
			if ( $validkey == -1 ) die();
			$data_enc = wfu_safe_store_shortcode_data(wfu_encode_array_to_string($wfu_shortcodes[$validkey]));
		}
		else {
			$widget_obj = wfu_get_widget_obj_from_id($widget_id);
			if ( $widget_obj === false ) die();
			$widget_sidebar = is_active_widget(false, $widget_id, "wordpress_file_upload_widget");
			if ( !$widget_sidebar ) die();
			if ( isset($wp_registered_sidebars[$widget_sidebar]) && isset($wp_registered_sidebars[$widget_sidebar]['name']) ) $widget_sidebar = $wp_registered_sidebars[$widget_sidebar]['name'];
			$data['shortcode'] = $widget_obj->shortcode();
			$data['position'] = 0;
			$data['widgetid'] = $widget_id;
			$data['sidebar'] = $widget_sidebar;
			$data_enc = wfu_safe_store_shortcode_data(wfu_encode_array_to_string($data));
		}
		if( $is_admin ) $url = site_url().'/wp-admin/options-general.php?page=wordpress_file_upload&tag='.$shortcode_tag.'&action=edit_shortcode&data='.$data_enc;
		//conditional that will open the shortcode composer for non-admin users
		//who can edit posts or pages
		else $url = site_url().'/wp-admin/admin.php?page=wordpress_file_upload&tag='.$shortcode_tag.'&action=edit_shortcode&data='.$data_enc;
		$echo_str = "wfu_edit_shortcode:success:".wfu_plugin_encode_string($url);
	}
	else $echo_str = "wfu_edit_shortcode:check_page_obsolete:".WFU_ERROR_PAGE_OBSOLETE;

	/**
	 * Customise Output of Shortcode Editing Initiation Function.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	$echo_str = apply_filters('_wfu_ajax_action_edit_shortcode', $echo_str);
	die($echo_str);
}

/**
 * Initiate Editing of Shortcode in Gutenberg Editor
 *
 * This function invokes the shortcode composer in order to edit a shortcode. It
 * applies when editing a shortcode from the new Gutenberg page editor of
 * Wordpress.
 *
 * @since 4.11.0
 */
function wfu_ajax_action_gutedit_shortcode() {
	$is_admin = current_user_can( 'manage_options' );
	$can_open_composer = ( WFU_VAR("WFU_SHORTCODECOMPOSER_NOADMIN") == "true" && ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) ) );
	if ( !$is_admin && !$can_open_composer ) die();
	if ( !isset($_POST['shortcode']) || !isset($_POST['post_id']) || !isset($_POST['shortcode_tag']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	//sanitize parameters
	$shortcode = wfu_sanitize_code($_POST['shortcode']);
	$post_id = wfu_sanitize_int($_POST['post_id']);
	$shortcode_tag = wfu_sanitize_tag($_POST['shortcode_tag']);

	$shortcode = wfu_sanitize_shortcode(wfu_plugin_decode_string($shortcode), $shortcode_tag);
	
	if ( $post_id == "" ) die();

	$data['shortcode'] = '['.$shortcode_tag.' '.$shortcode.']';
	$data['post_id'] = $post_id;
	$data['post_hash'] = '';
	$data['position'] = 0;
	$data_enc = wfu_safe_store_shortcode_data(wfu_encode_array_to_string($data));
	if ( $is_admin ) $url = site_url().'/wp-admin/options-general.php?page=wordpress_file_upload&tag='.$shortcode_tag.'&action=edit_shortcode&data='.$data_enc.'&referer=guteditor';
	//conditional that will open the shortcode composer for non-admin users who
	//can edit posts or pages
	else $url = site_url().'/wp-admin/admin.php?page=wordpress_file_upload&tag='.$shortcode_tag.'&action=edit_shortcode&data='.$data_enc.'&referer=guteditor';

	$echo_str = "wfu_gutedit_shortcode:success:".wfu_plugin_encode_string($url);
	/**
	 * Customise Output of Gutenberg Shortcode Editing Initiation Function.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 4.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	$echo_str = apply_filters('_wfu_ajax_action_gutedit_shortcode', $echo_str);
	die($echo_str);
}

/**
 * Retrieve Subfolder Structure of a Folder
 *
 * This function is used to retrieve the subfolder structure of a folder,
 * together with the subfolder structure of one of the subfolders. It is used
 * when defining the subfolders of the subfolders element of the upload form
 * using the shortcoe composer.
 *
 * @since 2.4.1
 */
function wfu_ajax_action_read_subfolders() {
	if ( !isset($_POST['folder1']) || !isset($_POST['folder2']) ) die();

	$_POST = stripslashes_deep($_POST);

	$folder1 = wfu_sanitize_code($_POST['folder1']);
	$folder1 = wfu_sanitize_url(wfu_plugin_decode_string($folder1));
	$folder2 = wfu_sanitize_code($_POST['folder2']);
	$folder2 = wfu_sanitize_url(wfu_plugin_decode_string($folder2));
	if ( wfu_plugin_encode_string($folder1) != $_POST['folder1'] || wfu_plugin_encode_string($folder2) != $_POST['folder2'] ) die();

	$temp_params = array( 'uploadpath' => $folder1, 'accessmethod' => 'normal', 'ftpinfo' => '', 'useftpdomain' => 'false' );
	$path = wfu_upload_plugin_full_path($temp_params);

	if ( !is_dir($path) ) {
		/**
		 * Customise Output of Shortcode Subfolder Structure Retrieval.
		 *
		 * This filter allows scripts to customise return of this function after
		 * finish of it.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_read_subfolders', 'wfu_read_subfolders:error:Parent folder is not valid! Cannot retrieve subfolder list.'));
	}

	$path2 = $folder2;
	$dirlist = "";
	if ( $handle = opendir($path) ) {
		$blacklist = array('.', '..');
		while ( false !== ($file = readdir($handle)) )
			if ( !in_array($file, $blacklist) ) {
				$filepath = $path.$file;
				if ( is_dir($filepath) ) {
					if ( $file == $path2 ) $file = '[['.$file.']]';
					$dirlist .= ( $dirlist == "" ? "" : "," ).$file;
				}
			}
		closedir($handle);
	}
	if ( $path2 != "" ) {
		$dirlist2 = $path2;
		$path .= $path2."/";
		if ( is_dir($path) ) {
			if ( $handle = opendir($path) ) {
				$blacklist = array('.', '..');
				while ( false !== ($file = readdir($handle)) )
					if ( !in_array($file, $blacklist) ) {
						$filepath = $path.$file;
						if ( is_dir($filepath) )
							$dirlist2 .= ",*".$file;
					}
				closedir($handle);
			}
		}
		$dirlist = str_replace('[['.$path2.']]', $dirlist2, $dirlist);
	}

	/** This filter is documnted above. */
	die(apply_filters('_wfu_ajax_action_read_subfolders', "wfu_read_subfolders:success:".wfu_plugin_encode_string($dirlist)));
}

/**
 * Initiate a File Download
 *
 * This function initiates a file download. It will first check whether the user
 * has the right to download the file. Then it will return an iframe element
 * that will start the download. Short life tokens are used in order to avoid
 * CSRF attacks. Download is executed outside Wordpress enviroment because some
 * times Wordpress environment outputs warnings that are downloaded with the
 * file, resulting in a broken download.
 *
 * @since 2.6.0
 */
function wfu_ajax_action_download_file_invoker() {
	global $wfu_user_state_handler;

	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	
	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $file_code == '' || $nonce == '' ) die();

	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_download_file_invoker') ) die();
	
	//check if user is allowed to download files
	if ( !current_user_can( 'manage_options' ) ) {
		//if $_POST['browser'] exists, then this is a call from a front-end browser
		if ( isset($_POST['browser']) ) {
			$browser_code = wfu_sanitize_code($_POST['browser']);
			//get params for this browser
			$params = wfu_get_browser_params_from_safe($browser_code);
			if ( $params === false ) die();
			if ( !isset($params["candownload"]) || $params["candownload"] != "true" ) die();
		}
		//else this is a call from a back-end browser
		else {
			$permissions = wfu_get_current_user_browser_permissions();
			if ( !$permissions['download'] ) die();
		}
	}
	
	$cookies = array();
	$use_cookies = ( $wfu_user_state_handler == "dboption" && WFU_VAR("WFU_US_DBOPTION_BASE") == "cookies" );
	$file_code = sanitize_text_field($file_code);
	//if file_code is exportdata, then export of data has been requested and
	//we need to create a file with export data and recreate file_code
	if ( substr($file_code, 0, 10) == "exportdata" && current_user_can( 'manage_options' ) ) {
		$params = null;
		$params_str = substr($file_code, 11);
		if ( trim($params_str) != "" ) $params = json_decode($params_str, true);
		$filepath = wfu_export_uploaded_files($params);
		if ( $filepath === false ) die();
		$file_code = "exportdata".wfu_safe_store_filepath($filepath);
		//store filepath in user state otherwise it can not be retrieved by
		//downloader script
		if ( !$use_cookies ) WFU_USVAR_store_session('wfu_storage_'.substr($file_code, 10), $filepath);
		else array_push($cookies, '{name: "wfu_storage_'.substr($file_code, 10).'", value: "'.$filepath.'", expires: 30}');
	}
	//else get the file path from the safe
	else {
		$filepath = wfu_get_filepath_from_safe($file_code);
		if ( $filepath === false ) die();
		$filepath = wfu_path_rel2abs(wfu_flatten_path($filepath));
		//reject download of blacklisted file types for security reasons
		if ( wfu_file_extension_blacklisted($filepath) ) {
			/**
			 * Customise Output of Download Initiation Operation.
			 *
			 * This filter allows scripts to customise return of this function
			 * after finish of it.
			 *
			 * @since 3.11.0
			 *
			 * @param string $echo_str The return of the function.
			 */
			die(apply_filters('_wfu_ajax_action_download_file_invoker', 'wfu_ajax_action_download_file_invoker:not_allowed:'.( isset($_POST['browser']) ? WFU_BROWSER_DOWNLOADFILE_NOTALLOWED : 'You are not allowed to download this file!' )));
		}
		//for front-end browser apply wfu_browser_check_file_action filter to
		//allow or restrict the download
		if ( isset($_POST['browser']) ) {
			$changable_data["error_message"] = "";
			$filerec = wfu_get_file_rec($filepath, true);
			$userdata = array();
			foreach ( $filerec->userdata as $data )
				array_push($userdata, array( "label" => $data->property, "value" => $data->propvalue ));
			$additional_data = array(
				"file_action"	=> "download",
				"filepath"		=> $filepath,
				"uploaduser"	=> $filerec->uploaduserid,
				"userdata"		=> $userdata
			);
			/**
			 * Check if Action is Allowed in Front-End File Browser.
			 *
			 * This filter allows scripts to check whether the action on a file
			 * from the front-end file browser is allowed.
			 *
			 * @since 3.7.2
			 *
			 * @param array $changable_data {
			 *        Controls allowance or rejection of the action.
			 *
			 *        @type string $error_message An error message to return in
			 *              case the action must be rejected.
			 * }
			 * @param array $additional_data {
			 *        Additional data of the file action operation.
			 *
			 *        @type string $file_action The performed action.
			 *        @type string $filepath The full path of the file.
			 *        @type string $uploaduser The ID of the user who uploaded
			 *              the file.
			 *        @type array $userdata Each item of the array contains the
			 *              label and value of any additional userdata exist
			 *              together with the file.
			 * }
			 */
			$changable_data = apply_filters("wfu_browser_check_file_action", $changable_data, $additional_data);
			if ( $changable_data["error_message"] != "" )
				/** This filter is documented above. */
				die(apply_filters('_wfu_ajax_action_download_file_invoker', 'wfu_ajax_action_download_file_invoker:not_allowed:'.$changable_data["error_message"]));
		}
		//for back-end browser check if user is allowed to perform this action
		//on this file
		if ( !( isset($_POST['browser']) ) && !wfu_current_user_owes_file($filepath) ) die();
		//store filepath in user state otherwise it can not be retrieved by
		//downloader script
		if ( !$use_cookies ) WFU_USVAR_store_session('wfu_storage_'.$file_code, wfu_get_filepath_from_safe($file_code));
		else array_push($cookies, '{name: "wfu_storage_'.$file_code.'", value: "'.wfu_get_filepath_from_safe($file_code).'", expires: 30}');
	}
	
	//generate download unique id to monitor this download
	$download_id = wfu_create_random_string(16);
	//store download status of this download in user state, so that it can be
	//changed by downloader script; it is noted that the downloader script
	//does not load WP environment, so in case of dboption it cannot access the
	//download status (which is stored in the database); however the downloader
	//script does not need to read it; it only needs to change it after the
	//download; so after the download, the downloader script loads WP
	//environment, so that it can change download status
	WFU_USVAR_store('wfu_download_status_'.$download_id, 'starting');
	//generate download ticket which expires in 30sec and store it in user
	//state; it will be used as security measure for the downloader script,
	//which runs outside Wordpress environment; it is noted that the downloader
	//script needs to read download ticket before the download; however in the
	//case of dboption the only way to achieve this is to store it in a cookie
	if ( !$use_cookies ) WFU_USVAR_store_session('wfu_download_ticket_'.$download_id, time() + 30);
	else array_push($cookies, '{name: "wfu_download_ticket_'.$download_id.'", value: '.(time() + 30).', expires: 30}');
	//generate download monitor ticket which expires in 30sec and store it in
	//user state; it will be used as security measure for the monitor script
	//that will check download status; it is noted that there is no reason to
	//store download monitor ticket in a cookie in case of dboption, because it
	//is not needed to be read by the downloader script
	WFU_USVAR_store('wfu_download_monitor_ticket_'.$download_id, time() + 30);

	//store ABSPATH in user state so that it can be used by download script;
	//again, in case of dboption, the only way the downloader script can read it
	//is to store it in a cookie
	if ( !$use_cookies ) WFU_USVAR_store_session('wfu_ABSPATH', wfu_abspath());
	else array_push($cookies, '{name: "wfu_ABSPATH", value: "'.urlencode(wfu_abspath()).'", expires: 30}');
	//store translatable strings to user state so that they can be used by a
	//script that runs outside Wordpress environment
	if ( !$use_cookies ) WFU_USVAR_store_session('wfu_browser_downloadfile_notexist', ( isset($_POST['browser']) ? WFU_BROWSER_DOWNLOADFILE_NOTEXIST : 'File does not exist!' ));
	else array_push($cookies, '{name: "wfu_browser_downloadfile_notexist", value: "'.( isset($_POST['browser']) ? WFU_BROWSER_DOWNLOADFILE_NOTEXIST : 'File does not exist!' ).'", expires: 30}');
	if ( !$use_cookies ) WFU_USVAR_store_session('wfu_browser_downloadfile_failed', ( isset($_POST['browser']) ? WFU_BROWSER_DOWNLOADFILE_FAILED : 'Could not download file!' ));
	else array_push($cookies, '{name: "wfu_browser_downloadfile_failed", value: "'.( isset($_POST['browser']) ? WFU_BROWSER_DOWNLOADFILE_FAILED : 'Could not download file!' ).'", expires: 30}');

	//this routine returns a dynamically created iframe element, that will call
	//the actual download script; the actual download script runs outside
	//Wordpress environment in order to ensure that no php warnings or echo from
	//other plugins is generated, that could scramble the downloaded file; a
	//ticket, similar to nonces, is passed to the download script to check that
	//it is not a CSRF attack; moreover,the ticket is destroyed by the time it
	//is consumed by the download script, so it cannot be used again
	$urlparams = 'file='.$file_code.'&ticket='.$download_id.'&handler='.$wfu_user_state_handler.'&session_legacy='.( WFU_VAR("WFU_US_SESSION_LEGACY") == "true" ? '1' : '0' ).'&dboption_base='.WFU_VAR("WFU_US_DBOPTION_BASE").'&dboption_useold='.( WFU_VAR("WFU_US_DBOPTION_USEOLD") == "true" ? '1' : '0' ).'&wfu_cookie='.WPFILEUPLOAD_COOKIE;
	$response["html"] = '<iframe src="'.WFU_DOWNLOADER_URL.'?'.$urlparams.'" style="display: none;"></iframe>';
	//if user state handler is set to dboption (cookies), then tickets and other
	//variables must pass to the download script as cookies; the cookies are
	//passed in the response of this function, so that the client browser can
	//add them in cookies by executing wfu_add_cookies() function
	$response["js"] = ( count($cookies) > 0 ? 'wfu_add_cookies(['.implode(", ", $cookies).']);' : '' );
	$response = wfu_encode_array_to_string($response);

	/** This filter is documented above. */
	die(apply_filters('_wfu_ajax_action_download_file_invoker', 'wfu_ajax_action_download_file_invoker:wfu_download_id;'.$download_id.':'.$response));
}

/**
 * Monitor a File Download
 *
 * This function monitors a file download and performs post-download actions in
 * case the download has ended.
 *
 * @since 2.6.0
 */
function wfu_ajax_action_download_file_monitor() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$id = (isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
	if ( $file_code == '' || $id == '' ) die();
	$id = wfu_sanitize_code($id);
	
	//ensure that this is not a CSRF attack by checking validity of a security
	//ticket
	if ( !WFU_USVAR_exists('wfu_download_monitor_ticket_'.$id) || time() > WFU_USVAR('wfu_download_monitor_ticket_'.$id) ) {
		WFU_USVAR_unset('wfu_download_monitor_ticket_'.$id);
		WFU_USVAR_unset('wfu_download_status_'.$id);
		die();
	}
	//destroy monitor ticket so it cannot be used again
	WFU_USVAR_unset('wfu_download_monitor_ticket_'.$id);
	
	//initiate loop of 30secs to check the download status of the file;
	//the download status is controlled by the actual download script;
	//if the file finishes within the 30secs of the loop, then this routine logs
	//the action and notifies the client side about the download status of the
	//file, otherwise an instruction to the client side to repeat this routine
	//and wait for another 30secs is dispatched
	$end_time = time() + 30;
	$upload_ended = false;
	while ( time() < $end_time ) {
		$upload_ended = ( WFU_USVAR_exists('wfu_download_status_'.$id) ? ( WFU_USVAR('wfu_download_status_'.$id) == 'downloaded' || WFU_USVAR('wfu_download_status_'.$id) == 'failed' ? true : false ) : false );
		if ( $upload_ended ) break;
		usleep(100);
	}
	
	if ( $upload_ended ) {
		$upload_result = WFU_USVAR('wfu_download_status_'.$id);
		WFU_USVAR_unset('wfu_download_status_'.$id);
		$user = wp_get_current_user();
//		$filepath = wfu_plugin_decode_string($file_code);
		$filepath = wfu_get_filepath_from_safe($file_code);
		if ( $filepath === false ) die();
		$filepath = wfu_path_rel2abs(wfu_flatten_path($filepath));
		wfu_log_action('download', $filepath, $user->ID, '', 0, 0, '', null);
		/**
		 * Customise Output of Download Monitoring Operation.
		 *
		 * This filter allows scripts to customise return of this function after
		 * finish of it.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_download_file_monitor', 'wfu_ajax_action_download_file_monitor:'.$upload_result.':'));
	}
	else {
		//regenerate monitor ticket
		WFU_USVAR_store('wfu_download_monitor_ticket_'.$id, time() + 30);
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_download_file_monitor', 'wfu_ajax_action_download_file_monitor:repeat:'.$id));
	}
}

/**
 * Get View Log Page
 *
 * This function returns the HTML code of a specific page of View Log feature of
 * the plugin in Dashboard.
 *
 * @since 3.5.0
 */
function wfu_ajax_action_get_historylog_page() {
	if ( !isset($_POST['token']) || !isset($_POST['page']) ) die();
	check_ajax_referer( 'wfu-historylog-page', 'token' );
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS") <= 0 ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	$page = wfu_sanitize_int($_POST['page']);
	$rows = wfu_view_log($page, true);
	
	/**
	 * Customise Output of View Log Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_historylog_page', 'wfu_historylog_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Get Uploaded Files Page
 *
 * This function returns the HTML code of a specific page of Uploaded Files area
 * of the plugin in Dashboard.
 *
 * @since 4.7.0
 */
function wfu_ajax_action_get_uploadedfiles_page() {
	if ( !isset($_POST['token']) || !isset($_POST['page']) ) die();
	check_ajax_referer( 'wfu-uploadedfiles-page', 'token' );
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( WFU_VAR("WFU_UPLOADEDFILES_TABLE_MAXROWS") <= 0 ) die();
	
	$_POST = stripslashes_deep($_POST);
	
	$page = wfu_sanitize_int($_POST['page']);
	$rows = wfu_uploadedfiles_manager($page, true);
	
	/**
	 * Customise Output of Uploaded Files Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 4.7.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_uploadedfiles_page', 'wfu_uploadedfiles_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Get File Browser Page
 *
 * This function returns the HTML code of a specific page of File Browser
 * feature of the plugin in Dashboard.
 *
 * @since 4.6.1
 */
function wfu_ajax_action_get_adminbrowser_page() {
	if ( !isset($_POST['code']) || !isset($_POST['token']) || !isset($_POST['page']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-adminbrowser-page', 'token' );
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( WFU_VAR("WFU_ADMINBROWSER_TABLE_MAXROWS") <= 0 ) die();
	
	$code = wfu_sanitize_code($_POST['code']);
	$page = wfu_sanitize_int($_POST['page']);
	//get list of files
	$rows = wfu_browse_files($code, $page, true);
	
	/**
	 * Customise Output of File Browser Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 4.6.1
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_adminbrowser_page', 'wfu_adminbrowser_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Include a File in Plugin Database
 *
 * This function includes a file in the plugin database.
 *
 * @since 3.8.2
 */
function wfu_ajax_action_include_file() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	if ( $file_code == '' || $nonce == '' ) die();

	if ( !current_user_can( 'manage_options' ) ) die();
	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_include_file') ) die();
	
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	if ( $plugin_options['includeotherfiles'] != "1" ) die();
	
	$dec_file = wfu_get_filepath_from_safe($file_code);
	if ( $dec_file === false ) die();

	$user = wp_get_current_user();
	$dec_file = wfu_path_rel2abs(wfu_flatten_path($dec_file));
	$fileid = wfu_log_action('include', $dec_file, $user->ID, '', '', get_current_blog_id(), '', null);
	
	if ( $fileid !== false ) {
		//generate thumbnails of the file, if this feature is activated
		if ( $plugin_options["createthumbnails"] == "1" ) wfu_update_file_thumbnails($fileid);
		/**
		 * Customise Output of File Inclusion Operation.
		 *
		 * This filter allows scripts to customise return of this function after
		 * finish of it.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_include_file', "wfu_include_file:success:".$fileid));
	}
	/** This filter is documented above. */
	else die(apply_filters('_wfu_ajax_action_include_file', 'wfu_include_file:fail:'));
}

/**
 * Reload Front-End File Browser
 *
 * This function returns the HTML code of the front-end file browser, after a
 * request for reloading of it.
 *
 * @since 3.8.5
 */
function wfu_ajax_action_browser_reload() {
	if ( !isset($_POST['nonce']) || !isset($_POST['browser']) || !isset($_POST['page']) || !isset($_POST['sort']) || !isset($_POST['filters']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($_POST['nonce'], 'wfu_download_file_invoker') ) die();
	
	$browser_code = wfu_sanitize_code($_POST['browser']);
	//get params for this browser
	$params = wfu_get_browser_params_from_safe($browser_code);
	if ( $params === false ) die();
	
	$page = preg_replace("/[^0-9]/", "", $_POST['page']);
	$sort = preg_replace("/[^A-Za-z0-9_\-]/", "", $_POST['sort']);
	$filters_str = sanitize_textarea_field($_POST['filters']);
	$filters = ( $filters_str != "" ? json_decode($_POST['filters'], true) : "" );
	if ( $filters == null ) $filters = "";
	
	$echo_str = wordpress_file_upload_render_browser($params, $page, $sort, true, $filters);
	if ( $echo_str != "" ) {
		/**
		 * Customise Output of Front-End File Browser Reload Operation.
		 *
		 * This filter allows scripts to customise return of this function in
		 * case that front-end file browser reload succeeded.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_browser_reload', "wfu_browser_reload_success:".wfu_plugin_encode_string($echo_str)));
	}
	/** This filter is documented above. */
	else die(apply_filters('_wfu_ajax_action_browser_reload', ''));
}

/**
 * Execute Deletion of One or More Files in Front-End File Browser
 *
 * This function executes deletion of one or more files, initiated from a
 * front-end file browser.
 *
 * @since 3.1.0
 */
function wfu_ajax_action_delete_file() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	$browser_code = (isset($_POST['browser']) ? $_POST['browser'] : (isset($_GET['browser']) ? $_GET['browser'] : ''));
	$page = (isset($_POST['page']) ? $_POST['page'] : (isset($_GET['page']) ? $_GET['page'] : ''));
	$sort = (isset($_POST['sort']) ? $_POST['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : ''));
	$filters = (isset($_POST['filters']) ? $_POST['filters'] : (isset($_GET['filters']) ? $_GET['filters'] : ''));
	$remote = (isset($_POST['remote']) ? $_POST['remote'] : (isset($_GET['remote']) ? $_GET['remote'] : ''));
	if ( $file_code == '' || $nonce == '' || $browser_code == '' || $page == '' || $remote == '' ) die();

	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_download_file_invoker') ) die();
	
	//check if this is a removal of remote files
	$remove_remote = ( $remote == '1' );
	
	$browser_code = wfu_sanitize_code($browser_code);
	//get params for this browser
	$params = wfu_get_browser_params_from_safe($browser_code);
	if ( $params === false ) die();
	//check if deletion is enabled for this browser
	if ( !isset($params["candelete"]) || $params["candelete"] != "true" ) die();

	//construct the file_props structure
	$single = true;
	if ( substr($file_code, 0, 5) == "list:" ) {
		$file_code = explode(",", substr($file_code, 5));
		$single = false;
	}
	else $file_code = array( $file_code );
	$file_props = array();
	foreach( $file_code as $code ) {
		$code = wfu_sanitize_code($code);
		if ( !$remove_remote ) {
			$filepath = wfu_get_filepath_from_safe($code);
			if ( $filepath !== false ) {
				$filepath = wfu_flatten_path($filepath);
				array_push($file_props, array( "code" => $code, "path" => $filepath, "rec" => null, "error" => "" ));
			}
		}
		else array_push($file_props, array( "code" => $code, "path" => "", "rec" => null, "error" => "" ));
	}
	if ( count($file_props) == 0 ) die();
	
	//filter file_props based on user view permissions and fill filerec property
	if ( !current_user_can( 'manage_options' ) ) {
		$filerecs = wfu_get_filtered_recs(wfu_prepare_browser_filter($params));
		foreach ( $file_props as $index => $prop ) {
			if ( !$remove_remote ) {
				foreach ( $filerecs as $filerec ) {
					if ( $prop["path"] == $filerec->filepath ) {
						$file_props[$index]["rec"] = $filerec;
						break;
					}
				}
			}
			else {
				foreach ( $filerecs as $filerec ) {
					if ( $prop["code"] == $filerec->idlog ) {
						$file_props[$index]["rec"] = $filerec;
						$file_props[$index]["path"] = $filerec->filepath;
						break;
					}
				}
			}
			if ( $file_props[$index]["rec"] == null ) unset($file_props[$index]);
		}
	}
	else {
		foreach ( $file_props as $index => $prop ) {
			if ( !$remove_remote )
				$file_props[$index]["rec"] = wfu_get_file_rec(wfu_path_rel2abs($prop["path"]), true);
			else {
				$file_props[$index]["rec"] = wfu_get_file_rec_from_id($prop["code"], true);
				$file_props[$index]["path"] = $file_props[$index]["rec"]->filepath;
			}
		}
	}
	if ( count($file_props) == 0 ) die();
	
	//perform custom check and deletion actions and prepare return report
	$report = "";
	$success_count = 0;
	$user = wp_get_current_user();
	foreach ( $file_props as $index => $prop ) {
		$filepath = wfu_path_rel2abs($prop["path"]);
		//apply wfu_browser_check_file_action filter to allow or restrict the
		//deletion of this file
		$changable_data["error_message"] = "";
		$userdata = array();
		foreach ( $prop["rec"]->userdata as $data )
			array_push($userdata, array( "label" => $data->property, "value" => $data->propvalue ));
		$additional_data = array(
			"file_action"	=> "delete",
			"filepath"		=> $filepath,
			"uploaduser"	=> $prop["rec"]->uploaduserid,
			"userdata"		=> $userdata
		);
		/** This filter is documented above. */
		$changable_data = apply_filters("wfu_browser_check_file_action", $changable_data, $additional_data);
		if ( $changable_data["error_message"] != "" )
			$file_props[$index]["error"] = "not_allowed_custom:".$changable_data["error_message"];
		else {
			//check if user is allowed to perform this action on this file
			if ( !wfu_user_owns_file($user->ID, $prop["rec"]) && $params["whodelete"] != "all" )
				$file_props[$index]["error"] = "not_allowed:";
			else {
				//attempt to delete the file
				if ( !wfu_delete_file_execute($filepath, get_current_user_id(), ( $remove_remote ? $prop["rec"] : null )) )
					$file_props[$index]["error"] = "fail:";
				else $success_count++;
			}
		}
		$report .= ( $report == "" ? "" : ";" ).$prop["code"].":".wfu_plugin_encode_string($file_props[$index]["error"]);
	}
	
	//reload browser in case of multiple deletion (provided that at least one
	//file was deleted) or in case reload-on-update attribute is on and the
	//browser is paginated
	$do_reload = ( ( $params["reloadonupdate"] == "true" && $params["pagination"] == "true" && (int)$params["pagerows"] > 0 ) || !$single );
	$browser_html = "";
	if ( $do_reload && $success_count > 0 ) {
		$page = preg_replace("/[^0-9]/", "", $page);
		$sort = preg_replace("/[^A-Za-z0-9_\-]/", "", $sort);
		$filters_str = sanitize_textarea_field($filters);
		$filters = ( $filters_str != "" ? json_decode($filters_str, true) : "" );
		if ( $filters == null ) $filters = "";
		$browser_html = wordpress_file_upload_render_browser($params, $page, $sort, true, $filters);
	}
	
	/**
	 * Customise Output of Front-End File Browser Deletion Operation.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_delete_file', 'wfu_ajax_action_delete_file:'.$report.( $browser_html != "" ? "wfu_browser_html:".wfu_plugin_encode_string($browser_html) : "" )));
}

/**
 * Retrieve User Permissions Page
 *
 * This function returns the HTML code of a user-permissions page when editing
 * back-end browser user permissions in plugin's Settings in Dashboard.
 *
 * @since 3.5.0
 */
function wfu_ajax_action_get_userpermissions_page() {
	if ( !isset($_POST['token']) || !isset($_POST['page']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-userpermissions-page', 'token' );
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS") <= 0 ) die();
	
	$page = wfu_sanitize_int($_POST['page']);
	//get roles
	$ret = wfu_get_rolepermissions_props();
	$role_props = $ret["role_props"];
	//calculate user offset and get users
	$offset = ((int)$page - 1) * (int)WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS");
	$args = array( 'number' => WFU_VAR("WFU_USERPERMISSIONS_TABLE_MAXROWS"), 'offset' => $offset );
	/** This filter is documented in lib/wfu_admin_browser.php */
	$args = apply_filters("_wfu_get_users", $args, "settings_userpermissions_ajax");
	$users = get_users($args);
	//get user rows
	$rows = wfu_get_userpermissions_rows($users, $role_props);
	
	/**
	 * Customise Output of User Permissions Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_userpermissions_page', 'wfu_userpermissions_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Get Back-End Browser Page
 *
 * This function returns the HTML code of a specific page of the back-end
 * browser of the plugin in Dashboard.
 *
 * @since 3.5.0
 */
function wfu_ajax_action_get_filebrowser_page() {
	if ( !isset($_POST['token']) || !isset($_POST['page']) || !isset($_POST['sort']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-filebrowser-page', 'token' );
	// check that user can see the files
	$permissions = wfu_get_current_user_browser_permissions();
	if ( !$permissions['view'] ) die();
	if ( WFU_VAR("WFU_BACKENDBROWSER_TABLE_MAXROWS") <= 0 ) die();
	
	$page = wfu_sanitize_int($_POST['page']);
	$sort = wfu_sanitize($_POST['sort']);
	//get list of files
	$rows = wfu_browse_uploaded_files($sort, $page, true);
	
	/**
	 * Customise Output of Back-End Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_filebrowser_page', 'wfu_filebrowser_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Get Remote Files Page
 *
 * This function returns the HTML code of the remote files page of the plugin in
 * Dashboard.
 *
 * @since 4.16.0
 */
function wfu_ajax_action_get_remotefiles_page() {
	if ( !isset($_POST['token']) || !isset($_POST['page']) || !isset($_POST['sort']) || !isset($_POST['filter']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu-remotefiles-page', 'token' );
	if ( !current_user_can( 'manage_options' ) ) die();
	if ( WFU_VAR("WFU_REMOTEFILES_TABLE_MAXROWS") <= 0 ) die();
	
	$page = wfu_sanitize_int($_POST['page']);
	$sort = wfu_sanitize($_POST['sort']);
	$filter = wfu_sanitize_filter($_POST['filter']);
	//get list of files
	$rows = wfu_manage_remote_files($sort, $page, true, $filter);
	
	/**
	 * Customise Output of Remote Files Page Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 4.16.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_remotefiles_page', 'wfu_remotefiles_page_success:'.wfu_plugin_encode_string($rows)));
}

/**
 * Execute a Function Asynchronously
 *
 * This function executes the function defined in POST parameters. It is part of
 * a mechanism used to execute PHP functions asynchronously.
 *
 * @since 3.5.0
 */
function wfu_ajax_action_wfu_call_async() {
	if ( !isset($_POST['token']) || !isset($_POST['function']) || !isset($_POST['params']) ) die();

	$_POST = stripslashes_deep($_POST);

	$token = wfu_sanitize_code($_POST['token']);
	if ( !wfu_verify_global_short_token($token) ) die();
	
	$function = wfu_sanitize_tag($_POST['function']);
	$params_str = wfu_sanitize_code($_POST['params']);
	$params = array();
	if ( $params_str != "") {
		$params = wfu_decode_array_from_string($params_str);
		if ( is_array($params) ) {
			foreach ( $params as $key => $param )
				$params[$key] = esc_attr($param);
		}
		else $params = array();
	}

	call_user_func_array($function, $params);
	die();
}

/**
 * Execute a Function Asynchronously
 *
 * This function executes the function defined in POST parameters. It is part of
 * a mechanism used to execute PHP functions asynchronously.
 *
 * @since 3.5.0
 */
function wfu_ajax_action_load_hook_code() {
	if ( !isset($_POST['token']) || !isset($_POST['boundary']) || !isset($_POST['key']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	$token = wfu_sanitize_code($_POST['token']);
	if ( !wfu_verify_global_short_token($token) ) die();
	
	$boundary = wfu_sanitize_code($_POST['boundary']);
	$key = wfu_sanitize_code($_POST['key']);
	
	$plugin_hooks = get_option( "wordpress_file_upload_hooks" );
	if ( !is_array($plugin_hooks) ) $plugin_hooks = array();
	if ( !array_key_exists($key, $plugin_hooks) ) die();
	$code = wfu_plugin_decode_string($plugin_hooks[$key]["code"]);
	
	//prepend "return 1;" to the code, so that eval does not execute it but only check the syntax
	$code = "return 1; ".$code;
	//enable reporting and showing all errors
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
	//enclose eval output in boundaries, in order to track it easily
	echo $boundary."0";
	echo eval($code);
	echo $boundary."1";
	die();
}

/**
 * Get Hook Code from a Hook Template
 *
 * This function returns the PHP code of a hook template, together with comments
 * including description and scope.
 *
 * @since 3.6.0
 */
function wfu_ajax_action_get_hook_code_from_template() {
	if ( !isset($_POST['token']) || !isset($_POST['template']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu_edit_hook', 'token' );
	
	$template = wfu_sanitize_tag($_POST['template']);
	
	$templates = wfu_load_hook_templates();
	if ( !isset($templates[$template]) ) die();
	
	$code = $templates[$template]["body"];
	if ( trim($templates[$template]["description"]) != "" )
		$code = "/*\n".$templates[$template]["description"]."*/\n".$code;
	$code = "/*scope:".$templates[$template]["scope"]."*/\n".$code;
	/**
	 * Customise Output of Hook Code Retrieval.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 3.11.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_get_hook_code_from_template', "wfu_get_hook_code_from_template:".wfu_plugin_encode_string($code)));
}

/**
 * Update an Environment Variable
 *
 * This function updates the value of an environment variable of the plugin.
 *
 * @since 3.7.1
 */
function wfu_ajax_action_update_envar() {
	if ( !isset($_POST['token']) || !isset($_POST['slug']) || !isset($_POST['value_enc']) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu_environment_variables', 'token' );
	
	$slug = wfu_sanitize_tag($_POST['slug']);
	$value = esc_attr(wfu_plugin_decode_string($_POST['value_enc']));
	
	$found = false;
	foreach ( $GLOBALS["WFU_GLOBALS"] as $ind => $envar ) {
		if ( $ind == $slug ) {
			$found = true;
			break;
		}
	}
	if ( !$found ) {
		/**
		 * Customise Output of Environment Variable Operation.
		 *
		 * This filter allows scripts to customise return of this function after
		 * finish of it.
		 *
		 * @since 3.11.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_update_envar', 'wfu_update_envar:fail:'));
	}

	$envar = $GLOBALS["WFU_GLOBALS"][$slug];
	$saved = ( $envar[1] == "integer" ? (int)$value : (string)$value );	
	$envars = get_option("wfu_environment_variables", array());
	$GLOBALS["WFU_GLOBALS"][$slug][3] = $saved;
	$envars[$slug] = $saved;
	update_option("wfu_environment_variables", $envars);
	/** This filter is documented above. */
	die(apply_filters('_wfu_ajax_action_update_envar', "wfu_update_envar:success:".wfu_plugin_encode_string($saved)));
}

/**
 * Update an Extension's Active State
 *
 * This function activates or disables an extension.
 *
 * @since 4.17.0
 */
function wfu_ajax_action_update_extension_active() {
	global $WFU_PLUGIN_EXTENSIONS;
	
	if ( !isset($_POST['token']) || !isset($_POST['extension']) || !isset($_POST['active']) ) die();
	if ( $_POST['active'] !== "0" && $_POST['active'] !== "1" ) die();
	
	if ( !isset($WFU_PLUGIN_EXTENSIONS) || !is_array($WFU_PLUGIN_EXTENSIONS) ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu_edit_extensions', 'token' );
	
	$extension = wfu_sanitize_tag($_POST['extension']);
	$active = $_POST['active'];
	
	$extensions = apply_filters("_wfu_plugin_extensions", array());
	$found = false;
	foreach ( $extensions as $extension_info ) {
		if ( $extension_info["code"] == $extension ) {
			$found = true;
			break;
		}
	}
	if ( !$found ) die();
	
	$WFU_PLUGIN_EXTENSIONS[$extension] = $active;
	update_option( "wordpress_file_upload_extensions", $WFU_PLUGIN_EXTENSIONS );
	$WFU_PLUGIN_EXTENSIONS = get_option( "wordpress_file_upload_extensions", array() );
	$active = ( !array_key_exists($extension, $WFU_PLUGIN_EXTENSIONS) || $WFU_PLUGIN_EXTENSIONS[$extension] !== "0" ? "1" : "0" );

	/**
	 * Customise Output of Change Extension's State Operation.
	 *
	 * This filter allows scripts to customise return of this function after
	 * finish of it.
	 *
	 * @since 4.17.0
	 *
	 * @param string $echo_str The return of the function.
	 */
	die(apply_filters('_wfu_ajax_action_update_extension_active', "wfu_update_extension_active:success:".$active));
}

/**
 * Execute a File Transfer Command
 *
 * This function executes a command on a file that is being transferred in a
 * cloud service and is shown in File Transfers page of the plugin in Dashboard.
 *
 * @since 4.0.0
 */
function wfu_ajax_action_transfer_command() {
	global $wpdb;
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";

	if ( !isset($_POST['code']) || !isset($_POST['nonce']) ) die();
	if ( !WFU_USVAR_exists("wfu_transfers_data") ) die();

	$_POST = stripslashes_deep($_POST);
	
	check_ajax_referer( 'wfu_transfers_nonce', 'nonce' );
	
	$code = wfu_sanitize_code($_POST['code']);
	//if code is refresh then this is a simple refresh of transfers page
	if ( $code == "refresh" ) {
		/**
		 * Customise Output of File Transfer Command Operation.
		 *
		 * This filter allows scripts to customise return of this function after
		 * finish of it.
		 *
		 * @since 4.0.0
		 *
		 * @param string $echo_str The return of the function.
		 */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:success:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	//else continue with transfer command
	if ( strlen($code) != 8 ) die();
	
	//find the transfer id and command corresponding to the code
	$transfer_id = '';
	$transfer_command = '';
	$transfers_data = WFU_USVAR("wfu_transfers_data");
	foreach ( $transfers_data as $id => $data ) {
		foreach ( $data as $command => $command_code ) {
			if ( $command != "status" && $command_code == $code ) {
				$transfer_id = $id;
				$transfer_command = $command;
				break;
			}
		}
		if ( $transfer_id != '' ) break;
	}
	
	//check if the transfer is still valid or it is obsolete
	if ( $transfer_id == '' ) {
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	$data = $wpdb->get_row('SELECT * FROM '.$table_name3.' WHERE iddbxqueue = '.$transfer_id);
	if ( $data == null ) {
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	$filedata = wfu_get_latest_filedata_from_id($data->fileid);
	//$filedata may contain data for file transfers to many service
	//accounts, so we need to get the one corresponding to this rec
	$service = wfu_get_service_from_filedata($filedata, $data->iddbxqueue);
	if ( $service == "" || !isset($filedata[$service]) ) {
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	$filepath = wfu_path_rel2abs($filedata[$service]["filepath"]);
	if ( !wfu_file_exists($filepath, "wfu_ajax_action_transfer_command") ) {
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	if ( $data->status != $transfers_data[$transfer_id]["status"] ) {
		/** This filter is documented above. */
		die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
	}
	
	//proceed to check and execution of the specific command
	if ( $transfer_command == "restart" ) {
		$additional_params = array();
		if ( isset($filedata[$service]["additional_params"]) ) $additional_params = $filedata[$service]["additional_params"];
		wfu_add_file_to_transfer_queue($data->fileid, $filepath, $service, $filedata[$service]["destination"], ( $filedata[$service]["deletelocal"] == 1 ), 'last', $additional_params, false);
	}
	elseif ( $transfer_command == "remove" ) {
		$wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$transfer_id);
	}
	elseif ( $transfer_command == "up" ) {
		$id2 = $wpdb->get_var("SELECT iddbxqueue FROM $table_name3 
			WHERE status = $data->status AND priority < (SELECT priority FROM $table_name3 WHERE iddbxqueue = $data->iddbxqueue) 
			ORDER BY priority DESC LIMIT 1");
		if ( $id2 === null ) {
			/** This filter is documented above. */
			die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
		}
		$res = $wpdb->query("UPDATE $table_name3 AS table1 JOIN $table_name3 AS table2 
			ON ( table1.iddbxqueue = $data->iddbxqueue AND table2.iddbxqueue = $id2 ) 
			SET table1.priority = table2.priority, table2.priority = table1.priority;");
		if ( $res === false ) {
			/** This filter is documented above. */
			die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
		}
	}
	elseif ( $transfer_command == "down" ) {
		$id2 = $wpdb->get_var("SELECT iddbxqueue FROM $table_name3 
			WHERE status = $data->status AND priority > (SELECT priority FROM $table_name3 WHERE iddbxqueue = $data->iddbxqueue) 
			ORDER BY priority ASC LIMIT 1");
		if ( $id2 === null ) {
			/** This filter is documented above. */
			die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
		}
		$res = $wpdb->query("UPDATE $table_name3 AS table1 JOIN $table_name3 AS table2 
			ON ( table1.iddbxqueue = $data->iddbxqueue AND table2.iddbxqueue = $id2 ) 
			SET table1.priority = table2.priority, table2.priority = table1.priority;");
		if ( $res === false ) {
			/** This filter is documented above. */
			die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:obsolete:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
		}
	}
	else die();
	
	/** This filter is documented above. */
	die(apply_filters('_wfu_ajax_action_transfer_command', "wfu_transfer_command:success:".wfu_plugin_encode_string(wfu_manage_file_transfers(true))));
}

/**
 * Update WPFilebase Manager Plugin
 *
 * This function instructs WPFilebase Manager plugin to synchronize its list of
 * files, after a file upload.
 *
 * @since 2.4.1
 */
function wfu_ajax_action_notify_wpfilebase() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$params_index = (isset($_POST['params_index']) ? $_POST['params_index'] : (isset($_GET['params_index']) ? $_GET['params_index'] : ''));
	$session_token = (isset($_POST['session_token']) ? $_POST['session_token'] : (isset($_GET['session_token']) ? $_GET['session_token'] : ''));
	if ( $params_index == '' || $session_token == '' ) die();

	$params_index = sanitize_text_field($params_index);
	$session_token = sanitize_text_field($session_token);

	$arr = wfu_get_params_fields_from_index($params_index, $session_token);
	//check referer using server sessions to avoid CSRF attacks
	if ( WFU_USVAR("wfu_token_".$arr['shortcode_id']) != $session_token ) die();

	//execute WPFilebase plugin sunchronization by calling 'wpfilebase_sync'
	//action
	do_action('wpfilebase_sync');

	die();
}

/**
 * Get List of Users
 *
 * This function returns a list of users meeting specific criteria. Only the
 * first 100 users will be returned, for avoiding performance issues.
 *
 * @since 4.5.0
 */
function wfu_ajax_action_pdusers_get_users() {
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);

	$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_GET['nonce']) ? $_GET['nonce'] : ''));
	$query = (isset($_POST['query']) ? $_POST['query'] : (isset($_GET['query']) ? $_GET['query'] : ''));
	if ( $nonce == '' || $query == '' ) die();

	if ( !current_user_can( 'manage_options' ) ) die();
	//security check to avoid CSRF attacks
	if ( !wp_verify_nonce($nonce, 'wfu_edit_policy') ) die();

	$query = sanitize_text_field($query);
	$args = array(
		'search'         => $query,
		'search_columns' => array( 'user_login', 'display_name' ),
		'fields'		 => array( 'user_login', 'display_name' ),
		'number'		 => 100
	);
	/** This filter is documented in lib/wfu_admin_browser.php */
	$args = apply_filters("_wfu_get_users", $args, "manage_pdusers");
	$users = get_users($args);
	
	die("pdusers_get_users:".wfu_encode_array_to_string($users));
}