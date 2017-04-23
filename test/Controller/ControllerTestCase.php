<?php

namespace Tests\InternetOfVoice\VSMS\Core\Controller;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Uri;

/**
 * ControllerTestCase
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class ControllerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock request
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Slim\Http\Request
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function createRequest($method, $uri, $headers = [], $data = null) {
        $headers = array_merge([
            'REQUEST_METHOD'             => $method,
            'REQUEST_URI'                => $uri,
            'CONTENT_TYPE'               => 'application/json',
            'HTTP_SIGNATURE'             => '',
            'HTTP_SIGNATURECERTCHAINURL' => '',
        ], $headers);

        // Fake $_SERVER array as expected by vendor library
        $_SERVER['HTTP_SIGNATURE']             = $headers['HTTP_SIGNATURE'];
        $_SERVER['HTTP_SIGNATURECERTCHAINURL'] = $headers['HTTP_SIGNATURECERTCHAINURL'];

        // Create prerequisites
        $environment     = Environment::mock($headers);
        $request_uri     = Uri::createFromString('http://example.com' . $uri); // ignore example.com, just create URI
        $request_headers = Headers::createFromEnvironment($environment);
        $cookies         = [];
        $serverParams    = $environment->all();

        // Create body, optional with request data
        $body = new RequestBody();
        if(isset($data)) {
            $body->write($data);
            $body->rewind();
        }

        return new Request($method, $request_uri, $request_headers, $cookies, $serverParams, $body);
    }

    /**
     * Mock application
     *
     * @param  \Slim\Http\Request   $request    Mocked request
     * @param  string               $settings   Mocked settings
     * @return \Slim\App
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function createApp($request, $settings) {
        $app = new App($settings);
        $container = $app->getContainer();
        $container['request'] = $request;

        return $app;
    }
}
