<?php

namespace Tests\InternetOfVoice\VSMS\Core\Helper;

use InternetOfVoice\VSMS\Core\Helper\SkillHelper;

/**
 * Class SkillHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */

class SkillHelperTest extends \PHPUnit_Framework_TestCase {
	/**
	 * testGetDateFromSeason
	 */
    public function testGetDateFromSeason() {
        $helper = new SkillHelper();
        $result = $helper->getDateFromSeason(2017, 'spring', 'northern');
        $this->assertEquals('2017-03-21', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'summer', 'northern');
	    $this->assertEquals('2017-06-21', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'fall', 'northern');
	    $this->assertEquals('2017-09-23', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'winter', 'northern');
	    $this->assertEquals('2017-12-21', $result->format('Y-m-d'));


	    $result = $helper->getDateFromSeason(2016, 'spring', 'southern');
        $this->assertEquals('2016-09-23', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2016, 'summer', 'southern', false);
	    $this->assertEquals('2015-12-21', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2016, 'fall', 'southern');
	    $this->assertEquals('2016-03-21', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2016, 'winter', 'southern');
	    $this->assertEquals('2016-06-21', $result->format('Y-m-d'));


	    $result = $helper->getDateFromSeason(2017, 'fall', 'australia');
	    $this->assertEquals('2017-03-01', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'winter', 'australia');
	    $this->assertEquals('2017-06-01', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'spring', 'australia');
	    $this->assertEquals('2017-09-01', $result->format('Y-m-d'));

	    $result = $helper->getDateFromSeason(2017, 'summer', 'australia');
	    $this->assertEquals('2017-12-01', $result->format('Y-m-d'));
    }

	/**
	 * testExtractAmazonDate
	 */
    public function testExtractAmazonDate() {
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
        list($start, $end) = $helper->extractAmazonDate('2016-XX-XX');
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
        list($start) = $helper->extractAmazonDate((date('Y') + 1) . '-' . date('m'), true);
        $this->assertEquals(date('Y'), $start->format('Y'));

		// Backdating of future day
	    list($start) = $helper->extractAmazonDate(date('Y') . '-' . date('m') . '-' . (date('d') + 7), true);
	    $this->assertEquals(date('Y-m-d'), $start->format('Y-m-d'));

	    // Fail on unknown formats
        list($start, $end) = $helper->extractAmazonDate('THIS-IS-BS');
        $this->assertEquals(false, $start);
        $this->assertEquals(false, $end);
    }

	/**
	 * testExtractAmazonTime
	 */
    public function testExtractAmazonTime() {
        $helper = new SkillHelper();
        $result = $helper->extractAmazonTime('16:17', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 16:17:00', $result->format('Y-m-d H:i:s'));

	    $result = $helper->extractAmazonTime('NI', new \DateTime('2017-05-14'));
	    $this->assertEquals('2017-05-14 00:00:00', $result->format('Y-m-d H:i:s'));

	    $result = $helper->extractAmazonTime('MO', new \DateTime('2017-05-14'));
	    $this->assertEquals('2017-05-14 06:00:00', $result->format('Y-m-d H:i:s'));

	    $result = $helper->extractAmazonTime('AF', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 12:00:00', $result->format('Y-m-d H:i:s'));

        $result = $helper->extractAmazonTime('EV', new \DateTime('2017-05-14'));
        $this->assertEquals('2017-05-14 18:00:00', $result->format('Y-m-d H:i:s'));

        $result = $helper->extractAmazonTime(false);
        $this->assertEquals(date('Y-m-d H:i'), $result->format('Y-m-d H:i'));
    }

	/**
	 * testConvertDateTimeToHuman
	 */
    public function testConvertDateTimeToHuman() {
        $helper = new SkillHelper();
        $translations = [
            'days'   => ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'],
            'months' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            'today'  => 'heute',
            'yesterday'  => 'gestern',
            'prefix_48h' => '',
            'prefix_rel' => 'am',
            'prefix_abs' => 'am',
            'at'         => 'um',
            'o\'clock'   => 'Uhr',
        ];

	    $result = $helper->convertDateTimeToHuman(new \DateTime('2016-03-02 13:22:00'), ['with_time'], $translations);
        $this->assertEquals('am 2. März 2016 um 13 Uhr 22', $result);

	    $result = $helper->convertDateTimeToHuman(new \DateTime(), ['relative'], $translations);
	    $this->assertEquals('heute', $result);

	    $result = $helper->convertDateTimeToHuman(new \DateTime('yesterday'), ['relative'], $translations);
	    $this->assertEquals('gestern', $result);

		// Test "last <day>" - does not work if <day> is today or yesterday, so ensure another day.
	    if(date('N') > 2) {
            $result = $helper->convertDateTimeToHuman(new \DateTime('last monday'), ['relative'], $translations);
            $this->assertEquals('am Montag', $result);
	    } else {
            $result = $helper->convertDateTimeToHuman(new \DateTime('last friday'), ['relative'], $translations);
            $this->assertEquals('am Freitag', $result);
	    }

    }

	/**
	 * testConvertListToHuman
	 */
    public function testConvertListToHuman() {
        $helper = new SkillHelper();
        $result = $helper->convertListToHuman(['one', 'two', 'three'], 'or');
        $this->assertEquals('one, two or three', $result);
    }
}
