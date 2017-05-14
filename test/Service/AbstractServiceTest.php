<?php

namespace Tests\InternetOfVoice\VSMS\Core\Service;

use InternetOfVoice\VSMS\Core\Helper\LogHelper;
use InternetOfVoice\VSMS\Core\Helper\SkillHelper;
use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;
use \Tests\InternetOfVoice\VSMS\Core\Fixtures\MockService;

/**
 * LogHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
class LogHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testService
     */
    public function testService() {
        $service = new MockService();
        $service->setLogger(new LogHelper);
        $service->setSkillHelper(new SkillHelper);
        $service->setTranslator(new TranslationHelper([], ''));

        $this->assertObjectHasAttribute('logger', $service);
        $this->assertObjectHasAttribute('skillHelper', $service);
        $this->assertObjectHasAttribute('translator', $service);
    }
}
