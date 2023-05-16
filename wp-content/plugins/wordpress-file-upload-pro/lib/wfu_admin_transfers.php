<?php

/**
 * File Transfers Page in Dashboard Area of Plugin
 *
 * This file contains functions related to File Transfers page of plugin's
 * Dashboard area.
 *
 * @link /lib/wfu_admin_transfers.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 4.0.0
 */

/**
 * Display the File Transfers Page.
 *
 * This function displays the File Transfers page of the plugin's Dashboard
 * area.
 *
 * @since 4.0.0
 *
 * @redeclarable
 *
 * @param bool $only_table_rows Optional. Return only the HTML code of the table
 *        rows.
 *
 * @return string The HTML output of the plugin's File Transfers Dashboard page.
 */
function wfu_manage_file_transfers($only_table_rows = false) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	global $wpdb;
	
	wfu_schedule_transfermanager(true);
	
	$siteurl = site_url();
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

//	$transfers = $wpdb->query('DROP TABLE '.$table_name3);
//	return;
	
	if ( !current_user_can( 'manage_options' ) ) return;
	//get data from database
	$transfers = $wpdb->get_results('SELECT * FROM '.$table_name3.' ORDER BY priority ASC');
	//move failed records to the end of the array
	$failed = array();
	foreach ( $transfers as $ind => $transfer ) {
		if ( $transfer->status == -2 ) {
			array_push($failed, $transfer);
			unset($transfers[$ind]);
		}
	}
	$transfers = array_merge($transfers, $failed);
	
	$echo_str = "";
	
	if ( !$only_table_rows ) {
		$echo_str .= "\n".'<div class="wrap">';
		$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
		$echo_str .= "\n\t".'<div style="margin-top:20px;">';
		$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "Transfers");
		$echo_str .= "\n\t".'<h3 style="margin-bottom: 10px;">Pending File Transfers</h3>';
		$echo_str .= "\n\t".'<div style="position:relative;">';
		$echo_str .= wfu_add_loading_overlay("\n\t\t", "filetransfers");
		$echo_str .= "\n\t\t".'<input type="hidden" id="wfu_transfers_nonce" value="'.wp_create_nonce('wfu_transfers_nonce').'" />';
		$echo_str .= "\n\t\t".'<div class="wfu_filetransfers_header">';
		$echo_str .= "\n\t\t\t".'<label>Autorefresh list</label>';
		$echo_str .= "\n\t\t\t".'<a id="wfu_transfers_switch" onclick="javascript: wfu_transfers_switch(); return false;" class="button wfu_transfers_switch wfu_pause" title="pause auto-refresh"><img class="wfu_pause" src="'.WFU_IMAGE_TRANSFER_PAUSE.'" /><img class="wfu_refresh" src="'.WFU_IMAGE_TRANSFER_REFRESH.'" /></a>';
		$echo_str .= "\n\t\t\t".'<div class="wfu_transfers_timer" title="">';
		$echo_str .= "\n\t\t\t\t".'<input type="hidden" id="wfu_transfers_interval" value="'.WFU_VAR("WFU_FILETRANSFERS_REFRESH_INTERVAL").'" />';
		$echo_str .= "\n\t\t\t\t".'<div id="wfu_transfers_timer_inner" class="wfu_transfers_timer_inner" style="width: 0%;"></div>';
		$echo_str .= "\n\t\t\t".'</div>';
		$echo_str .= "\n\t\t\t".'<img id="wfu_transfers_refreshing" class="wfu_transfers_refreshing_disabled" src="'.WFU_IMAGE_OVERLAY_LOADING.'" />';
		$echo_str .= "\n\t\t".'</div>';
		$echo_str .= "\n\t\t".'<table id="wfu_filetransfer_table" class="wp-list-table widefat fixed striped">';
		$echo_str .= "\n\t\t\t".'<thead>';
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="5%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>#</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" class="manage-column column-primary">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>File</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Service</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="20%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Destination</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="20%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Status</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Actions</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$echo_str .= "\n\t\t\t".'</thead>';
		$echo_str .= "\n\t\t\t".'<tbody>';
	}

	$i = 1;
	$max_uploads = (int)WFU_VAR("WFU_TRANSFERMANAGER_MAX_JOBS");
	$current_uploads = 0;
	$group_index = 1;
	$status_of_previous = 2;
	//reset session data
	WFU_USVAR_store("wfu_transfers_data", array());
	foreach ( $transfers as $transfer ) {
		$filedata = wfu_get_latest_filedata_from_id($transfer->fileid);
		//$filedata may contain data for file transfers to many service
		//accounts, so we need to get the one corresponding to this rec
		$service = wfu_get_service_from_filedata($filedata, $transfer->iddbxqueue);
		if ( $service != "" && isset($filedata[$service]) ) {
			$filepath = wfu_path_rel2abs($filedata[$service]["filepath"]);
			if ( wfu_file_exists($filepath, "wfu_manage_file_transfers") ) {
				//calculate retries of file and relevant text
				$retries = $filedata[$service]["total_retries"];
				$attempts = "";
				switch ($retries) {
					case 0: break;
					case 1: break;
					case 2: $attempts = " (2nd attempt)"; break;
					case 3: $attempts = " (3rd attempt)"; break;
					default: $attempts = " (".$retries."th attempt)"; break;
				}
				//calculate status of transfer commands
				$s = $transfer->status;
				$sclass = ( $s == 1 || $s == 99 ? "uploading" : ( $s == 0 ? "pending" : "failed" ) );
				$group_index = ( $s == $status_of_previous ? $group_index + 1 : 1 );
				if ( $s == 1 || $s == 99 ) $current_uploads ++;
				$transfer_buttons = array ( 
					"restart"	=> ( $s == 1 || $s == 99 || $s == -2 ),
					"remove"	=> true,
					"up"		=> ( $s == 0 && $group_index > 1 ),
				);
				//output html of down button of previous file
				$transfer_data = WFU_USVAR("wfu_transfers_data");
				if ( $i > 1 ) {
					if ( $transfer_buttons["up"] ) $transfer_data[$id]["down"] = wfu_create_random_string(8);
					$echo_str .= "\n\t\t\t\t\t\t".'<a onclick="'.( $transfer_buttons["up"] ? 'javascript:wfu_transfer_command('.$s.', \'down\', \''.$transfer_data[$id]["down"].'\'); return false;' : 'javascript: return false;' ).'" class="button wfu_transfers_button'.( $transfer_buttons["up"] ? '' : '_disabled' ).'" title="move down"'.( $transfer_buttons["up"] ? '' : ' disabled="disabled"' ).'><img src="'.WFU_IMAGE_TRANSFER_DOWN.'" /></a>';
					$echo_str .= "\n\t\t\t\t\t".'</td>';
					$echo_str .= "\n\t\t\t\t".'</tr>';
				}
				//save to session
				$id = $transfer->iddbxqueue;
				$transfer_data[$id]["status"] = $s;
				if ( $transfer_buttons["restart"] ) $transfer_data[$id]["restart"] = wfu_create_random_string(8);
				if ( $transfer_buttons["remove"] ) $transfer_data[$id]["remove"] = wfu_create_random_string(8);
				if ( $transfer_buttons["up"] ) $transfer_data[$id]["up"] = wfu_create_random_string(8);
				WFU_USVAR_store("wfu_transfers_data", $transfer_data);
				//output html of current file (except down button)
				$echo_str .= "\n\t\t\t\t".'<tr>';
				$echo_str .= "\n\t\t\t\t\t".'<th class="wfu_transfer_row_'.$sclass.'" style="word-wrap: break-word;">';
				$echo_str .= "\n\t\t\t\t\t\t".'<label>'.$i.'</label>';
				$echo_str .= "\n\t\t\t\t\t".'</th>';
				$echo_str .= "\n\t\t\t\t\t".'<td class="column-primary wfu_transfer_row_'.$sclass.'" data-colname="File">';
				$echo_str .= "\n\t\t\t\t\t\t".'<label>'.wfu_basename($filepath).'</label>';
				$echo_str .= "\n\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
				$echo_str .= "\n\t\t\t\t\t".'</td>';
				$echo_str .= "\n\t\t\t\t\t".'<td class="wfu_transfer_row_'.$sclass.'" data-colname="Service">';
				$echo_str .= "\n\t\t\t\t\t\t".'<label>'.$service.'</label>';
				$echo_str .= "\n\t\t\t\t\t".'</td>';
				$echo_str .= "\n\t\t\t\t\t".'<td class="wfu_transfer_row_'.$sclass.'" data-colname="Destination">';
				$echo_str .= "\n\t\t\t\t\t\t".'<label>'.$filedata[$service]["destination"].'</label>';
				$echo_str .= "\n\t\t\t\t\t".'</td>';
				$echo_str .= "\n\t\t\t\t\t".'<td class="wfu_transfer_row_'.$sclass.'" data-colname="Status">';
				$echo_str .= "\n\t\t\t\t\t\t".'<label class="wfu_transfer_status_label" title="'.( $transfer->status == 1 || $transfer->status == 99 ? 'uploading'.$attempts.'"><img src="'.WFU_IMAGE_TRANSFER_UPLOADING.'" /> for '.wfu_human_time(time() - (int)$filedata[$service]["start_time"]).( isset($filedata[$service]["upload_data"]) && $filedata[$service]["upload_data"]["progress"] > -1 ? " (".$filedata[$service]["upload_data"]["progress"]."%)" : "" ) : ( $transfer->status == -2 ? 'transfer failed"><img src="'.WFU_IMAGE_TRANSFER_FAILED.'" />' : 'pending"><img src="'.WFU_IMAGE_TRANSFER_WAITING.'" />' ) ).'</label>';
				$echo_str .= "\n\t\t\t\t\t".'</td>';
				$echo_str .= "\n\t\t\t\t\t".'<td class="wfu_transfer_row_'.$sclass.'" data-colname="Actions">';
				$echo_str .= "\n\t\t\t\t\t\t".'<a onclick="'.( $transfer_buttons["restart"] ? 'javascript:wfu_transfer_command('.$s.', \'restart\', \''.$transfer_data[$id]["restart"].'\'); return false;' : 'javascript: return false;' ).'" class="button wfu_transfers_button'.( $transfer_buttons["restart"] ? '' : '_disabled' ).'" title="restart upload"'.( $transfer_buttons["restart"] ? '' : ' disabled="disabled"' ).'><img src="'.WFU_IMAGE_TRANSFER_RESTART.'" /></a>';
				$echo_str .= "\n\t\t\t\t\t\t".'<a onclick="'.( $transfer_buttons["remove"] ? 'javascript:wfu_transfer_command('.$s.', \'remove\', \''.$transfer_data[$id]["remove"].'\'); return false;' : 'javascript: return false;' ).'" class="button wfu_transfers_button'.( $transfer_buttons["remove"] ? '' : '_disabled' ).'" title="remove upload"'.( $transfer_buttons["remove"] ? '' : ' disabled="disabled"' ).'><img src="'.WFU_IMAGE_TRANSFER_REMOVE.'" /></a>';
				$echo_str .= "\n\t\t\t\t\t\t".'<a onclick="'.( $transfer_buttons["up"] ? 'javascript:wfu_transfer_command('.$s.', \'up\', \''.$transfer_data[$id]["up"].'\'); return false;' : 'javascript: return false;' ).'" class="button wfu_transfers_button'.( $transfer_buttons["up"] ? '' : '_disabled' ).'" title="move up"'.( $transfer_buttons["up"] ? '' : ' disabled="disabled"' ).'><img src="'.WFU_IMAGE_TRANSFER_UP.'" /></a>';
				$status_of_previous = $s;
				$i++;
			}
		}
	}
	//output html of down button of last file
	if ( $i > 1 ) {
		$echo_str .= "\n\t\t\t\t\t\t".'<a class="button wfu_transfers_button_disabled" title="move down" disabled="disabled"><img src="'.WFU_IMAGE_TRANSFER_DOWN.'" /></a>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';		
	}
	
	if ( !$only_table_rows ) {
		$echo_str .= "\n\t\t\t".'</tbody>';
		$echo_str .= "\n\t\t".'</table>';
		$echo_str .= "\n\t".'</div>';
		$echo_str .= "\n\t".'</div>';
		$handler = 'function() { wfu_initiate_transfers_timer_observer(); }';
		$echo_str .= "\n\t".'<script type="text/javascript">if(window.addEventListener) { window.addEventListener("load", '.$handler.', false); } else if(window.attachEvent) { window.attachEvent("onload", '.$handler.'); } else { window["onload"] = '.$handler.'; }</script>';
		$echo_str .= "\n".'</div>';
	}

	return $echo_str;
}

