<?php

namespace Tests\InternetOfVoice\VSMS\Core\Helper;

use InternetOfVoice\VSMS\Core\Helper\SkillHelper;

/**
 * SkillHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class SkillHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testHelper() {
        $helper = new SkillHelper();

        $result = $helper->extractAmazonDate('2017-05-10');
        $this->assertEquals('2017-05-10 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-05-11 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        $result = $helper->extractAmazonDate('2017-W01');
        $this->assertEquals('2017-01-02 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-01-09 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

    }
}
