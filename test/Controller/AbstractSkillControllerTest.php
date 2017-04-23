<?php

namespace Tests\Controller;

use Slim\Http\Response;
use Tests\Fixtures\MockSkillController;

/**
 * AbstractSkillControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class AbstractSkillControllerTest extends ControllerTestCase
{
    /**
     *Run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Psr\Http\Message\ResponseInterface
     * @access	protected
     * @author	a.schmidt@internet-of-voice.de
     */
    protected function runApp($method, $uri, $headers = [], $data = null) {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $request  = $this->createRequest($method, $uri, $headers, $data);
        $app      = $this->createApp($request, $settings);

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