/**
 * Add Files to Transfers Queue.
 *
 * This function adds uploaded files to transfers queue and invokes Transfer
 * Manager. Plugin extensions enable the administrator to transfer the uploaded
 * files to a Dropbox or other service account that has been set up through the
 * plugin's settings in Dashboard. Files are transferred to the account
 * asynchronously, which means that the user who uploaded the files does not
 * have to wait until they are transferred. A database table, wfu_dbxqueue,
 * keeps the list of files that need to be transferred to the account. Details
 * about the transfer, such as destination directory in account, are kept in
 * filedata field of the main table, wfu_log.
 *
 * @since 4.1.0
 *
 * @param int $fileid The ID of the file to add to queue.
 * @param string $filepath The full absolute file path of the file.
 * @param string $service The service to be used (Dropbox, Google Drive etc.).
 * @param string $destination Folder in account to store the file.
 * @param bool $deletelocal Delete or keep the local file after the transfer.
 * @param string $position Position of the file in queue, can take values
 *        "first" or "last".
 * @param array $additional_params Optional. An array of additional parameters
 *        to pass in service's transfer functions.
 * @param bool $execute_transfermanager Optional. Execute or not transfermanager
 *        right after adding the file to the queue.
 */
function wfu_add_file_to_transfer_queue($fileid, $filepath, $service, $destination, $deletelocal, $position, $additional_params = array(), $execute_transfermanager = true) {
	wfu_tf_LOG("add_file_to_queue_start:".$fileid);
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	
	$filedata = wfu_get_latest_filedata_from_id($fileid, true);
	//abort if filedata is null, which means that the file does not have a
	//database record
	if ( $filedata === null ) return wfu_tf_LOG("add_file_to_queue_end:no_filedata");
	//get min and max priorities of transfer queue
	$priority_limits = $wpdb->get_row('SELECT min(priority), max(priority) FROM '.$table_name3.' WHERE status >= 0', ARRAY_N);
	$priority_min = ( isset($priority_limits[0]) ? ( $priority_limits[0] != null ? $priority_limits[0] : 1 ) : 1 );
	$priority_max = ( isset($priority_limits[1]) ? ( $priority_limits[1] != null ? $priority_limits[1] : 0 ) : 0 );
	//calculate priority of file based on its position
	$priority = ( $position == "first" ? $priority = $priority_min - 1 : $priority = $priority_max + 1 );
	//add file to queue
	$id = -1;
	if ( $wpdb->insert($table_name3, array(
			'fileid' 		=> $fileid,
			'priority' 		=> $priority,
			'status' 		=> 0,
			'jobid' 		=> ""
		), array( '%d', '%d', '%d', '%s' )) !== false ) {
		$id = $wpdb->insert_id;
		//update filedata field of file in database
		$filedata[$service] = array(
			'type' 				=> "transfer",
			'id' 				=> $id,
			'status' 			=> "pending",
			'retries' 			=> 0,
			'total_retries'		=> 0,
			'start_time' 		=> -1,
			'end_time' 			=> -1,
			'transfer_time'		=> 0,
			'total_time'		=> 0,
			'filepath' 			=> wfu_path_abs2rel($filepath),
			'destination' 		=> $destination,
			'deletelocal' 		=> ( $deletelocal ? 1 : 0 ),
			'additional_params'	=> $additional_params
		);
		wfu_save_filedata_from_id($fileid, $filedata);
		if ( $execute_transfermanager ) wfu_schedule_transfermanager(true);
		wfu_tf_LOG("add_file_to_queue_end");
	}
	else wfu_tf_LOG("add_file_to_queue_end:insert_failed");
}

