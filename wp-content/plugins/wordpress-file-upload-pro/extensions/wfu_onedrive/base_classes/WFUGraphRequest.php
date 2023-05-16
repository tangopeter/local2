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
 * Class WFUGraphRequest
 *
 * @category Library
 * @package  WFU.OneDrive
 * @license  http://www.gnu.org/licenses/
 * @link     https://www.iptanus.com/
 */
class WFUGraphRequest extends GraphRequest
{
    /**
    * Executes the HTTP request using Guzzle multipart
    *
    * @param mixed $client The client to use in the request
    * @param array $metadata The metadata to pass in the request
    *
    * @throws GraphException if response is invalid
    *
    * @return mixed object or array of objects
    *         of class $returnType
    */
    public function execute_multipart($client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient($this->proxyPort);
        }

		$options = [
			'stream' =>  $this->returnsStream,
			'timeout' => $this->timeout
		];
		$request = new \GuzzleHttp\Psr7\Request($this->requestType, $this->_WFUgetRequestUrl(), $this->getHeaders(), $this->requestBody, '1.1');
		//modify Content-Type to multipart/related as this is the one accepted
		//for multipart uploads
		$modify['set_headers']['Content-Type'] = 'multipart/related; boundary=' . $request->getBody()->getBoundary();
		//correct Host header otherwise Bad Request error is generated
		$modify['set_headers']['Host'] = parse_url($this->baseUrl, PHP_URL_HOST);
		$request = \GuzzleHttp\Psr7\modify_request($request, $modify);
		$result = $client->send($request, $options);
        // Wrap response in GraphResponse layer
        $response = new GraphResponse(
            $this, 
            $result->getBody(), 
            $result->getStatusCode(), 
            $result->getHeaders()
        );

        // If no return type is specified, return GraphResponse
        $returnObj = $response;

        if ($this->returnType) {
            $returnObj = $response->getResponseAsObject($this->returnType);
        }
        return $returnObj; 
    }

    /**
    * Upload a file to OneDrive from a given location using multipart
    *
    * @param string $path   The path of the file to upload
    * @param mixed  $client The client to use in the request
    *
     * @throws GraphException if file is invalid
     *
    * @return mixed DriveItem or array of DriveItems
    */
    public function upload_multipart($path, $client = null, $options)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            if (wfu_file_exists($path, "WFUGraphRequest::upload_multipart") && is_readable($path)) {
				//get file contents in stream
                $file = wfu_fopen($path, "r", "WFUGraphRequest::upload_multipart");
                $stream = \GuzzleHttp\Psr7\stream_for($file);
				//define necessary metadata
				$metadata = array(
					'name' => wfu_basename($path),
					'file' => new \StdClass(),
					'@content.sourceUrl' => 'cid:content',
					'@name.conflictBehavior' => $options['conflict_policy']
				);
				//include userdata in file's description if they exist
				if ( isset($options['userdata']) ) {
					$description = "";
					foreach ( $options["userdata"] as $item )
						$description .= ( $description == "" ? "" : "\n" ).$item["property"].": ".$item["value"]." ";
					$metadata['description'] = $description;
				}
				//define multipart body
				$multipart = [
					[
						'headers' => [
							'Content-ID' => '<metadata>',
							'Content-Type' => 'application/json'
						],
						'name' => 'metadata',
						'contents' => json_encode($metadata)
					],
					[
						'headers' => [
							'Content-ID' => '<content>',
							'Content-Type' => wfu_mime_content_type($path)
						],
						'name' => 'content',
						'contents' => $stream
					]
				];
                $this->requestBody = new \GuzzleHttp\Psr7\MultipartStream($multipart);
                return $this->execute_multipart($client);
            } else {
                throw new GraphException(GraphConstants::INVALID_FILE);
            }
        } catch(GraphException $e) {
            throw new GraphException(GraphConstants::INVALID_FILE);
        }
    }

	/**
    * Get the concatenated request URL
    *
    * @return string request URL
    */
    private function _WFUgetRequestUrl()
    {
        //Send request with opaque URL
        if (stripos($this->endpoint, "http") === 0) {
            return $this->endpoint;
        }

        return $this->apiVersion . $this->endpoint;
    }

}