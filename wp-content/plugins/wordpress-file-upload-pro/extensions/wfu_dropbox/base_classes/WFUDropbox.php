<?php
namespace WFU;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Models\FileMetadata;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

/**
 * Dropbox
 */
class WFUDropbox extends Dropbox
{
    /**
     * Upload a File to Dropbox in a single request
     *
     * @param  string|DropboxFile $dropboxFile DropboxFile object or Path to file
     * @param  string             $path        Path to upload the file to
     * @param  array              $params      Additional Params
     *
     * @link https://www.dropbox.com/developers/documentation/http/documentation#files-upload
     *
     * @return \Kunnu\Dropbox\Models\FileMetadata
     */
    public function simpleUpload($dropboxFile, $path, array $params = [])
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile);

        //Set the path and file
        $params['path'] = $path;
        $params['file'] = $dropboxFile;
		//store full params structure to use them later
		$stored_params = $params;
		$fileid = $params["fileid"];
		$jobid = $params["jobid"];
		//clean params to keep only those necessary for the upload
		unset($params["fileid"]);
		unset($params["jobid"]);
		unset($params["share_file"]);
		wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array( "upload_type" => "simple" ));

        //Upload File
        $file = $this->postToContent('/files/upload', $params);
        $body = $file->getDecodedBody();

        //Make and Return the Model
		$uploadMetadata = new FileMetadata($body);
		$filepath = wfu_basedir($dropboxFile->getFilePath()).$uploadMetadata->getName();
		$metadata = wfu_dropbox_post_upload_actions($uploadMetadata, $stored_params, $this, false);
		wfu_set_transfer_result($fileid, $jobid, "dropbox", true, "", $filepath, $metadata);
        return $uploadMetadata;
    }

	public function checkUpload($fileid, $jobid)
	{
		$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "dropbox");
		if ( isset($upload_data["upload_type"]) && $upload_data["upload_type"] == "chunked" && $upload_data["remaining"] > 0 ) {
			$since_last_time = time() - $upload_data["last_chunk_time"];
			$timeout = (int)WFU_VAR("WFU_DROPBOX_MAX_CHUNKTIME");
			if ( $timeout > -1 && $since_last_time > $timeout ) {
				$filepath = $upload_data["filepath"];
				$dropboxFile = new DropboxFile($filepath);
				wfu_tf_LOG("reuploading ".$dropboxFile->getFileName());
				$path = $upload_data["path"];
				$fileSize = $upload_data["fileSize"];
				$chunkSize = $upload_data["chunkSize"];
				$params = unserialize($upload_data["params"]);
				$sessionId = $upload_data["sessionId"];
				$uploaded = $upload_data["uploaded"];
				$remaining = $upload_data["remaining"];
				try {
					return $this->reUploadChunked($dropboxFile, $path, $fileSize, $chunkSize, $params, $sessionId, $uploaded, $remaining);
				}
				catch (DropboxClientException $e) {
					$data = json_decode($e->getMessage());
					if ( $data && isset($data->error) && isset($data->error->correct_offset) ) {
						$uploaded = $data->error->correct_offset;
						$remaining = $fileSize - $uploaded;
						return $this->reUploadChunked($dropboxFile, $path, $fileSize, $chunkSize, $params, $sessionId, $uploaded, $remaining);
					}
					else throw new DropboxClientException($e->getMessage());
				}		
			}
		}
	}

    /**
     * Upload file in sessions/chunks
     *
     * @param  string|DropboxFile $dropboxFile DropboxFile object or Path to file
     * @param  string             $path        Path to save the file to, on Dropbox
     * @param  int                $fileSize    The size of the file
     * @param  int                $chunkSize   The amount of data to upload in each chunk
     * @param  array              $params      Additional Params
     *
     * @link https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-start
     * @link https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-finish
     * @link https://www.dropbox.com/developers/documentation/http/documentation#files-upload_session-append_v2
     *
     * @return string Unique identifier for the upload session
     */
    public function uploadChunked($dropboxFile, $path, $fileSize = null, $chunkSize = null, array $params = array())
    {
        //Make Dropbox File
        $dropboxFile = $this->makeDropboxFile($dropboxFile);

        //No file size specified explicitly
        if (is_null($fileSize)) {
            $fileSize = $dropboxFile->getSize();
        }

        //No chunk size specified, use default size
        if (is_null($chunkSize)) {
            $chunkSize = static::DEFAULT_CHUNK_SIZE;
        }

        //If the filesize is smaller
        //than the chunk size, we'll
        //make the chunk size relatively
        //smaller than the file size
        if ($fileSize <= $chunkSize) {
            $chunkSize = intval($fileSize / 2);
        }

        //Start the Upload Session with the file path
        //since the DropboxFile object will be created
        //again using the new chunk size.
		$fileid = $params["fileid"];
		$jobid = $params["jobid"];
		//We create a unique ID only valid within this routine to
		//check whether this routine is still transferring the file
		//or another one has gotten control. This way we make sure
		//that only one single routine will be transfering the file.
		$upload_uid = wfu_create_random_string(8);
		//Store unique ID in params to pass it in reUploadChunked 
		$params["upload_uid"] = $upload_uid;
		//We store upload data that are necessary in order to revive
		//and continue the upload in case that this routine times
		//out.
		wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array(
			"upload_type"     => "chunked",
			"upload_uid"      => $upload_uid,
			"filepath"        => $dropboxFile->getFilePath(),
			"path"            => $path,
			"fileSize"        => $fileSize,
			"chunkSize"       => $chunkSize,
			"params"          => serialize($params),
			"sessionId"       => "",
			"uploaded"        => 0,
			"remaining"       => $fileSize
		));
		return $this->reUploadChunked($dropboxFile, $path, $fileSize, $chunkSize, $params);
    }
	
    public function reUploadChunked($dropboxFile, $path, $fileSize, $chunkSize, $params, $sessionId = "", $uploaded = 0, $remaining = 0)
    {
		$t0 = time();
		$fileid = $params["fileid"];
		$jobid = $params["jobid"];
		$upload_uid = $params["upload_uid"];
		$check = wfu_check_transfer_job($fileid, $jobid, "dropbox");
		$valid = $check["valid"];
		if ( $uploaded == 0 && $valid ) {
			wfu_tf_LOG($dropboxFile->getFileName());
			wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array(
				"last_chunk_time" => time(),
			));
			wfu_set_transfer_progress($fileid, $jobid, "dropbox", 0);
			$sessionId = $this->startUploadSession($dropboxFile->getFilePath(), $chunkSize);
			//We do not know how long has startUploadSession taken. It
			//may have timed out and another routine may have gotten
			//control, so we need to check upload_uid
			$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "dropbox");
			if ( !isset($upload_data["upload_uid"]) || $upload_data["upload_uid"] != $upload_uid ) {
				wfu_tf_LOG("wfu_heartbeat: aborted");
				return false;
			}

			//Uploaded
			$uploaded = $chunkSize;

			//Remaining
			$remaining = $fileSize - $chunkSize;

			//While the remaining bytes are
			//more than the chunk size, append
			//the chunk to the upload session.
			$valid = wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array(
				"last_chunk_time" => time(),
				"sessionId"       => $sessionId,
				"uploaded"        => $uploaded,
				"remaining"       => $remaining
			));
			$progress = round($uploaded / $fileSize * 100);
			wfu_set_transfer_progress($fileid, $jobid, "dropbox", $progress);
			$dif = time() - $t0;
			wfu_tf_LOG("wfu_heartbeat: ".$dif." uploaded: ".$uploaded);
		}
        while ( $remaining > $chunkSize && $valid ) {
             //Append the next chunk to the Upload session
            $sessionId = $this->appendUploadSession($dropboxFile, $sessionId, $uploaded, $chunkSize);
			$upload_data = wfu_get_service_transfer_data($fileid, $jobid, "dropbox");
			if ( !isset($upload_data["upload_uid"]) || $upload_data["upload_uid"] != $upload_uid ) {
				$valid = false;
				break;
			}

            //Update remaining and uploaded
            $uploaded = $uploaded + $chunkSize;
            $remaining = $remaining - $chunkSize;
			$valid = wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array(
				"last_chunk_time" => time(),
				"sessionId"       => $sessionId,
				"uploaded"        => $uploaded,
				"remaining"       => $remaining
			));
			$progress = round($uploaded / $fileSize * 100);
			wfu_set_transfer_progress($fileid, $jobid, "dropbox", $progress);
			$dif = time() - $t0;
			wfu_tf_LOG("wfu_heartbeat: ".$dif." uploaded: ".$uploaded);
       }
		
		if ( !$valid ) {
			wfu_tf_LOG("wfu_heartbeat: aborted");
			return false;
		}
		wfu_tf_LOG("wfu_heartbeat: finished");
		//store full params structure to use them later
		$stored_params = $params;
		//clean params to keep only those necessary for the upload
		unset($params["fileid"]);
		unset($params["jobid"]);
		unset($params["upload_uid"]);
		unset($params["share_file"]);

        //Finish the Upload Session and return the Uploaded File Metadata
		$uploadMetadata = $this->finishUploadSession($dropboxFile, $sessionId, $uploaded, $remaining, $path, $params);
		wfu_store_service_transfer_data($fileid, $jobid, "dropbox", array( "remaining" => 0 ));
 		$filepath = wfu_basedir($dropboxFile->getFilePath()).$uploadMetadata->getName();
		$metadata = wfu_dropbox_post_upload_actions($uploadMetadata, $stored_params, $this, false);
		wfu_set_transfer_result($fileid, $jobid, "dropbox", true, "", $filepath, $metadata);
       return $uploadMetadata;
    }
}