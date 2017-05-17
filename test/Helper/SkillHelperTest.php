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
        list($start, $end) = $helper->extractAmazonDate('PRESENT_REF');
        $this->assertEquals(date('Y-m-d H:i:s'), $start->format('Y-m-d H:i:s'));
        $this->assertEquals($start, $end);

        // Date
        list($start, $end) = $helper->extractAmazonDate('2017-05-10');
        $this->assertEquals('2017-05-10 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-05-10 23:59:59', $end->format('Y-m-d H:i:s'));

        // Week
        list($start, $end) = $helper->extractAmazonDate('2017-W01');
        $this->assertEquals('2017-01-02 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-01-08 23:59:59', $end->format('Y-m-d H:i:s'));

        // Weekend
        list($start, $end) = $helper->extractAmazonDate('2017-W19-WE');
        $this->assertEquals('2017-05-13 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-05-14 23:59:59', $end->format('Y-m-d H:i:s'));

        // Month
        list($start, $end) = $helper->extractAmazonDate('2017-09');
        $this->assertEquals('2017-09-01 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-09-30 23:59:59', $end->format('Y-m-d H:i:s'));

        // Year
        list($start, $end) = $helper->extractAmazonDate('2016');
        $this->assertEquals('2016-01-01 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-31 23:59:59', $end->format('Y-m-d H:i:s'));

        // Decade
        list($start, $end) = $helper->extractAmazonDate('199X');
        $this->assertEquals('1990-01-01 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('1999-12-31 23:59:59', $end->format('Y-m-d H:i:s'));

        // Season
        list($start, $end) = $helper->extractAmazonDate('1974-SU');
        $this->assertEquals('1974-06-21 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('1974-09-20 23:59:59', $end->format('Y-m-d H:i:s'));

        // Season (spanning two years)
        list($start, $end) = $helper->extractAmazonDate('2017-WI');
        $this->assertEquals('2016-12-21 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-03-20 23:59:59', $end->format('Y-m-d H:i:s'));

        // Backdating of future month
        list($start, $end) = $helper->extractAmazonDate((date('Y') + 1) . '-' . date('m'), true);
        $this->assertEquals(date('Y'), $start->format('Y'));

        // Fail on unknown formats
        list($start, $end) = $helper->extractAmazonDate('THIS-IS-BS');
        $this->assertEquals(false, $start);
        $this->assertEquals(false, $end);
    }

    public function testAmazonTime() {
        $helper = new SkillHelper();
        $result = $helper->extractAmazonTime('16:17', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 16:17:00', $result->format('Y-m-d H:i:s'));
        $result = $helper->extractAmazonTime('AF', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 12:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function testHumanConversion() {
        $helper = new SkillHelper();
        $translations = [
            'days'   => ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'],
            'months' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            'today'  => 'heute',
            'yesterday'  => 'gestern',
            'prefix_rel' => 'am',
            'prefix_abs' => 'am',
            'at'         => 'um',
            'o\'clock'   => 'Uhr',
        ];

        $result = $helper->convertDateTimeToHuman(new \DateTime('2016-03-02 13:22:00'), ['with_time'], $translations);
        $this->assertEquals('am 2. März 2016 um 13 Uhr 22', $result);
    }
}
