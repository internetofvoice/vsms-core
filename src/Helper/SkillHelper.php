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
     * Extracts an AMAZON.DATE slot value to a DateTime and a DateInterval.
     * Returns a StdClass object with properties 'start' (DateTime object) and 'duration' (DateInterval object).
     * If given date matches no meaningful date, both properties will be returned as boolean false.
     *
     * If a time span is given (like 2017), start will be 2017-01-01 00:00:00, and duration an interval of one year.
     *
     * For a specific date (like 2017-05-10), start will be 2017-05-10 00:00:00, and duration an interval of one day.
     *
     * Unspecific dates like "May" are always sent as future dates (next May) by Amazon.
     * You may use the optional $date_back parameter to enforce past dates (last May).
     * If we are right now in May, no backdating is performed.
     * Caution: please consider that a user might have actually referred to a future date.
     *
     * @param 	string      $amazon_date        AMAZON.DATE slot value
     * @param   bool        $date_back          Enforce a date in the past?
     * @return  \StdClass
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function extractAmazonDate($amazon_date, $date_back = false) {
        $start     = false;
        $duration  = false;
        $amendment = false;

        switch(true) {
            // Date
            case preg_match('~^[\d]{4}-[\d]{2}-[\d]{2}$~', $amazon_date):
                $start = \DateTime::createFromFormat('Y-m-d', $amazon_date);
                $start->setTime(0, 0, 0);
                $duration = new \DateInterval('P1D');
            break;

            // Week
            case preg_match('~^([\d]{4})-W([\d]{2})$~', $amazon_date, $matches):
                $start = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $duration = new \DateInterval('P1W');
            break;

            // Weekend
            case preg_match('~^([\d]{4})-W([\d]{2})-WE$~', $amazon_date, $matches):
                $start = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $start->modify('+6 days');
                $duration = new \DateInterval('P1W');
            break;
        }

        if($date_back && $amendment) {
            // if(in_future)
        }

        return (object)['start' => $start, 'duration' => $duration];
    }
}