/**
 * Schedule Transfer Manager.
 *
 * This function will first check if the transfer manager is running by
 * checking wfu_transfermanager_props option. If it is not running, then it
 * invokes the transfermanager immediately, otherwise it stores a flag to re-run
 * it right after it finishes. This way, only one instance of the
 * transfermanager is running at all times.
 *
 * It is noted that in order to read wfu_transfermanager_props option we do not
 * use the classic get_option function of Wordpress, because this function uses
 * caching and it does not work well when parallel PHP scripts are running.
 * Instead, we use wfu_get_option which reads straight from the database.
 * 
 * We also do not use update_option to update the option value, because it also
 * does not work well with parallel PHP scripts. This is because update_option
 * uses two database queries, one to read existing value and another one to 
 * update it, however it is likely that between these two queries another one 
 * from another PHP script may interfere and cause erroneous value readings.
 * Instead, in order to avoid this problem, we use wfu_update_option, which
 * uses only one MYSQL query to update the value. The query is of the form:
 *  
 *  INSERT INTO table (field1, field2, ...) VALUES (val1, val2, ...)
 *    ON DUPLICATE KEY UPDATE field2=VALUES(field2), field3=VALUES(field3), ...
 *
 * @since 4.1.0
 *
 * @param bool $immediately Optional. True if Transfer Mnaager must run
 *        immediately, false otherwise.
 */
function wfu_schedule_transfermanager($immediately = false) {
	wfu_tf_LOG("schedule_transfermanager_start");
	//check if any extension service account is activated
	if ( !wfu_filetransfer_any_service_active() ) return wfu_tf_LOG("schedule_transfermanager_end:no_service_active");
	
	$props = wfu_get_transfermanager_props();
	//check if transfer manager takes too long to finish; if the limit is
	//exceeded then its status will be set to 0, so that it can run again
	if ( $props["status"] == 1 ) {
		$max_runtime = (int)WFU_VAR("WFU_TRANSFERMANAGER_MAX_RUNTIME");
		if ( $max_runtime > -1 && time() - $props["start_time"] > $max_runtime ) {
			wfu_update_option( 'wfu_transfermanager_props', array( "status" => 0, "nextrun" => 0, "start_time" => -1, "end_time" => -1, "jobs" => $props["jobs"] ));
			$props = wfu_get_transfermanager_props();
		}	
	}
	//in case that there are pending jobs from last time then we want to execute
	//transfermanager again immediately, regardless of $immediately flag, so we
	//update this flag accordingly
	$immediately = ( $immediately || $props["jobs"] > 0 );
	if ( $immediately ) {
		//if immediately flag is enabled and transfermanager is not running,
		//then run it immediately
		if ( $props["status"] == 0 ) wfu_execute_transfermanager();
		//if it is running, then mark it to run again immediately after it
		//finishes
		else wfu_update_option( 'wfu_transfermanager_props', array( "status" => 1, "nextrun" => 1, "start_time" => $props["start_time"], "end_time" => $props["end_time"], "jobs" => $props["jobs"] ));
	}
	else {
		//if immediately flag is not enabled and at least File Transfer Jobs
		//Recheck Interval has passed after the last execution of
		//transfermanager, then execute it again
		$interval_limit = time() - (int)WFU_VAR("WFU_TRANSFERMANAGER_CHECKJOBS_INTERVAL");
		if ( $props["status"] == 0 && $props["end_time"] < $interval_limit ) wfu_execute_transfermanager();
	}
	wfu_tf_LOG("schedule_transfermanager_end");
}

