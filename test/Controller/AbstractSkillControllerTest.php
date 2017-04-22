<?php

namespace Tests\Controller;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;
use Tests\Fixtures\MockSkillController;

/**
 * AbstractSkillControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class AbstractSkillControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock and run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Psr\Http\Message\ResponseInterface
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function runApp($method, $uri, $headers = [], $data = null) {
        $headers = array_merge([
            'REQUEST_METHOD'             => $method,
            'REQUEST_URI'                => $uri,
            'CONTENT_TYPE'               => 'application/json',
            'HTTP_SIGNATURE'             => '',
            'HTTP_SIGNATURECERTCHAINURL' => '',
        ], $headers);

        // Fake _SERVER array as expected by vendor library
        $_SERVER['HTTP_SIGNATURE']             = $headers['HTTP_SIGNATURE'];
        $_SERVER['HTTP_SIGNATURECERTCHAINURL'] = $headers['HTTP_SIGNATURECERTCHAINURL'];

        // Create prerequisites
        $environment     = Environment::mock($headers);
        $request_uri     = Uri::createFromString('http://example.com' . $uri); // example.com is irrelevant, we just want a route
        $request_headers = Headers::createFromEnvironment($environment);
        $cookies         = [];
        $serverParams    = $environment->all();

        // Create body with request data
        $body = new RequestBody();
        $body->write($data);
        $body->rewind();

        // Mock request
        $request = new Request($method, $request_uri, $request_headers, $cookies, $serverParams, $body);

        // Run app and return response
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $app      = new App($settings);

        $app->map([$method], $uri, MockSkillController::class . ':invoke');

        return $app->process($request, new Response());
    }

    /**
     * testLaunchRequest
     */
    public function testLaunchRequest() {
        $headerStub = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequestHeader.json'), true);
        $bodyStub = file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequestBody-Launch.json');

        $response = $this->runApp('POST', '/skill/test', $headerStub, $bodyStub);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('"outputSpeech"', strval($response->getBody()));

        $json_body = json_decode(strval($response->getBody()), true);
        $this->assertArrayHasKey('response', $json_body);
        $this->assertArrayHasKey('outputSpeech', $json_body['response']);
        $this->assertArrayHasKey('text', $json_body['response']['outputSpeech']);
        $this->assertEquals('Welcome to Test Skill.', $json_body['response']['outputSpeech']['text']);

        $this->assertArrayHasKey('reprompt', $json_body['response']);
        $this->assertArrayHasKey('outputSpeech', $json_body['response']['reprompt']);
        $this->assertArrayHasKey('text', $json_body['response']['reprompt']['outputSpeech']);
        $this->assertEquals('If you need help, please say help.', $json_body['response']['reprompt']['outputSpeech']['text']);
    }

    /**
     * testHelpIntent
     */
    public function testHelpIntent() {
        $headerStub = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequestHeader.json'), true);
        $bodyStub = file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequestBody-Help.json');

        $response = $this->runApp('POST', '/skill/test', $headerStub, $bodyStub);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('"outputSpeech"', strval($response->getBody()));

        $json_body = json_decode(strval($response->getBody()), true);
        $this->assertArrayHasKey('response', $json_body);
        $this->assertArrayHasKey('outputSpeech', $json_body['response']);
        $this->assertArrayHasKey('text', $json_body['response']['outputSpeech']);
        $this->assertEquals('This is the help text.', $json_body['response']['outputSpeech']['text']);

        $this->assertArrayHasKey('card', $json_body['response']);
        $this->assertArrayHasKey('type', $json_body['response']['card']);
        $this->assertArrayHasKey('title', $json_body['response']['card']);
        $this->assertEquals('Simple', $json_body['response']['card']['type']);
        $this->assertEquals('Help', $json_body['response']['card']['title']);
        $this->assertEquals('This is the help text.', $json_body['response']['card']['content']);
    }
}
