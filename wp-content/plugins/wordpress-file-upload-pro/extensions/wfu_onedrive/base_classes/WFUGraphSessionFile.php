<?php 
/**
* Copyright (c) Nickolas Bossinas.
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
* 
* WFUGraphRequest File
* PHP version 7
*
* @category  Library
* @package   WFU.OneDrive
* @copyright 2018 Nickolas Bossinas
* @license   http://www.gnu.org/licenses/
* @version   0.1.0
* @link      https://www.iptanus.com/
*/

namespace WFU;

use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Http\GraphResponse;;
use GuzzleHttp\Client;
use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Exception\GraphException;

/**
 * Class WFUGraphSessionFile
 *
 * @category Library
 * @package  WFU.OneDrive
 * @license  http://www.gnu.org/licenses/
 * @link     https://www.iptanus.com/
 */
class WFUGraphSessionFile
{
	private $Graph;
	
	private $filepath;
	
	private $filesize;
	
	private $destination;
	
	private $params;
	
	private $uploadUrl;
	
	private $expiration;
	
	private $nextExpectedRanges;
	
	private $chunkSize;
	
	private $finished;
	
	private $fileID;
	
	private $uploadResponse;
	
	private $new_filename;

	public function __construct($filepath, $destination, $params, $Graph)
	{
		$this->Graph = $Graph;
		$this->filepath = $filepath;
		$this->filesize = wfu_filesize($filepath, "WFUGraphSessionFile::__construct");
		$this->destination = $destination;
		$this->params = $params;
		//set chunk size from advanced variable and adjust it to be multiple of
		//320KB, min 320KB and max 60MB
		$this->chunkSize = min(max(round(WFU_VAR("WFU_ONEDRIVE_CHUNK_SIZE") / 327680), 1), 192) * 327680;
		$this->finished = false;
		$this->new_filename = "";
	}
	
	public function create_session() {
		$Graph = $this->Graph;
		$request = $Graph->createRequest("POST", ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? '' : '/me' )."/drive/root:".$this->destination.wfu_basename($this->filepath).":/".( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? 'oneDrive.' : '' )."createUploadSession");
		$options = [
			'@name.conflictBehavior' => $this->params["conflict_policy"],
			'name' => wfu_basename($this->filepath)
		];
		//include userdata in file's description if they exist
		if ( isset($this->params['userdata']) ) {
			$description = "";
			foreach ( $this->params["userdata"] as $item )
				$description .= ( $description == "" ? "" : "\n" ).$item["property"].": ".$item["value"]." ";
			if ( $description != "" ) $options['description'] = $description;
		}
		$request->attachBody([ 'item' => $options ]);
		$rawresponse = $request->execute();
		$response = $rawresponse->getBody();
		$this->uploadUrl = $response['uploadUrl'];
		$this->expiration = $response['expirationDateTime'];
		$this->nextExpectedRanges = $this->_normalize_ranges($response['nextExpectedRanges']);
		
		return true;
	}

	public function resume_session($uploadUrl) {
		$Graph = $this->Graph;
		$request = $Graph->createRequest("GET", $uploadUrl);
		$rawresponse = $request->execute();
		$response = $rawresponse->getBody();
		$this->uploadUrl = $uploadUrl;
		$this->expiration = $response['expirationDateTime'];
		$this->nextExpectedRanges = $this->_normalize_ranges($response['nextExpectedRanges']);
		
		return true;
	}
	
	public function next_chunk() {
		if ( count($this->nextExpectedRanges) == 0 ) {
			//make an effort to resume the session
			$this->resume_session($this->uploadUrl);
			if ( count($this->nextExpectedRanges) == 0 ) return false;
		}
		//calculate next range
		$next_range = $this->nextExpectedRanges[0];
		$start = $next_range['start'];
		$length = min($next_range['length'], $this->chunkSize);
		$end = $length + $start - 1;

		$Graph = $this->Graph;
		$request = $Graph->createRequest("PUT", $this->uploadUrl);
		//add range headers
		$request->addHeaders([
			'Content-Length' => $length,
			'Content-Range' => "bytes $start-$end/".$this->filesize
		]);
		//get file chunk
		$f = wfu_fopen($this->filepath, "rb", "WFUGraphSessionFile::next_chunk");
		fseek($f, $start);
		$chunk = fread($f, $length);
		fclose($f);
		//put file in stream
		$stream = \GuzzleHttp\Psr7\stream_for($chunk);
		$request->attachBody($stream);
		//execute
		$rawresponse = $request->execute();
		$response = $rawresponse->getBody();
		$status = $rawresponse->getStatus();
		if ( $status == 202 ) {
			$this->expiration = $response['expirationDateTime'];
			$this->nextExpectedRanges = $this->_normalize_ranges($response['nextExpectedRanges']);
			return true;
		}
		elseif ( $status == 200 || $status == 201 ) {
			$this->fileID = $response['id'];
			$name = $response['name'];
			if ( $name != wfu_basename($this->filepath) ) $this->new_filename = $name;
			$this->nextExpectedRanges = array();
			$this->finished = true;
			$this->uploadResponse = $response;
			return true;
		}
		return false;
	}

	public function getProgress() {
		$remaining = $this->filesize;
		if ( count($this->nextExpectedRanges) == 0 ) $remaining = 0;
		else {
			$remaining = 0;
			foreach ( $this->nextExpectedRanges as $range ) $remaining += $range['length'];
		}
		$uploaded = $this->filesize - $remaining;
		
		return $uploaded;
	}
	
	public function getGraph() {
		return $this->Graph;
	}
	
	public function getUploadURL() {
		return $this->uploadUrl;
	}
	
	public function getchunkSize() {
		return $this->chunkSize;
	}
	
	public function is_finished() {
		return ($this->finished === true);
	}
	
	public function get_finalpath() {
		if ( $this->finished !== true || $this->new_filename == "" ) return $this->filepath;
		else return wfu_basedir($this->filepath).$this->new_filename;
	}
	
	public function get_response() {
		if ( $this->finished !== true ) return null;
		else return $this->uploadResponse;
	}
	
	private function _normalize_ranges($rawranges) {
		$ranges = array( array( 'start' => 0, 'end' => $this->filesize - 1, 'length' => $this->filesize ) );
		if ( is_array($rawranges) && count($rawranges) > 0 ) {
			$ranges = array();
			$prevend = -1;
			foreach ( $rawranges as $range ) {
				$has_error = false;
				$pos = strpos($range, "-");
				$has_error = ( $pos === false );
				if ( $has_error ) break;
				$start = trim(substr($range, 0, $pos));
				$end = trim(substr($range, $pos + 1));
				$has_error = ( $start == "" );
				if ( $has_error ) break;
				$start = (int)$start;
				if ( $end == "" ) $end = $this->filesize - 1;
				else $end = (int)$end;
				$has_error = ( $start <= $prevend || $start > $end || $start >= $this->filesize || $end >= $this->filesize );
				if ( $has_error ) break;
				array_push($ranges, array( 'start' => $start, 'end' => $end, 'length' => ($end - $start + 1) ));
				$prevend = $end;
			}
			if ( $has_error ) $ranges = array();
		}
		
		return $ranges;
	}
}