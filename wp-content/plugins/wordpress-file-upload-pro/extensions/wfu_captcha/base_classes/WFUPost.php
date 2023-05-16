<?php
/**
 * WFU redeclaration of ReCaptcha\RequestMethod\Post class
 */

namespace ReCaptcha\RequestMethod;

use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

/**
 * Sends POST requests to the reCAPTCHA service.
 */
class WFUPost implements RequestMethod
{
    /**
     * URL to which requests are POSTed.
     * @const string
     */
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Submit the POST request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        /**
         * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
         * Using "CN_name" will still work, but it will raise deprecated errors.
         */
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $params->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peer_key => 'www.google.com',
            ),
        );
        $context = stream_context_create($options);
		//WFU modification - verify URL is defined from environment variable
        //return file_get_contents(self::SITE_VERIFY_URL, false, $context);
		$site_verify_url = 'https://'.WFU_VAR("WFU_RECAPTCHAV2_HOST").'/recaptcha/api/siteverify';
        return file_get_contents($site_verify_url, false, $context);
    }
}