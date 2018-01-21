<?php

namespace Tests\InternetOfVoice\VSMS\Core\Controller;

use \Exception;
use \Slim\Exception\MethodNotAllowedException;
use \Slim\Exception\NotFoundException;
use \Slim\Http\Response;
use \Tests\InternetOfVoice\VSMS\Core\Fixtures\MockSkillController;


/**
 * Class AbstractSkillControllerTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class AbstractSkillControllerTest extends ControllerTestCase {
    /**
     *Run application
     *
     * @param   string              $method     request method
     * @param   string              $uri        request URI
     * @param   array|null          $headers    additional request headers
     * @param   array|object|null   $data       request data
     * @return  \Psr\Http\Message\ResponseInterface
     * @access	protected
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     * @author	a.schmidt@internet-of-voice.de
     */
    protected function runApp($method, $uri, $headers = [], $data = null) {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $request  = $this->createRequest($method, $uri, $headers, $data);
        $app      = $this->createApp($request, $settings);

        $app->map([$method], $uri, MockSkillController::class);

        return $app->process($request, new Response());
    }

    /**
     * testLaunchRequest
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     */
    public function testLaunchRequest() {
	    $body     = file_get_contents(__DIR__ . '/../Fixtures/TestLaunchRequest-Body.txt');
	    $header   = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestLaunchRequest-Header.json'), true);
        $response = $this->runApp('POST', '/skill/test', $header, $body);
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
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     */
    public function testHelpIntent() {
        $body     = file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequest-Body.txt');
        $header   = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequest-Header.json'), true);
        $response = $this->runApp('POST', '/skill/test', $header, $body);

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

	/**
	 * testSessionEndedRequest
	 * @throws  Exception
	 * @throws  MethodNotAllowedException
	 * @throws  NotFoundException
	 */
	public function testSessionEndedRequest() {
		$body     = trim(file_get_contents(__DIR__ . '/../Fixtures/TestSessionEndedRequest-Body.txt'));
		$header   = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSessionEndedRequest-Header.json'), true);
		$response = $this->runApp('POST', '/skill/test', $header, $body);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(
			'{"version":"1.0","sessionAttributes":[],"response":{"shouldEndSession":null}}',
			strval($response->getBody())
		);
	}

    /**
     * testUnknownIntent
     * @throws  Exception
     * @throws  MethodNotAllowedException
     * @throws  NotFoundException
     */
    public function testUnknownIntent() {
        $body = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequest-Body.txt'), true);
        $body['request']['intent']['name'] = 'NonExistentIntent';

        $header   = json_decode(file_get_contents(__DIR__ . '/../Fixtures/TestSkillRequest-Header.json'), true);
        $response = $this->runApp('POST', '/skill/test', $header, json_encode($body));
        $this->assertEquals(400, $response->getStatusCode());
    }
}
