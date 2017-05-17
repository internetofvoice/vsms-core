<?php

namespace Tests\InternetOfVoice\VSMS\Core\Helper;

use Analog\Handler\LevelName;
use Analog\Handler\Variable;
use InternetOfVoice\VSMS\Core\Helper\LogHelper;
use Tests\InternetOfVoice\VSMS\Core\Controller\ControllerTestCase;

/**
 * LogHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
class LogHelperTest extends ControllerTestCase
{
    /**
     * testLogger
     */
    public function testLogger() {
        $log    = '';
        $logger = new LogHelper;
        $logger->format('%s - %s - %s - %s' . PHP_EOL);
        $logger->handler(LevelName::init(Variable::init($log)));

        $logger->info('A test log entry.');
        $this->assertContains('INFO', $log);
        $this->assertContains('A test log entry.', $log);
    }

    public function testRequestLogger() {
        $request = $this->createRequest('POST', '/test/url', [], json_encode(['key' => 'value', 'key2' => 'value2']));
        $log     = '';
        $logger  = new LogHelper;
        $logger->format('%s - %s - %s - %s' . PHP_EOL);
        $logger->handler(LevelName::init(Variable::init($log)));
        $logger->setMask(['key']);
        $logger->logRequest($request, ['extra-key' => 'extra-value']);

        $this->assertContains('DEBUG - POST LogHelperTest::testRequestLogger {"extra-key":"extra-value"}', $log);
        $this->assertContains('DEBUG - Header', $log);
        $this->assertContains('DEBUG - Body: {"key":"***","key2":"value2"}', $log);
    }
}