/**
 * Execute Transfer Manager.
 *
 * This function executes the Transfer Manager. If an instance of it is already
 * running, then it exits.
 * 
 * The Transfer Manager first checks if there are any files that are being 
 * transferred to service accounts (their status is 1) but it has passed a lot
 * of time since their beginning. If the time passed exceeds the maxretry
 * limit, then it is assumed that the file fails to upload repeatedly for some
 * reason, so it is aborted and is marked as 'overtime'. If the time passed
 * exceeds the max wait upload time, then it is assumed that the script that
 * transferred it to the service account failed without notification, so the
 * file status is set to 0, so that the file is transferred again.
 * 
 * If 'ModSecurity Restrictions' option is disabled, then the transfer command
 * is executed asynchronously. However some times the server fails to perform
 * the asynchrounous call and the file stays in status of 1 (uploading status)
 * forever. For this reason, an additional check has been added that first puts
 * the file in status 99. In this status the plugin expects that the transfer
 * command for this file will have to be executed within the next seconds. If
 * the transfer command is not executed, then it is assumed that the server
 * failed to perform the asynchronous call. In this case the file transfer is
 * reset so that the plugin retries the asynchronous call.
 * 
 * Afterwards the transfermanager checks if the maximum number of concurrent
 * transfers has been reached. If not, then it initiates service transfers of
 * pending files, until the maximum number of concurrent transfers is reached
 * or there are no other pending files.
 * 
 * The following statuses are supported:
 *    1 (uploading): the file is being transferred to service account
 *    0 (pending): the file is waiting in queue to be transferred
 *   -1 (failed): the file failed to be transferred once
 *   -2 (failed_permanently): the file failed to be transferred permanently
 *   99 (starting): the file is waiting for transfer function to be executed
 * 
 * Finally, it checks if it needs to re-run again and proceeds accordingly.
 *
 * @since 4.1.0
 */
function wfu_execute_transfermanager() {
	wfu_tf_LOG("execute_transfermanager_start");
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));
	
	//check if any extension service account is activated
	if ( !wfu_filetransfer_any_service_active() ) return wfu_tf_LOG("execute_transfermanager_end:no_service_active");
	
	$props = wfu_get_transfermanager_props();
	//if the transfermanager is already running, then exit
	if ( $props["status"] == 1 ) return wfu_tf_LOG("execute_transfermanager_end:already_running");
	//update the status of the transfermanager
	wfu_update_option( 'wfu_transfermanager_props', array( "status" => 1, "nextrun" => 0, "start_time" => time(), "end_time" => -1, "jobs" => $props["jobs"] ));
	
	//begin initial check of all records for validity
	$recs = $wpdb->get_results('SELECT * FROM '.$table_name3.' ORDER BY priority ASC');
	if ( !is_array($recs) ) $recs = array();
	//first check and update fileids to their latest record
	foreach( $recs as $ind => $rec ) {
		$filerec = wfu_get_latest_rec_from_id($rec->fileid);
		//if filerec is null then the record does not exist or it is obsolete
		//and must be removed from queue
		if ( $filerec == null ) {
			$wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$rec->iddbxqueue);
			//remove current record from the array
			unset($recs[$ind]);
		}
		//if the latest idlog is different than fileid, then update the queue
		//record and $rec
		elseif ( $filerec->idlog != $rec->fileid ) {
			$wpdb->update($table_name3,
				array( 'fileid' => $filerec->idlog ),
				array( 'iddbxqueue' => $rec->iddbxqueue ),
				array( '%d' ), array( '%d' )
			);
			$recs[$ind]->fileid = $filerec->idlog;
		}
	}
	//then check validity of filedata and the file itself
	foreach( $recs as $ind => $rec ) {
		$filedata = wfu_get_latest_filedata_from_id($rec->fileid);
		//$filedata may contain data for file transfers to many service
		//accounts, so we need to get the one corresponding to this rec
		$service = wfu_get_service_from_filedata($filedata, $rec->iddbxqueue);
		//if $service is empty (meaning that filedata is null or the id does not
		//correspond to the unique index of the db record), or the file does not
		//exist then this means that the record is not valid and must be removed
		//from queue
		if ( $service == "" || !wfu_file_exists(wfu_path_rel2abs($filedata[$service]["filepath"]), "wfu_execute_transfermanager") ) {
			$wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$rec->iddbxqueue);
			//remove current record from the array
			unset($recs[$ind]);
		}
		//if filedata is valid then add it to rec record for using it later on
		else {
			$recs[$ind]->service = $service;
			$recs[$ind]->filedata = $filedata;
		}
	}
	
	//check files being transferred for completion or for good health and revive
	//any large transfers that seem to delay due to script timeouts
	foreach( $recs as $ind => $rec ) {
		$service = $rec->service;
		if ( $rec->status == 1 ) {
			//if for some reason "upload_data" array does not exist in filedata
			//then mark the file as failed for starting it over again
			if ( !isset($rec->filedata[$service]["upload_data"]) ) {
				$recs[$ind] = wfu_update_transferred_file($rec, -1);
			}
			//process files that have finished uploading
			elseif ( $rec->filedata[$service]["upload_data"]["finished"] === true ) {
				$result = $rec->filedata[$service]["upload_data"]["result"];
				$recs[$ind] = wfu_transfer_file_finish($rec, $result);
				//if the file has finished uploading remove from recs array
				if ( $result["result"] == true ) unset($recs[$ind]);
			}
			//process files that are still uploading
			else {
				//if service does not exist then mark file as permanantly failed
				if ( !function_exists("wfu_{$service}_service_active") || !function_exists("wfu_{$service}_transfer_file") ) {
					wfu_update_transferred_file($rec, -2);
					//remove current record from the array
					unset($recs[$ind]);
				}
				//if service is not activated them mark the file as pending
				elseif ( !call_user_func("wfu_{$service}_service_active") ) {
					$recs[$ind] = wfu_update_transferred_file($rec, 0, "", $rec->priority);
				}
				//call function of service that checks the uploading file
				else {
					if ( $plugin_options["modsecurity"] != "1" ) {
						wfu_call_async('wfu_check_file_transfer', array($rec->fileid, $rec->jobid, $service));
					}
					else {
						//in case than mod_security module is enabled, then the
						//async call wont work and we need to execute the
						//transfer synchronously
						wfu_check_file_transfer($rec->fileid, $rec->jobid, $service);
					}
				}
			}
		}
	}

	//perform checks on running transfers that have either failed (status is -1)
	//or have exceeded transfer time or async limit:
	// a. max total transfer time has been exceeded -> remove file from queue
	// b. max total retries have been exceeded -> remove file from queue
	// c. upload function was not executed (async limit exceeded) -> reset file
	//    and put it last
	// d. consequtive retries have been exceeded -> reset file and put it last
	// e. reset file
	//make one first pass to find and process any permanently failed files
	//(having a status of -2) and then make a second loop to process the rest
	//failed files
	$overtime_limit = ( (int)WFU_VAR("WFU_TRANSFERMANAGER_TIMEOUT") > -1 ? time() - (int)WFU_VAR("WFU_TRANSFERMANAGER_TIMEOUT") : -1 );
	$maxretry_limit = (int)WFU_VAR("WFU_TRANSFERMANAGER_MAX_RETRYTIME");
	$maxretries_limit = (int)WFU_VAR("WFU_TRANSFERMANAGER_MAX_RETRIES");
	$retries_limit = (int)WFU_VAR("WFU_TRANSFERMANAGER_RETRIES");	
	$async_limit = ( (int)WFU_VAR("WFU_ASYNC_TIMEOUT") > -1 ? time() - (int)WFU_VAR("WFU_ASYNC_TIMEOUT") : -1 );
	foreach( $recs as $ind => $rec ) {
		$service = $rec->service;
		//in case file has failed permanently (status is -2) then remove it from
		//the array and also check if permanently failed files should be kept
		//in the transfer queue or not
		if ( $rec->status == -2 ) {
			if ( WFU_VAR("WFU_TRANSFERMANAGER_KEEP_FAILED_FILES") != "true" ) {
				//remove record from queue
				$wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$rec->iddbxqueue);
			}
			//remove current record from the array
			unset($recs[$ind]);
		}
		//in case file failed or transfer time limit has been exceeded or async
		// limit has been exceeded then perform checks
		elseif ( $rec->status == -1 || 
			( $async_limit > -1 && $rec->status == 99 && $rec->filedata[$service]["start_time"] < $async_limit ) ||
			( $overtime_limit > -1 && $rec->status == 1 && $rec->filedata[$service]["start_time"] < $overtime_limit ) ) {
			$recs[$ind]->filedata[$service]["total_time"] += time() - $rec->filedata[$service]["start_time"];
			//in case maximum total transfer time or maximum total retries
			//have been exceeded then set file status to -2 denoting that it has
			//failed permanently or remove it from queue if this option is
			//activated
			if ( ( $maxretry_limit > -1 && $rec->filedata[$service]["total_time"] > $maxretry_limit ) ||
				( $maxretries_limit > -1 && $rec->filedata[$service]["total_retries"] >= $maxretries_limit ) ) {
				wfu_update_transferred_file($rec, -2);
				//remove current record from the array
				unset($recs[$ind]);
			}
		}
	}
	//second pass to check and process all other failed files
	foreach( $recs as $ind => $rec ) {
		$service = $rec->service;
		//in case file failed or transfer time limit has been exceeded or async
		// limit has been exceeded then perform checks
		if ( $rec->status == -1 ||
			( $async_limit > -1 && $rec->status == 99 && $rec->filedata[$service]["start_time"] < $async_limit ) ||
			( $overtime_limit > -1 && $rec->status == 1 && $rec->filedata[$service]["start_time"] < $overtime_limit ) ) {
			//reset the file in the queue
			$priority = $rec->priority;
			//in case that the number of consequtive retries has been
			//exceeded then set lowest priority of file in queue and
			//reset retries
			if ( $retries_limit > -1 && $rec->filedata[$service]["retries"] >= $retries_limit ) {
				foreach( $recs as $rec2 ) $priority = max($priority, $rec2->priority + 1);
				$recs[$ind]->filedata[$service]["retries"] = 0;
			}
			//reset properties in queue
			$recs[$ind] = wfu_update_transferred_file($rec, 0, "", $priority);
		}
	}
	
	//get count of all pending jobs (running or pending)
	$jobs_count = count($recs);
	//get count of transfers running
	$uploads_count = 0;
	foreach( $recs as $rec ) $uploads_count += ( $rec->status == 1 ? 1 : 0 );
	$max_uploads = (int)WFU_VAR("WFU_TRANSFERMANAGER_MAX_JOBS");
	//start more uploads if we have not reached concurrent upload limit
	if ( $max_uploads == -1 || $uploads_count < $max_uploads ) {
		//get pending uploads
		foreach( $recs as $ind => $rec ) if ( $rec->status != 0 ) unset($recs[$ind]);
		$pending_count = count($recs);
		//loop as long as we have not reached concurrent upload limit or number
		//of pending files
		while ( ( $max_uploads == -1 || $uploads_count < $max_uploads ) && $pending_count > 0 ) {
			//find record with highest priority
			$min_priority = -1;
			foreach( $recs as $rec ) {
				if ( $min_priority == -1 ) $min_priority = $rec->priority;
				else $min_priority = min($min_priority, $rec->priority);
			}
			foreach( $recs as $ind => $rec ) {
				$service = $rec->service;
				if ( $rec->priority == $min_priority ) {
					$jobid = wfu_generate_unique_jobid($service);
					//change properties in queue
					$recs[$ind] = wfu_update_transferred_file($rec, 99, $jobid);
					//start the service transfer of the file
					wfu_tf_LOG("call_upload_command fileid: ".$rec->fileid);
					if ( $plugin_options["modsecurity"] != "1" ) {
						wfu_call_async('wfu_transfer_file_start', array($rec->fileid, $jobid, $service));
					}
					else {
						//in case than mod_security module is enabled, then the
						//async call wont work and we need to execute the
						//transfer synchronously
						wfu_transfer_file_start($rec->fileid, $jobid, $service);
					}
					//remove record from array
					unset($recs[$ind]);
					$uploads_count++;
					$pending_count--;
				}
			}
		}		
	}

	//update upload manager status
	$props = wfu_get_transfermanager_props();
	wfu_update_option( 'wfu_transfermanager_props', array( "status" => 0, "nextrun" => 0, "start_time" => -1, "end_time" => time(), "jobs" => $jobs_count ));
	//if nextrun is set, then the transfermanager must run again immediately
	if ( $props["nextrun"] == 1 ) {
		if ( $plugin_options["modsecurity"] != "1" )
			wfu_call_async('wfu_schedule_transfermanager', array(true));
		else
			//in case than mod_security module is enabled, then the
			//async call wont work and we need to execute the 
			//transfermanager synchronously
			wfu_schedule_transfermanager(true);
	}
	wfu_tf_LOG("execute_transfermanager_end");
}

