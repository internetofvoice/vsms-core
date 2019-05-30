<?php

namespace Tests\InternetOfVoice\VSMS\Core\Service;

use InternetOfVoice\VSMS\Core\Helper\LogHelper;
use InternetOfVoice\VSMS\Core\Helper\SkillHelper;
use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;
use PHPUnit\Framework\TestCase;
use Tests\InternetOfVoice\VSMS\Core\Fixtures\MockService;

/**
 * Class LogHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class LogHelperTest extends TestCase {
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
