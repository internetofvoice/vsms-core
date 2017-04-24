<?php

namespace Tests\InternetOfVoice\VSMS\Core\Controller;

use Slim\Http\Response;
use Tests\InternetOfVoice\VSMS\Core\Fixtures\MockLinkController;

/**
 * AbstractLinkControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
class AbstractLinkControllerTest extends ControllerTestCase
{
    /**
     * Run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Psr\Http\Message\ResponseInterface
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function runApp($method, $uri, $headers = [], $data = null) {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $request  = $this->createRequest($method, $uri, $headers, $data);
        $app      = $this->createApp($request, $settings);

        $app->get('/get-parameters', MockLinkController::class . ':getParameters');
        $app->post('/get-location', MockLinkController::class . ':getLocation');

        return $app->process($request, new Response());
    }

    /**
     * Test abstract link controller
     *
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function testAbstractLinkController() {
        $fixture  = file_get_contents(__DIR__ . '/../Fixtures/TestLinkRequest.json');
        $response = $this->runApp('GET', '/get-parameters', [], $fixture);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('current-state', (string)$response->getBody());

        $response = $this->runApp('POST', '/get-location', [], $fixture);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('account-linking-status.html', (string)$response->getBody());

        $response = $this->runApp('GET', '/non-existent');
        $this->assertEquals(404, $response->getStatusCode());
    }
}
