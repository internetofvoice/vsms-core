<?php

namespace Tests\InternetOfVoice\VSMS\Core\Service;

use InternetOfVoice\VSMS\Core\Helper\LogHelper;
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
        $service = new MockService(new LogHelper);

        $this->assertObjectHasAttribute('logger', $service);
        $this->assertObjectHasAttribute('mask', $service->getLogger());
    }
}