/**
 * Initiate Transfer of File.
 * 
 * This function first checks if service account is activated. If everything is
 * Ok, then this script marks the status of the file as 'uploading' and
 * executes the transfer function of the extension. After finish it adjusts the
 * status of the file depending on whether it has finished successfully or not.
 * If the file was transferred successfully and the administrator has selected
 * to delete the local file and all other pending transfers have finished for
 * this file, then it will be deleted from the web server.
 * 
 * Finally the script initiates transfermanager again, in order to check if
 * there are any other files in the queue to be transferred.
 *
 * @since 4.3.3
 *
 * @param int $fileid ID of the file to be transferred.
 * @param string $jobid Unique ID of the transfer job.
 * @param string $service The service to be used.
 *
 * @return mixed array containing information about transfer result.
 */
function wfu_transfer_file_start($fileid, $jobid, $service) {
	wfu_tf_LOG("transfer_start:".$fileid);
	//check if service's basic functions exist
	if ( !function_exists("wfu_{$service}_service_active") || !function_exists("wfu_{$service}_transfer_file") ) return wfu_tf_LOG("transfer_end:no_func");
	//check if service is activated
	if ( !call_user_func("wfu_{$service}_service_active") ) return wfu_tf_LOG("transfer_end:not_active");
	//check job for validity and get filedata and queue data structures
	$check = wfu_check_transfer_job($fileid, $jobid, $service);
	if ( !$check["valid"] ) return wfu_tf_LOG("transfer_end:".$check["error"]);
	$filedata = $check["filedata"];
	$data = $check["queuedata"];
	$filepath = wfu_path_rel2abs($filedata[$service]["filepath"]);
	//update file status
	$data->service = $service;
	$data->filedata = $filedata;
	$data = wfu_update_transferred_file($data, 1);
	//upload file
	$delay = 0;
	$end_time = time() + $delay;
	while ( time() < $end_time ) sleep(10);
	//call the service's transfer function; we pass the following parameters:
	//  - the filepath of the source file
	//  - the destination folder path
	//  - additional parameters, which are:
	//      - the id of the file's database record
	//      - the job id
	//the return value is an array with the following items:
	//  - result: boolean denoting if upload was successful or not
	//  - error: any error messages if upload failed
	//  - filepath: the final filepath of the transferred file on success
	$params = array(
		"fileid"	=> $fileid,
		"jobid"		=> $jobid
	);
	$ret = call_user_func("wfu_{$service}_transfer_file", $filepath, $filedata[$service]["destination"], $params);
	wfu_tf_LOG("transfer_start_end");
}

