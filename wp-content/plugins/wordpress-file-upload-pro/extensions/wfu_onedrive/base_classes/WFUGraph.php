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
* WFUGraph File
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

use Microsoft\Graph\Graph;
use Microsoft\Graph\Core\GraphConstants;

/**
 * Class WFUGraph
 *
 * @category Library
 * @package  WFU.OneDrive
 * @license  http://www.gnu.org/licenses/
 * @link     https://www.iptanus.com/
 */
class WFUGraph extends Graph
{
    /**
    * The access_token provided after authenticating
    * with Microsoft Graph (required)
    *
    * @var string
    */
    private $_WFUaccessToken;

    /**
    * Sets the access token. A valid access token is required
    * to run queries against Graph
    *
    * @param string $accessToken The user's access token, retrieved from 
    *                     MS auth
    *
    * @return Graph object
    */
    public function setAccessToken($accessToken)
    {
        $this->_WFUaccessToken = $accessToken;
        return $this;
    }

    /**
    * Creates a new request object with the given Graph information
    *
    * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
    * @param string $endpoint    The Graph endpoint to call
    *
    * @return WFUGraphRequest The request object, which can be used to 
    *                      make queries against Graph
    */
    public function createRequest($requestType, $endpoint)
    {
        return new WFUGraphRequest(
            $requestType, 
            $endpoint, 
            $this->_WFUaccessToken,
			//WFUGraph uses api.onedrive.com endpoint and not
			//graph.microsoft.com because the later does not support multipart
			//uploads yet
            ( WFU_VAR("WFU_ONEDRIVE_REST_ENDPOINT") == "OneDrive" ? WFU_ONEDRIVE_REST_ENDPOINT_OENDP : WFU_ONEDRIVE_REST_ENDPOINT_GENDP ), 
            GraphConstants::API_VERSION
        );
    }
}