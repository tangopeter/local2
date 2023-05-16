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
* WFUFacebookCurlHttpClient File
* PHP version 5.6.3
*
* @category  Library
* @package   WFU.Facebook
* @copyright 2018 Nickolas Bossinas
* @license   http://www.gnu.org/licenses/
* @version   0.1.0
* @link      https://www.iptanus.com/
*/

namespace WFU;

use Facebook\HttpClients\FacebookCurlHttpClient;

/**
 * Class FacebookCurlHttpClient
 *
 * @package Facebook
 */
class WFUFacebookCurlHttpClient extends FacebookCurlHttpClient
{
    /**
     * Opens a new curl connection. Function extended to include support for
	 * proxy, if defined in Wordpress wp-config.php file.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     */
    public function openConnection($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeOut,
            CURLOPT_RETURNTRANSFER => true, // Return response as string
            CURLOPT_HEADER => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => ABSWPFILEUPLOAD_DIR . WFU_MODULES_PHP50600 . 'facebook/graph-sdk/src/Facebook/HttpClients/certs/DigiCertHighAssuranceEVRootCA.pem',
        ];

        if ($method !== "GET") {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
		//check proxy
		$proxy = new \WP_HTTP_Proxy();
		//configure cURL request for proxy
		if ( $proxy->is_enabled() && $proxy->send_through_proxy($url) ) {
			$options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
			$options[CURLOPT_PROXY] = $proxy->host().":".$proxy->port();
			if ( $proxy->use_authentication() ) {
				$options[CURLOPT_PROXYAUTH] = CURLAUTH_ANY;
				$options[CURLOPT_PROXYUSERPWD] = $proxy->authentication();
			}
		}

        $this->facebookCurl->init();
        $this->facebookCurl->setoptArray($options);
    }
}