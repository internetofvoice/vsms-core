<?php

namespace Tests\Fixtures;

use InternetOfVoice\VSMS\Core\Controller\AbstractSkillController;

/**
 * MockSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
class MockSkillController extends AbstractSkillController
{
    protected $skillHandle = 'example';
    protected $askApplicationId = [
        'dev'   => 'amzn1.ask.skill.b5ec8cfa-d9e5-40c9-8325-c56927a2e42b',
        'test'  => 'amzn1.ask.skill.b5ec8cfa-d9e5-40c9-8325-c56927a2e42b',
        'stage' => 'amzn1.ask.skill.b5ec8cfa-d9e5-40c9-8325-c56927a2e42b',
        'prod'  => 'amzn1.ask.skill.b5ec8cfa-d9e5-40c9-8325-c56927a2e42b',
    ];

    protected $messages = [
        'default' => 'I am afraid I did not understand you.',
        'welcome' => 'Welcome to Test Skill.',
        'clue'    => 'If you need help, please say help.',
        'help'    => 'This is the help text.',
    ];

    protected $cards = [
        'help' => [
            'title'   => 'Help',
            'content' => 'This is the help text.',
        ]
    ];


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
