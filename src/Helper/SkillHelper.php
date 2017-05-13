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
     * Duration will be false, if date refers to now.
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
        $start    = false;
        $duration = false;
        $origin   = false;

        switch(true) {
            // Now
            case ($amazon_date == 'PRESENT_REF'):
                $origin   = 'now';
                $start    = new \DateTime();
                $duration = false;
            break;

            // Date
            case preg_match('~^[\d]{4}-[\d]{2}-[\d]{2}$~', $amazon_date):
                $origin = 'date';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date);
                $start->setTime(0, 0, 0);
                $duration = new \DateInterval('P1D');
            break;

            // Week
            case preg_match('~^([\d]{4})-W([\d]{2})$~', $amazon_date, $matches):
                $origin = 'week';
                $start  = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $duration = new \DateInterval('P1W');
            break;

            // Weekend
            case preg_match('~^([\d]{4})-W([\d]{2})-WE$~', $amazon_date, $matches):
                $origin = 'weekend';
                $start  = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $start->modify('+5 days');
                $duration = new \DateInterval('P2D');
            break;

            // Month
            case preg_match('~^[\d]{4}-[\d]{2}$~', $amazon_date):
                $origin = 'month';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date . '-01');
                $start->setTime(0, 0, 0);
                $duration = new \DateInterval('P1M');
            break;

            // Year
            case preg_match('~^[\d]{4}$~', $amazon_date):
                $origin = 'year';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date . '-01-01');
                $start->setTime(0, 0, 0);
                $duration = new \DateInterval('P1Y');
            break;

            // Decade
            case preg_match('~^([\d]{3})X$~', $amazon_date, $matches):
                $origin = 'decade';
                $start  = \DateTime::createFromFormat('Y-m-d', $matches[1] . '0-01-01');
                $start->setTime(0, 0, 0);
                $duration = new \DateInterval('P10Y');
            break;

            // Season
            case preg_match('~^([\d]{4})-(SP|SU|FA|WI)$~', $amazon_date, $matches):
                $seasons  = ['SP' => 'spring', 'SU' => 'summer', 'FA' => 'fall', 'WI' => 'winter'];
                $origin   = 'season';
                $start    = $this->getDateFromSeason(intval($matches[1]), $seasons[$matches[2]], 'northern', false);
                $duration = new \DateInterval('P3M');
            break;
        }

        if($date_back && $origin) {
            // if(in_future)
        }

        return (object)['start' => $start, 'duration' => $duration];
    }

    /**
     * getDateFromSeason
     *
     * Helper function to get a (start) date for a given year and season.
     *
     * Dates are not always accurate for northern and southern hemispheres, as
     * solstices/equinoxes may vary by -1/+1 day.
     *
     * Seasons spanning two years (winter in northern and summer in southern
     * hemisphere) might point to either current or last year. Current year
     * is enforced by parameter $force_year, else the seasons point to last year.
     *
     * @param   int             $year                   Year
     * @param   string          $season                 Season {spring, summer, fall, winter}
     * @param   string          $hemisphere             Hemisphere {northern, southern, australia}
     * @param   bool            $force_year             Enforce given year?
     * @return  false|\DateTime
     */
    protected function getDateFromSeason($year, $season, $hemisphere, $force_year = true) {
        $date   = false;
        $offset = $force_year ? 0 : 1;

        switch($hemisphere) {
            case 'northern':
                switch($season) {
                    case 'spring':
                        $date = new \DateTime($year . '-03-21');
                    break;

                    case 'summer':
                        $date = new \DateTime($year . '-06-21');
                    break;

                    case 'fall':
                        $date = new \DateTime($year . '-09-23');
                    break;

                    case 'winter':
                        $date = new \DateTime(($year - $offset) . '-12-21');
                    break;
                }
            break;

            case 'southern':
                switch($season) {
                    case 'fall':
                        $date = new \DateTime($year . '-03-21');
                    break;

                    case 'winter':
                        $date = new \DateTime($year . '-06-21');
                    break;

                    case 'spring':
                        $date = new \DateTime($year . '-09-23');
                    break;

                    case 'summer':
                        $date = new \DateTime(($year - $offset) . '-12-21');
                    break;
                }
            break;

            case 'australia':
                switch($season) {
                    case 'fall':
                        $date = new \DateTime($year . '-03-01');
                    break;

                    case 'winter':
                        $date = new \DateTime($year . '-06-01');
                    break;

                    case 'spring':
                        $date = new \DateTime($year . '-09-01');
                    break;

                    case 'summer':
                        $date = new \DateTime(($year - $offset) . '-12-01');
                    break;
                }
            break;
        }

        if($date instanceof \DateTime) {
            $date->setTime(0, 0, 0);
        }

        return $date;
    }
}
