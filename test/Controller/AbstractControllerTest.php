<?php

namespace Tests\InternetOfVoice\VSMS\Core\Controller;

use \Exception;
use \Slim\Exception\MethodNotAllowedException;
use \Slim\Exception\NotFoundException;
use \Slim\Http\Response;
use \Tests\InternetOfVoice\VSMS\Core\Fixtures\MockController;

/**
 * Class AbstractControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class AbstractControllerTest extends ControllerTestCase {
    /**
     * Run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Psr\Http\Message\ResponseInterface
     * @access  public
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     * @author  a.schmidt@internet-of-voice.de
     */
    public function runApp($method, $uri, $headers = [], $data = null) {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $request  = $this->createRequest($method, $uri, $headers, $data);
        $app      = $this->createApp($request, $settings);

        $app->get('/get-language', MockController::class . ':getLanguage');
        $app->get('/get-locale', MockController::class . ':getLocale');

        return $app->process($request, new Response());
    }

    /**
     * Test abstract controller
     *
     * @access  public
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     * @author  a.schmidt@internet-of-voice.de
     */
    public function testAbstractController() {
        $response = $this->runApp('GET', '/get-language', ['HTTP_ACCEPT_LANGUAGE' => 'de-DE,en;q=0.5']);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('de', (string)$response->getBody());

        $response = $this->runApp('GET', '/get-locale');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('en-US', (string)$response->getBody());

        $response = $this->runApp('POST', '/get-locale');
        $this->assertEquals(405, $response->getStatusCode());

        $response = $this->runApp('GET', '/non-existent');
        $this->assertEquals(404, $response->getStatusCode());
    }
}
