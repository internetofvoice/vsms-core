<?php

namespace Tests\InternetOfVoice\VSMS\Core\Controller;

use Analog\Handler\Ignore;
use InternetOfVoice\VSMS\Core\Helper\LogHelper;
use InternetOfVoice\VSMS\Core\Helper\SkillHelper;
use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Uri;

/**
 * Class ControllerTestCase
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class ControllerTestCase extends TestCase {
    /**
     * Mock request
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     *
     * @return  Request
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

        // Mock $_SERVER pre-requisites
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
     * @param  Request $request  Mocked request
     * @param  string $settings Mocked settings
     *
     * @return App
     * @access public
     * @author a.schmidt@internet-of-voice.de
     */
    public function createApp($request, $settings) {
        $app = new App($settings);
        $container = $app->getContainer();
        $container['request'] = $request;

        $container['translator'] = function(Container $c) {
            $settings = $c->get('settings');
            return new TranslationHelper(
                $settings['locales'],
                $settings['locale_default']
            );
        };

        $container['logger'] = function() {
            $logger = new LogHelper();
            $logger->handler(Ignore::init());
            return $logger;
        };

        $container['skillHelper'] = function() {
            return new SkillHelper();
        };

        return $app;
    }
}
