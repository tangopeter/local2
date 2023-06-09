<?php
if ( !defined("ABSWPFILEUPLOAD_DIR") ) DEFINE("ABSWPFILEUPLOAD_DIR", dirname(__FILE__).'/');
if ( !defined("WFU_AUTOLOADER_PHP50600") ) DEFINE("WFU_AUTOLOADER_PHP50600", 'vendor/modules/php5.6/autoload.php');
include_once( ABSWPFILEUPLOAD_DIR.'lib/wfu_functions.php' );
include_once( ABSWPFILEUPLOAD_DIR.'lib/wfu_security.php' );
$handler = (isset($_POST['handler']) ? $_POST['handler'] : (isset($_GET['handler']) ? $_GET['handler'] : '-1'));
$session_legacy = (isset($_POST['session_legacy']) ? $_POST['session_legacy'] : (isset($_GET['session_legacy']) ? $_GET['session_legacy'] : ''));
$dboption_base = (isset($_POST['dboption_base']) ? $_POST['dboption_base'] : (isset($_GET['dboption_base']) ? $_GET['dboption_base'] : '-1'));
$dboption_useold = (isset($_POST['dboption_useold']) ? $_POST['dboption_useold'] : (isset($_GET['dboption_useold']) ? $_GET['dboption_useold'] : ''));
$wfu_cookie = (isset($_POST['wfu_cookie']) ? $_POST['wfu_cookie'] : (isset($_GET['wfu_cookie']) ? $_GET['wfu_cookie'] : ''));
if ( $handler == '-1' || $session_legacy == '' || $dboption_base == '-1' || $dboption_useold == '' || $wfu_cookie == '' ) die();
else {
	$GLOBALS["wfu_user_state_handler"] = wfu_sanitize_code($handler);
	$GLOBALS["WFU_GLOBALS"]["WFU_US_SESSION_LEGACY"] = array( "", "", "", ( $session_legacy == '1' ? 'true' : 'false' ), "", true );
	$GLOBALS["WFU_GLOBALS"]["WFU_US_DBOPTION_BASE"] = array( "", "", "", wfu_sanitize_code($dboption_base), "", true );
	$GLOBALS["WFU_GLOBALS"]["WFU_US_DBOPTION_USEOLD"] = array( "", "", "", ( $dboption_useold == '1' ? 'true' : 'false' ), "", true );
	if ( !defined("WPFILEUPLOAD_COOKIE") ) DEFINE("WPFILEUPLOAD_COOKIE", wfu_sanitize_tag($wfu_cookie));
	wfu_download_file();
}