/**
 * Finalize Transfer of File.
 * 
 * This function runs after a file transfer has finished and performs post-
 * finish actions (log the upload, remove from queue, delete local file if
 * necessary).
 *
 * @since 4.3.3
 *
 * @param array $data The database queue record of the file.
 * @param array $result Array holding the result of the transfer.
 *
 * @return array The updated queue record of the file.
 */
function wfu_transfer_file_finish($data, $result) {
	wfu_tf_LOG("transfer_finish_start:".$data->fileid);
	global $wpdb;
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	$service = $data->service;
	$filedata = wfu_get_latest_filedata_from_id($data->fileid);

	$filepath = wfu_path_rel2abs($filedata[$service]["filepath"]);
	//on success then log actions, delete the file if deletelocal is activated
	//and file can be deleted, remove it from queue and update filedata
	if ( $result["result"] == true ) {
		$filedata[$service]["id"] = -1;
		$filedata[$service]["status"] = ( $result["error"] == "" ? "uploaded" : $result["error"] );
		$filedata[$service]["end_time"] = time();
		$filedata[$service]["transfer_time"] = $filedata[$service]["end_time"] - $filedata[$service]["start_time"];
		$filedata[$service]["total_time"] += $filedata[$service]["transfer_time"];
		$filedata[$service]["start_time"] = -1;
		wfu_save_filedata_from_id($data->fileid, $filedata);
		$logdata = json_encode(array(
			'service' => $service,
			'transferred' => ( $result["error"] == "" ),
			'error' => $result["error"],
			'destination' => $filedata[$service]["destination"],
			'new_filename' => ( $result["filepath"] == "" || wfu_basename($filepath) == wfu_basename($result["filepath"]) ? "" : wfu_basename($result["filepath"]) )
		));
		wfu_log_action('filetransfer:json:'.$logdata, $filepath, 0, '', 0, 0, '', null);
		$wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$data->iddbxqueue);
		//if the local file must be deleted, then check that there are no other
		//pending transfers and delete it
		if ( $filedata[$service]["deletelocal"] == 1 ) wfu_checkdelete_local_file($filedata, $filepath, $service);
	}
	//on fail then set file status to -1 (failed)
	else {
		$data = wfu_update_transferred_file($data, -1);
		if ( $result["error"] != "" ) wfu_tf_LOG("transfer_finish_error:".$result["error"]);
	}
	$data->filedata = $filedata;
	wfu_tf_LOG("transfer_finish_end");
	
	return $data;
}

/**
 * Check a File Transfer.
 * 
 * This function checks the status of a file transfer by executing the
 * respective check_transfer function of the service. It updates the status of
 * the file stored in its database record.
 *
 * @since 4.4.0
 *
 * @param int $fileid The ID of the file to check.
 * @param string $jobid The unique ID of the transfer job to check.
 * @param string $service The service where the file is transferred.
 */
function wfu_check_file_transfer($fileid, $jobid, $service) {
	call_user_func("wfu_{$service}_check_transfer", $fileid, $jobid);
}

/**
 * Check a Transfer Job.
 * 
 * This function checks if a transfer job is still valid. A job can become
 * obsolete for several reasons: service has been deactivated, job has been
 * deleted by a parallel operation, it has been replaced by a newer job for the
 * same file, file does not exist anymore. This function is necessary because
 * transfer of files is asynchronous and may happen in parallel PHP scripts.
 *
 * @since 4.3.0
 *
 * @param int $fileid The ID of the file to check.
 * @param string $jobid The unique ID of the transfer job to check.
 * @param string $service The service where the file is transferred.
 *
 * @return array Array holding check results.
 */
function wfu_check_transfer_job($fileid, $jobid, $service) {
	global $wpdb;
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";

	//check if service's basic functions exist
	$filedata = wfu_get_latest_filedata_from_id($fileid);
	if ( $filedata == null || !isset($filedata[$service]) ) return array( "valid" => false, "error" => "no_filedata", "filedata" => null, "queuedata" => null );
	//get queue row corresponding to jobid
	$data = $wpdb->get_row('SELECT * FROM '.$table_name3.' WHERE jobid = \''.$jobid.'\'');
	//abort if job does not exist, probably because it was deleted
	if ( $data == null ) return array( "valid" => false, "error" => "no_job", "filedata" => $filedata, "queuedata" => null );
	//abort if job record id does not match to the id kept in filedata; this can
	//happen when for any reason a second transfer job has been assigned for the
	//same file
	if ( $filedata[$service]["id"] != $data->iddbxqueue ) return array( "valid" => false, "error" => "job_nomatch", "filedata" => $filedata, "queuedata" => $data );
	$filepath = ( isset($filedata[$service]["filepath"]) ? wfu_path_rel2abs($filedata[$service]["filepath"]) : "" );
	//if file does not exist then abort
	if ( !wfu_file_exists($filepath, "wfu_check_transfer_job") ) return array( "valid" => false, "error" => "invalid_file", "filedata" => $filedata, "queuedata" => $data );
	return array( "valid" => true, "error" => "", "filedata" => $filedata, "queuedata" => $data );
}

/**
 * Update Status of a File Transfer.
 * 
 * This function updates the status of a file transfer stored in the database
 * record of the file.
 *
 * @since 4.3.4
 *
 * @param array $data The database queue record of the file transfer.
 * @param int $status The new status of the file transfer.
 * @param string $jobid Optional. The unique ID of the transfer job to check.
 * @param int $priority Optional. The priority of this transfer job in queue.
 *
 * @return array The updated queue record of the file transfer.
 */
