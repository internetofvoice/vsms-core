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
    public function testAmazonDate() {
        $helper = new SkillHelper();

        // Now
        $result = $helper->extractAmazonDate('PRESENT_REF');
        $this->assertEquals(date('Y-m-d H:i:s'), $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals(false, $result->duration);

        // Date
        $result = $helper->extractAmazonDate('2017-05-10');
        $this->assertEquals('2017-05-10 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-05-11 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Week
        $result = $helper->extractAmazonDate('2017-W01');
        $this->assertEquals('2017-01-02 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-01-09 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Weekend
        $result = $helper->extractAmazonDate('2017-W19-WE');
        $this->assertEquals('2017-05-13 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-05-15 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Month
        $result = $helper->extractAmazonDate('2017-09');
        $this->assertEquals('2017-09-01 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-10-01 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Year
        $result = $helper->extractAmazonDate('2016');
        $this->assertEquals('2016-01-01 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-01-01 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Decade
        $result = $helper->extractAmazonDate('199X');
        $this->assertEquals('1990-01-01 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2000-01-01 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Season
        $result = $helper->extractAmazonDate('1974-SU');
        $this->assertEquals('1974-06-21 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('1974-09-21 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Season (spanning two years)
        $result = $helper->extractAmazonDate('2017-WI');
        $this->assertEquals('2016-12-21 00:00:00', $result->start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-03-21 00:00:00', $result->start->add($result->duration)->format('Y-m-d H:i:s'));

        // Backdating of future month
        $result = $helper->extractAmazonDate((date('Y') + 1) . '-' . date('m'), true);
        $this->assertEquals(date('Y'), $result->start->format('Y'));
    }

    public function testAmazonTime() {
        $helper = new SkillHelper();
        $result = $helper->extractAmazonTime('16:17', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 16:17:00', $result->format('Y-m-d H:i:s'));
        $result = $helper->extractAmazonTime('AF', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 12:00:00', $result->format('Y-m-d H:i:s'));
    }
}