function wfu_download_file() {
	global $wfu_user_state_handler;
	$file_code = (isset($_POST['file']) ? $_POST['file'] : (isset($_GET['file']) ? $_GET['file'] : ''));
	$ticket = (isset($_POST['ticket']) ? $_POST['ticket'] : (isset($_GET['ticket']) ? $_GET['ticket'] : ''));
	if ( $file_code == '' || $ticket == '' ) die();
	
	wfu_initialize_user_state();
	
	$ticket = wfu_sanitize_code($ticket);	
	$file_code = wfu_sanitize_code($file_code);
	//if download ticket does not exist or is expired die
	if ( !WFU_USVAR_exists_downloader('wfu_download_ticket_'.$ticket) || time() > WFU_USVAR_downloader('wfu_download_ticket_'.$ticket) ) {
		WFU_USVAR_unset_downloader('wfu_download_ticket_'.$ticket);
		WFU_USVAR_unset_downloader('wfu_storage_'.$file_code);
		wfu_update_download_status($ticket, 'failed');
		die();
	}
	//destroy ticket so it cannot be used again
	WFU_USVAR_unset_downloader('wfu_download_ticket_'.$ticket);
	
	//if file_code starts with exportdata, then this is a request for export of
	//uploaded file data, so disposition_name will not be the filename of the file
	//but wfu_export.csv; also set flag to delete file after download operation
	if ( substr($file_code, 0, 10) == "exportdata" ) {
		$file_code = substr($file_code, 10);
		//$filepath = wfu_get_filepath_from_safe($file_code);
		$filepath = WFU_USVAR_downloader('wfu_storage_'.$file_code);
		$disposition_name = "wfu_export.csv";
		$delete_file = true;
	}
	else {
		//$filepath = wfu_get_filepath_from_safe($file_code);
		$filepath = WFU_USVAR_downloader('wfu_storage_'.$file_code);
		if ( $filepath === false ) {
			WFU_USVAR_unset_downloader('wfu_storage_'.$file_code);
			wfu_update_download_status($ticket, 'failed');
			die();
		}
		$filepath = wfu_flatten_path($filepath);
		if ( substr($filepath, 0, 1) == "/" ) $filepath = substr($filepath, 1);
		$filepath = ( substr($filepath, 0, 6) == 'ftp://' || substr($filepath, 0, 7) == 'ftps://' || substr($filepath, 0, 7) == 'sftp://' ? $filepath : WFU_USVAR_downloader('wfu_ABSPATH').$filepath );
		$disposition_name = wfu_basename($filepath);
		$delete_file = false;
	}
	//destroy file code as it is no longer needed
	WFU_USVAR_unset_downloader('wfu_storage_'.$file_code);
	//check that file exists
	if ( !wfu_file_exists_for_downloader($filepath) ) {
		wfu_update_download_status($ticket, 'failed');
		die('<script language="javascript">alert("'.( WFU_USVAR_exists_downloader('wfu_browser_downloadfile_notexist') ? WFU_USVAR_downloader('wfu_browser_downloadfile_notexist') : 'File does not exist!' ).'");</script>');
	}

	$open_session = false;
	@set_time_limit(0); // disable the time limit for this script
	$fsize = wfu_filesize_for_downloader($filepath);
	if ( $fd = wfu_fopen_for_downloader($filepath, "rb") ) {
		$open_session = ( ( $wfu_user_state_handler == "session" || $wfu_user_state_handler == "" ) && ( function_exists("session_status") ? ( PHP_SESSION_ACTIVE !== session_status() ) : ( empty(session_id()) ) ) );
		if ( $open_session ) session_start();
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"".$disposition_name."\"");
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-length: $fsize");
		$failed = false;
		while( !feof($fd) ) {
			$buffer = @fread($fd, 1024*8);
			echo $buffer;
			ob_flush();
			flush();
			if ( connection_status() != 0 ) {
				$failed = true;
				break;
			}
		}
		fclose ($fd);
	}
	else $failed = true;
	
	if ( $delete_file ) wfu_unlink_for_downloader($filepath);
	
	if ( !$failed ) {
		wfu_update_download_status($ticket, 'downloaded');
		if ( $open_session ) session_write_close();
		die();
	}
	else {
		wfu_update_download_status($ticket, 'failed');
		if ( $open_session ) session_write_close();
		die('<script type="text/javascript">alert("'.( WFU_USVAR_exists_downloader('wfu_browser_downloadfile_failed') ? WFU_USVAR_downloader('wfu_browser_downloadfile_failed') : 'Could not download file!' ).'");</script>');
	}
}

function wfu_update_download_status($ticket, $new_status) {
	require_once WFU_USVAR_downloader('wfu_ABSPATH').'wp-load.php';
	WFU_USVAR_store('wfu_download_status_'.$ticket, $new_status);
}

function WFU_USVAR_exists_downloader($var) {
	global $wfu_user_state_handler;
	if ( $wfu_user_state_handler == "dboption" && WFU_VAR("WFU_US_DBOPTION_BASE") == "cookies" ) return isset($_COOKIE[$var]);
	else return WFU_USVAR_exists_session($var);
}

function WFU_USVAR_downloader($var) {
	global $wfu_user_state_handler;
	if ( $wfu_user_state_handler == "dboption" && WFU_VAR("WFU_US_DBOPTION_BASE") == "cookies" ) return $_COOKIE[$var];
	else return WFU_USVAR_session($var);
}

function WFU_USVAR_unset_downloader($var) {
	global $wfu_user_state_handler;
	if ( $wfu_user_state_handler == "session" || $wfu_user_state_handler == "" ) WFU_USVAR_unset_session($var);
}