function wfu_update_transferred_file($data, $status, $jobid = "", $priority = -1) {
	global $wpdb;
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	$service = $data->service;
	$filedata = wfu_get_latest_filedata_from_id($data->fileid);
	if ( $status == -2 ) {
		if ( WFU_VAR("WFU_TRANSFERMANAGER_KEEP_FAILED_FILES") == "true" )
			$wpdb->update($table_name3,
				array( 'status' => -2, 'jobid' => "", 'priority' => -1 ),
				array( 'iddbxqueue' => $data->iddbxqueue ),
				array( '%d', '%s', '%d' ), array( '%d' )
			);
		else $wpdb->query('DELETE FROM '.$table_name3.' WHERE iddbxqueue = '.$data->iddbxqueue);
		$data->status = -2;
		$data->jobid = "";
		$data->priority = -1;
		$filedata[$service]["id"] = $data->iddbxqueue;
		$filedata[$service]["status"] = "failed_permanently";
		$filedata[$service]["start_time"] = -1;
		wfu_save_filedata_from_id($data->fileid, $filedata);
	}
	elseif ( $status == -1 ) {
		$wpdb->update($table_name3,
			array( 'status' => -1, 'jobid' => "" ),
			array( 'iddbxqueue' => $data->iddbxqueue ),
			array( '%d', '%s' ), array( '%d' )
		);
		$data->status = -1;
		$data->jobid = "";
		$filedata[$service]["id"] = $data->iddbxqueue;
		$filedata[$service]["status"] = "failed";
		wfu_save_filedata_from_id($data->fileid, $filedata);
	}
	elseif ( $status == 0 ) {
		$wpdb->update($table_name3,
			array( 'status' => 0, 'jobid' => "", 'priority' => $priority ),
			array( 'iddbxqueue' => $data->iddbxqueue ),
			array( '%d', '%s', '%d' ), array( '%d' )
		);
		$data->status = 0;
		$data->jobid = "";
		$data->priority = $priority;
		$filedata[$service]["id"] = $data->iddbxqueue;
		$filedata[$service]["status"] = "pending";
		$filedata[$service]["start_time"] = -1;
		wfu_save_filedata_from_id($data->fileid, $filedata);
	}
	elseif ( $status == 1 ) {
		$wpdb->update($table_name3,
			array( 'status' => 1 ),
			array( 'iddbxqueue' => $data->iddbxqueue ),
			array( '%d' ), array( '%d' )
		);
		$data->status = 1;
		$start_time = time();
		$retries = $filedata[$service]["retries"] + 1;
		$total_retries = $filedata[$service]["total_retries"] + 1;
		$filedata[$service]["id"] = $data->iddbxqueue;
		$filedata[$service]["status"] = "uploading";
		$filedata[$service]["retries"] = $retries;
		$filedata[$service]["total_retries"] = $total_retries;
		$filedata[$service]["start_time"] = $start_time;
		$filedata[$service]["end_time"] = -1;
		//initialize upload_data array to hold service-specific information about
		//the upload, set finished flag to false
		$filedata[$service]["upload_data"] = array( "finished" => false, "result" => "", "progress" => -1 );
		wfu_save_filedata_from_id($data->fileid, $filedata);
	}
	elseif ( $status == 99 ) {
		$wpdb->update($table_name3,
			array( 'status' => 99, 'jobid' => $jobid ),
			array( 'iddbxqueue' => $data->iddbxqueue ),
			array( '%d', '%s' ), array( '%d' )
		);
		$data->status = 99;
		$data->jobid = $jobid;
		$start_time = time();
		$filedata[$service]["id"] = $data->iddbxqueue;
		$filedata[$service]["status"] = "starting";
		$filedata[$service]["start_time"] = $start_time;
		wfu_save_filedata_from_id($data->fileid, $filedata);
	}
	
	$data->filedata = $filedata;
	return $data;
}

/**
 * Set Transfer Result of a File Transfer.
 * 
 * This function updates the transfer result of a file transfer stored in the
 * database record of the file.
 *
 * @since 4.4.0
 *
 * @param int $fileid The ID of the file.
 * @param string $jobid The unique ID of the transfer job.
 * @param string $service The service where the file is transferred.
 * @param bool $success The result of the file transfer.
 * @param string $error Error message if the transfer failed.
 * @param string $filepath The file path of the file.
 * @param null|array $remotefile_metadata Optional. It contains sharing
 *        information of the file transferred to the cloud service.
 */
function wfu_set_transfer_result($fileid, $jobid, $service, $success, $error, $filepath, $remotefile_metadata = null) {
	$check = wfu_check_transfer_job($fileid, $jobid, $service);
	if ( $check["valid"] ) {
		$filedata = $check["filedata"];
		$filedata[$service]["upload_data"]["finished"] = true;
		$filedata[$service]["upload_data"]["result"] = array(
			"result" => $success,
			"error"  => $error,
			"filepath" => $filepath
		);
		//store remote file metadata, if they exist
		if ( $remotefile_metadata != null ) {
			//check if a thumbnail link exists in remote file metadata; in this
			//case get the thumbnail and store it to thumbnails of the file
			$thumbdir = wfu_get_thumbnail_path(true);
			if ( $remotefile_metadata['thumbnailLink'] != "" && $thumbdir != "" ) {
				if ( $thumbdir == "@" ) $thumbdir = wfu_basedir($filepath);
				if ( substr($thumbdir, -1) != '/' ) $thumbdir .= '/';
				$thumbdata = wfu_get_request($remotefile_metadata['thumbnailLink']);
				if ( $thumbdata != "" ) {
					$imagepath = $thumbdir."thumbnail_".wfu_create_random_string(8).".png";
					$res = file_put_contents($imagepath, $thumbdata);
					if ( $res === false || $res == 0 ) $imagepath = null;
				}
				//$imagepath = wfu_save_base64encoded_image($thumbdir, "thumbnail_".wfu_create_random_string(8), $thumbdata);
				if ( $imagepath !== false ) {
					$check = wfu_file_is_valid_image($imagepath);
					if ( $check != null ) 
						$filedata["thumbnails"]["size_".$check["width"]."x".$check["height"]] = $imagepath;
				}
			}
			$filedata[$service]["upload_data"]["metadata"] = $remotefile_metadata;
		}
		wfu_save_filedata_from_id($fileid, $filedata);
	}
}

/**
 * Set Transfer Progress of a File Transfer.
 * 
 * This function updates the transfer progress of a file transfer stored in the
 * database record of the file.
 *
 * @since 4.4.0
 *
 * @param int $fileid The ID of the file.
 * @param string $jobid The unique ID of the transfer job.
 * @param string $service The service where the file is transferred.
 * @param int $progress The transfer progress (from 0 to 100).
 */
function wfu_set_transfer_progress($fileid, $jobid, $service, $progress) {
	$check = wfu_check_transfer_job($fileid, $jobid, $service);
	if ( $check["valid"] ) {
		$filedata = $check["filedata"];
		$filedata[$service]["upload_data"]["progress"] = $progress;
		wfu_save_filedata_from_id($fileid, $filedata);
	}
}

