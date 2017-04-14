<?php

namespace Tests\Controller;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

    /**
 * AbstractControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock and run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|object|null   $data       request data
     * @param   array|null          $headers    additional request headers
     * @return  \Psr\Http\Message\ResponseInterface
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function runApp($method, $uri, $data = null, $headers = [])
    {
        $headers = array_merge([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri
        ], $headers);

        $environment = Environment::mock($headers);
        $request = Request::createFromEnvironment($environment);
        if (isset($data)) {
            $request = $request->withParsedBody($data);
        }

        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $app = new App($settings);
        $app->get('/get-language', \Tests\Fixtures\MockController::class . ':getLanguage');
        $app->get('/get-locale', \Tests\Fixtures\MockController::class . ':getLocale');

        return $app->process($request, new Response());
    }

    /**
     * Test abstract controller
     *
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function testAbstractController()
    {
        $response = $this->runApp('GET', '/get-language');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('en', (string)$response->getBody());

        $response = $this->runApp('GET', '/get-locale');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('en-US', (string)$response->getBody());
    }
}