function wfu_file_exists_for_downloader($filepath) {
	if ( substr($filepath, 0, 7) != "sftp://" ) return file_exists($filepath);
	$ret = false;
	$ftpinfo = wfu_decode_ftpurl($filepath);
	if ( $ftpinfo["error"] ) return $ret;
	$data = $ftpinfo["data"];
	//detect whether PHPSecLib or SSH2 library will be used
	if ( !function_exists("ssh2_connect") ) {
		include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
		$sftp = new phpseclib\Net\SFTP($data["ftpdomain"], $data["port"]);
		$ret = ( $sftp->login($data["username"], $data["password"]) && $sftp->file_exists($data["filepath"]) );
	}
	else
	{
		$conn = @ssh2_connect($data["ftpdomain"], $data["port"]);
		if ( $conn && @ssh2_auth_password($conn, $data["username"], $data["password"]) ) {
			$sftp = @ssh2_sftp($conn);
			$ret = ( $sftp && @file_exists("ssh2.sftp://".intval($sftp).$data["filepath"]) );
		}
	}
	
	return $ret;
}

function wfu_filesize_for_downloader($filepath) {
	if ( substr($filepath, 0, 7) != "sftp://" ) return filesize($filepath);
	$ret = false;
	$ftpinfo = wfu_decode_ftpurl($filepath);
	if ( $ftpinfo["error"] ) return $ret;
	$data = $ftpinfo["data"];
	//detect whether PHPSecLib or SSH2 library will be used
	if ( !function_exists("ssh2_connect") ) {
		include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
		$sftp = new phpseclib\Net\SFTP($data["ftpdomain"], $data["port"]);
		if ( $sftp->login($data["username"], $data["password"]) ) $ret = $sftp->size($data["filepath"]);
	}
	else
	{
		$conn = @ssh2_connect($data["ftpdomain"], $data["port"]);
		if ( $conn && @ssh2_auth_password($conn, $data["username"], $data["password"]) ) {
			$sftp = @ssh2_sftp($conn);
			if ( $sftp ) $ret = @filesize("ssh2.sftp://".intval($sftp).$data["filepath"]);
		}
	}
	
	return $ret;
}

function wfu_fopen_for_downloader($filepath, $mode) {
	if ( substr($filepath, 0, 7) != "sftp://" ) return @fopen($filepath, $mode);
	$ret = false;
	$ftpinfo = wfu_decode_ftpurl($filepath);
	if ( $ftpinfo["error"] ) return $ret;
	$data = $ftpinfo["data"];
	//detect whether PHPSecLib or SSH2 library will be used
	if ( !function_exists("ssh2_connect") ) {
		include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
		$sftp = new phpseclib\Net\SFTP($data["ftpdomain"], $data["port"]);
		if ( $sftp->login($data["username"], $data["password"]) ) {
			$contents = $sftp->get($data["filepath"]);
			$stream = fopen('php://memory', 'r+');
			fwrite($stream, $contents);
			rewind($stream);
			$ret = $stream;
		}
	}
	else
	{
		$conn = @ssh2_connect($data["ftpdomain"], $data["port"]);
		if ( $conn && @ssh2_auth_password($conn, $data["username"], $data["password"]) ) {
			$sftp = @ssh2_sftp($conn);
			if ( $sftp ) {
				//$ret = @fopen("ssh2.sftp://".intval($sftp).$data["filepath"], $mode);
				$contents = @file_get_contents("ssh2.sftp://".intval($sftp).$data["filepath"]);
				$stream = fopen('php://memory', 'r+');
				fwrite($stream, $contents);
				rewind($stream);
				$ret = $stream;
			}
		}
	}
	
	return $ret;
}

function wfu_unlink_for_downloader($filepath) {
	if ( substr($filepath, 0, 7) != "sftp://" ) return @unlink($filepath);
	$ret = false;
	$ftpinfo = wfu_decode_ftpurl($filepath);
	if ( $ftpinfo["error"] ) return $ret;
	$data = $ftpinfo["data"];
	//detect whether PHPSecLib or SSH2 library will be used
	if ( !function_exists("ssh2_connect") ) {
		include_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP50600;
		$sftp = new phpseclib\Net\SFTP($data["ftpdomain"], $data["port"]);
		if ( $sftp->login($data["username"], $data["password"]) ) $ret = $sftp->delete($data["filepath"]);
	}
	else
	{
		$conn = @ssh2_connect($data["ftpdomain"], $data["port"]);
		if ( $conn && @ssh2_auth_password($conn, $data["username"], $data["password"]) ) {
			$sftp = @ssh2_sftp($conn);
			if ( $sftp ) $ret = @unlink("ssh2.sftp://".intval($sftp).$data["filepath"]);
		}
	}
	
	return $ret;
}