/**
 * Store Transfer Data of a File Transfer.
 * 
 * This function stores transfer data of a file transfer stored in the database
 * record of the file.
 *
 * @since 4.4.0
 *
 * @param int $fileid The ID of the file.
 * @param string $jobid The unique ID of the transfer job.
 * @param string $service The service where the file is transferred.
 * @param array $data The transfer data to store.
 */
function wfu_store_service_transfer_data($fileid, $jobid, $service, $data) {
	$check = wfu_check_transfer_job($fileid, $jobid, $service);
	if ( $check["valid"] ) {
		$filedata = $check["filedata"];
		foreach ( $data as $key => $value )
			$filedata[$service]["upload_data"][$key] = $value;
		wfu_save_filedata_from_id($fileid, $filedata);
	}
	return $check["valid"];
}

/**
 * Get Transfer Data of a File Transfer.
 * 
 * This function retrieves transfer data of a file transfer from the database
 * record of the file.
 *
 * @since 4.4.0
 *
 * @param int $fileid The ID of the file.
 * @param string $jobid The unique ID of the transfer job.
 * @param string $service The service where the file is transferred.
 *
 * @return array An array containing the transfer data.
 */
function wfu_get_service_transfer_data($fileid, $jobid, $service) {
	$data = array();
	$check = wfu_check_transfer_job($fileid, $jobid, $service);
	if ( $check["valid"] ) {
		$data = $check["filedata"][$service]["upload_data"];
		unset($data["finished"]);
		unset($data["result"]);
		unset($data["progress"]);
	}
	return $data;
}

/**
 * Checks and Delete Local File.
 *  
 * This function first checks whether there are any pending transfer jobs for
 * this file. If there aren't, it deletes the file and logs the action. In case
 * that parameter $switch_to_remote_file_of_service is not empty, the local file
 * will be deleted but the database record will remain, pointing to the file
 * stored in the cloud service.
 *
 * @since 4.1.0
 *  
 * @param array $filedata The filedata array of the file.
 * @param string $filepath The absolute path of the file.
 * @param string $switch_to_remote_file_of_service Optional. The cloud service
 *        where the file was lastly transferred.
 */
function wfu_checkdelete_local_file($filedata, $filepath, $switch_to_remote_file_of_service = "") {
	global $wpdb;
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";
	
	$ids = array();
	foreach ( $filedata as $data ) 
		if ( !isset($data["type"]) || $data["type"] == "transfer" )
			array_push($ids, $data["id"]);
	$count = $wpdb->get_var('SELECT COUNT(*) FROM '.$table_name3.' WHERE iddbxqueue IN ('.implode(",", $ids).')');
	if ( $count == 0 ) wfu_delete_file_execute($filepath, -999, null, $switch_to_remote_file_of_service);
}

/**
 * Check For Any Active Transfer Service.
 *  
 * This function checks whether there are any transfer services activated.
 *
 * @since 4.1.0
 *  
 * @return bool True if at least one service is activated, false otherwise.
 */
function wfu_filetransfer_any_service_active() {
	$any_service_active = false;
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
	foreach ( $services as $service ) {
		$func = "wfu_{$service}_service_active";
		if ( function_exists($func) )
			$any_service_active = $any_service_active || call_user_func($func);
		if ( $any_service_active ) break;
	}
	
	return $any_service_active;
}

/**
 * Get Service of a File Transfer.
 *  
 * This function gets the service of a file transfer from the database record of
 * the file.
 *
 * @since 4.1.0
 *  
 * @param array $filedata The filedata array of the file.
 * @param int $id The database record ID of the transfer job.
 *
 * @return string The transfer service of this file transfer.
 */
function wfu_get_service_from_filedata($filedata, $id) {
	$service = "";
	if ( $filedata != null )
		foreach ( $filedata as $ind => $data ) {
			if ( !isset($data["type"]) || $data["type"] == "transfer" ) {
				if ( isset($data["id"]) && $data["id"] == $id ) {
					$service = $ind;
					break;
				}
			}
		}
	
	return $service;
}

/**
 * Generate a Unique Job ID.
 *  
 * This function generates a unique job ID from a random string.
 *
 * @since 4.6.0
 *  
 * @param string $service The service related to the specific job.
 *
 * @return string The unique job ID.
 */
function wfu_generate_unique_jobid($service) {
	return substr($service, 0, 1).'_'.wfu_create_random_string(8);	
}

/**
 * Get Transfer Manager Properties.
 *  
 * This function reads Transfer Manager properties from Options db table. It
 * keeps backward compatibility with older versions of the plugin that kept less
 * properties.
 *
 * @since 4.6.0
 *  
 * @return array An array containing Transfer Manager properties.
 */
function wfu_get_transfermanager_props() {
	//wfu_transfermanager_props contains the following items:
	// - status: whether transfermanager is currently running or not
	// - nextrun: whether transfermanager needs to run again after finish of
	//            current execution
	// - start_time: start time of current transfermanager execution
	// - end_time: end time of last transfermanager execution
	// - jobs: number of pending jobs
	//it is noted that due to updates of wfu_transfermanager_props structure we
	//need to make sure that $props contains all items otherwise we may get some
	//PHP warnings and maybe some unexpected behaviour
	$props_def = array( "status" => 0, "nextrun" => 0, "start_time" => -1, "end_time" => -1, "jobs" => 0 );
	$props = wfu_get_option('wfu_transfermanager_props', $props_def);
	foreach ( $props_def as $prop => $value ) if ( !isset($props[$prop]) ) $props[$prop] = $value;
	
	return $props;
}

/**
 * Get Name of Transfer Service.
 *  
 * This function invokes the respective get_name function of the service to read
 * its name.
 *
 * @since 4.7.0
 *  
 * @return string The name of the service.
 */
function wfu_service_get_name($service) {
	$name = $service;
	$func = "wfu_{$service}_get_name";
	if ( function_exists($func) ) $name = call_user_func($func);
	
	return $name;
}

/**
 * Log Debug Message
 *  
 * This function logs a timestamped debug message, together with the status of
 * wfu_transfermanager_props option, in the debug_log.txt file.
 *
 * @since 4.1.0
 *  
 * @param string $message A log message.
 */
function wfu_tf_LOG($message) {
	if ( WFU_VAR("WFU_DEBUG") != "ON" || !isset($GLOBALS["wfu_debug-wfu_tf_LOG"]) ) return;
	$props = wfu_get_transfermanager_props();
	$echo = '['.date("Y-m-d H:i:s", time()).'] '.$message;
	$echo .= ' transfermanager_props: status('.$props["status"].'), nextrun('.$props["nextrun"].'), endtime('.$props["end_time"].')';
	wfu_debug_log($echo."\n");
}