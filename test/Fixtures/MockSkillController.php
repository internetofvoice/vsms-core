<?php

namespace Tests\InternetOfVoice\VSMS\Core\Fixtures;

use InternetOfVoice\VSMS\Core\Controller\AbstractSkillController;

/**
 * Class MockSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class MockSkillController extends AbstractSkillController {
    /** @var array $askApplicationIds */
    protected $askApplicationIds = [
        'dev-test1' => 'amzn1.ask.skill.ef9d2df5-6c65-47b5-9f74-3e6a064c45e6',
        'dev-test2' => 'amzn1.ask.skill.e5427198-b2de-4f89-ac18-b54a4877927f',
    ];

    /** @var array $messages */
    protected $messages = [
        'default' => 'I am afraid I did not understand you.',
        'welcome' => 'Welcome to Test Skill.',
        'clue'    => 'If you need help, please say help.',
        'help'    => 'This is the help text.',
        'stop'    => 'Good bye.',
    ];

    /** @var array $cars */
    protected $cards = [
        'help' => [
            'title'   => 'Help',
            'content' => 'This is the help text.',
        ]
    ];


    /**
     * Invocation method
     *
     * @param   \Slim\Http\Request      $request    Slim request
     * @param   \Slim\Http\Response     $response   Slim response
     * @param   array                   $args       Arguments
     * @return  \Slim\Http\Response
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     * @see     routing configuration
     */
    public function __invoke($request, $response, $args) {
		try {
			$this->createAlexaRequest($request);

			$reply = $this->dispatchAlexaRequest($response);
		} catch(\Exception $e) {
			return $response->withJson(['error' => $e->getMessage()], 400);
		}

		return $reply;
    }


    /**
     * Launch request
     *
     * @access    protected
     * @author    a.schmidt@internet-of-voice.de
     */
    protected function launch() {
        $this->alexaResponse
            ->respond($this->messages['welcome'])
            ->reprompt($this->messages['clue'])
        ;
    }

    /**
     * AMAZON.HelpIntent request
     *
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function intentAMAZONHelpIntent() {
        $this->alexaResponse
            ->respond($this->messages['help'])
            ->withCard($this->cards['help']['title'], $this->cards['help']['content'])
        ;
    }

    /**
     * AMAZON.StopIntent request
     *
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function intentAMAZONStopIntent() {
        $this->alexaResponse
            ->respond($this->messages['stop'])
            ->endSession(true);
        ;
    }

    /**
     * AMAZON.CancelIntent request
     *
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function intentAMAZONCancelIntent() {
        $this->intentAMAZONStopIntent();
    }

    /**
     * Session-ended request
     *
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function sessionEnded() {
    }
}
