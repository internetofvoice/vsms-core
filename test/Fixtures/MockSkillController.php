<?php

namespace Tests\InternetOfVoice\VSMS\Core\Fixtures;

use InternetOfVoice\VSMS\Core\Controller\AbstractSkillController;

/**
 * MockSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
final class MockSkillController extends AbstractSkillController
{
    /** @var array $askApplicationIds */
    protected $askApplicationIds = [
        'dev-test' => 'amzn1.ask.skill.b5ec8cfa-d9e5-40c9-8325-c56927a2e42b',
    ];

    /** @var array $messages */
    protected $messages = [
        'default' => 'I am afraid I did not understand you.',
        'welcome' => 'Welcome to Test Skill.',
        'clue'    => 'If you need help, please say help.',
        'help'    => 'This is the help text.',
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
    public function invoke($request, $response, $args) {
        $this->createAlexaRequest($request);

        return $this->dispatchAlexaRequest($response);
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
     * Session-ended request
     *
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function sessionEnded() {
    }
}
