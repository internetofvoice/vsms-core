<?php

namespace InternetOfVoice\VSMS\Core\Helper;

/**
 * SkillHelper
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
class SkillHelper
{
    /**
     * extractAmazonDate
     *
     * Extracts AMAZON.DATE slot values to DateTime AND DateInterval. Returns a StdClass object with properties
     * 'start' (DateTime object) and 'duration' (DateInterval object).
     *
     * If slot value contains a time span like one year, 'start' will hold the first day at zero time,
     * and 'period' an interval of one year.
     * If a specific date like 2017-05-10 is given, 'start' will be 2017-05-10 at zero time, and 'period' is one day.
     *
     * @param 	string      $amazon_date        AMAZON.DATE slot value
     * @return  \StdClass
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function extractAmazonDate($amazon_date) {
        $start  = false;
        $period = false;

        return (object)[$start, $period];
    }
}
