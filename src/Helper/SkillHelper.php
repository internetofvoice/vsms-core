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
     * Based on code by http://biostall.com/get-the-current-season-using-php/
     *
     * @param   int             $year                   Year
     * @param   string          $season                 Season {spring, summer, fall, winter}
     * @param   string          $hemisphere             Hemisphere {northern, southern, australia}
     * @param   bool            $force_year             Enforce given year?
     * @return  false|\DateTime
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function getDateFromSeason($year, $season, $hemisphere, $force_year = true) {
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

    /**
     * extractAmazonDate
     *
     * Extracts an AMAZON.DATE slot value to two DateTime objects 'start' and 'end', returned as [start, end]
     * If given date matches no date, both will be false. If it refers to "now", both will be the same DateTime.
     * In all other cases, start and end describe a time span corresponding to given date, e.g.
     * 2017-01-01 -> [2017-01-01 00:00:00, 2017-01-01 23:59:59]
     * 2017-05    -> [2017-05-01 00:00:00, 2017-05-31 23:59:59]
     *
     * Unspecific dates like "May" are always sent as future dates (next May) by Amazon.
     * You may use the optional $date_back parameter to enforce past dates (last May).
     * If we are right now in May, no backdating is performed.
     * Caution: please consider that a user might have actually referred to a future date.
     *
     * @param   string      $amazon_date        AMAZON.DATE slot value
     * @param   bool        $date_back          Enforce a date in the past?
     * @return  \DateTime[]
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     * @see     https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/built-in-intent-ref/slot-type-reference#date
     */
    public function extractAmazonDate($amazon_date, $date_back = false) {
        $start    = false;
        $end = false;
        $origin   = false;

        switch(true) {
            // Now
            case ($amazon_date == 'PRESENT_REF'):
                $origin = 'now';
                $start  = new \DateTime();
                $end    = clone $start;
            break;

            // Date
            case preg_match('~^[\d]{4}-[\d]{2}-[\d]{2}$~', $amazon_date):
                $origin = 'date';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date);
                $start->setTime(0, 0, 0);
                $end = clone $start;
                $end->setTime(23, 59, 59);
            break;

            // Week
            case preg_match('~^([\d]{4})-W([\d]{2})$~', $amazon_date, $matches):
                $origin = 'week';
                $start  = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $end = clone $start;
                $end->add(new \DateInterval('P1W'))->sub(new \DateInterval('PT1S'));
            break;

            // Weekend
            case preg_match('~^([\d]{4})-W([\d]{2})-WE$~', $amazon_date, $matches):
                $origin = 'weekend';
                $start  = new \DateTime();
                $start->setTime(0, 0, 0);
                $start->setISODate(intval($matches[1]), intval($matches[2]));
                $start->add(new \DateInterval('P5D'));
                $end = clone $start;
                $end->add(new \DateInterval('P2D'))->sub(new \DateInterval('PT1S'));
            break;

            // Month
            case preg_match('~^[\d]{4}-[\d]{2}$~', $amazon_date):
                $origin = 'month';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date . '-01');
                $start->setTime(0, 0, 0);
                $end = clone $start;
                $end->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));
            break;

            // Year
            case preg_match('~^[\d]{4}$~', $amazon_date):
                $origin = 'year';
                $start  = \DateTime::createFromFormat('Y-m-d', $amazon_date . '-01-01');
                $start->setTime(0, 0, 0);
                $end = clone $start;
                $end->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));
            break;

            case preg_match('~^([\d]{4})-XX-XX$~', $amazon_date, $matches):
                $origin = 'year';
                $start  = \DateTime::createFromFormat('Y-m-d', $matches[1] . '-01-01');
                $start->setTime(0, 0, 0);
                $end = clone $start;
                $end->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));
            break;

            // Decade
            case preg_match('~^([\d]{3})X$~', $amazon_date, $matches):
                $origin = 'decade';
                $start  = \DateTime::createFromFormat('Y-m-d', $matches[1] . '0-01-01');
                $start->setTime(0, 0, 0);
                $end = clone $start;
                $end->add(new \DateInterval('P10Y'))->sub(new \DateInterval('PT1S'));
            break;

            // Season
            case preg_match('~^([\d]{4})-(SP|SU|FA|WI)$~', $amazon_date, $matches):
                $seasons = ['SP' => 'spring', 'SU' => 'summer', 'FA' => 'fall', 'WI' => 'winter'];
                $origin  = 'season';
                $start   = $this->getDateFromSeason(intval($matches[1]), $seasons[$matches[2]], 'northern', false);
                $end = clone $start;
                $end->add(new \DateInterval('P3M'))->sub(new \DateInterval('PT1S'));
            break;
        }

        if($date_back && in_array($origin, ['date', 'month', 'season'])) {
            $now  = new \DateTime();
            $diff = $now->getTimestamp() - $start->getTimestamp();
            if($diff < 0) {
                // Need to date back as requested with $date_back for some origins.
                if($diff > -604800) {
                    // If difference is less than seven days and origin is date, most likely a day name was given.
                    $start->sub(new \DateInterval('P7D'));
                    $end->sub(new \DateInterval('P7D'));
                } else {
                    // In all other cases, subtract a year (for dates like "3rd of November", months like "August"
                    // and seasons like "Winter")
                    $start->sub(new \DateInterval('P1Y'));
                    $end->sub(new \DateInterval('P1Y'));
                }
            }
        }

        return [$start, $end];
    }

    /**
     * extractAmazonTime
     *
     * Extracts an AMAZON.TIME slot value and sets time portion of a given date (or today, if omitted)
     *
     * For time period indicators, the following times are returned:
     * night: 00:00, morning: 06:00, afternoon: 12:00, evening: 18:00
     *
     * @param   string      $amazon_time        AMAZON.TIME slot value
     * @param   \DateTime   $date               DateTime object or null
     * @return  false|\DateTime
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     * @see     https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/built-in-intent-ref/slot-type-reference#time
     */
    public function extractAmazonTime($amazon_time, $date = null) {
        if(!$date instanceof \DateTime) {
            $date = new \DateTime;
        }

        switch(true) {
            case preg_match('~^(\d{2}:\d{2})~', $amazon_time, $matches):
                list($hours, $minutes) = explode(':', $matches[1]);
                $date->setTime($hours, $minutes, 0);
            break;

            case ($amazon_time == 'NI'):
                $date->setTime(0, 0, 0);
            break;

            case ($amazon_time == 'MO'):
                $date->setTime(6, 0, 0);
            break;

            case ($amazon_time == 'AF'):
                $date->setTime(12, 0, 0);
            break;

            case ($amazon_time == 'EV'):
                $date->setTime(18, 0, 0);
            break;
        }

        return $date;
    }


    /**
     * convertDateTimeToHuman
     *
     * Converts a DatTime object to a string that can be outspoken by Alexa
     *
     * Omits year, if date is current year. Omits minutes, if zero.
     * Can be configured to reply with relative day names (today, yesterday, friday, ...) instead of day number
     *
     * Options array values:
     * relative:  return "today", "yesterday" or "friday" if applicable; day number otherwise
     * with_time: including time portion in response (seconds are omitted)
     *
     * Translations array keys:
     * days:       ['Monday', ..., 'Sunday']
     * months:     ['January', ..., 'December']
     * today:      'today'
     * yesterday:  'yesterday'
     * prefix_rel: prefix for relative dates (like 'on' or 'at' or empty string, if not needed)
     * prefix_abs: prefix for absolute dates (like 'on' or 'at' or empty string, if not needed)
     * at:         'at'
     * o'clock:    'o\'clock' (mind proper apostrophe escaping)
     *
     * @param   \DateTime       $date           DateTime object
     * @param   array           $options        Options
     * @param   array           $translations   Translations
     * @return  string
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     * @todo    options for ordering date parts, 12h/24h handling and enumerations ("1st" versus "1.")
     */
    public function convertDateTimeToHuman(\DateTime $date, $options = [], $translations = []) {
        $now    = new \DateTime();
        $diff   = $now->getTimestamp() - $date->getTimestamp();
        $result = array();

        // Relative day, if not more than 7 days ago
        if(in_array('relative', $options) && ($diff < 604800)) {
            array_push($result, $translations['prefix_rel']);

            // Today
            if($date->format('d') == date('d')) {
                array_push($result, $translations['today']);

                // Yesterday
            } elseif($date->format('d') == date('d', time() - 86400)) {
                array_push($result, $translations['yesterday']);

                // Day name
            } else {
                array_push($result, $translations['days'][$date->format('N') - 1]);
            }
        } else {
            array_push($result, $translations['prefix_abs']);

            // Day without leading zero, including enumeration point
            array_push($result, $date->format('j.'));

            // Month name
            array_push($result, $translations['months'][$date->format('n') - 1]);

            // Year, if not current
            $year = $date->format('Y');
            if($year != date('Y')) {
                array_push($result, $year);
            }
        }

        if(in_array('with_time', $options)) {
            array_push($result, $translations['at']);

            // Hour without leading zero
            array_push($result, $date->format('G'));
            array_push($result, $translations['o\'clock']);

            // Minutes without leading zero, only if greater than zero
            $minutes = intval($date->format('i'));
            if($minutes > 0) {
                array_push($result, $minutes);
            }
        }

        $result = trim(implode(' ', $result));
        return preg_replace('/\s+/', ' ', $result);
    }

    /**
     * Converts a list to a comma separated string and using a conjunction word before the last element
     * Example: [one, two, three] -> one, two and three
     *
     * @param   array       $list
     * @param   string      $conjunction
     * @return  string
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function convertListToHuman($list, $conjunction = 'and') {
        if(count($list) > 1) {
            $last1 = array_pop($list);
            $last2 = count($list) - 1;
            $list[$last2] = $list[$last2] . ' ' . $conjunction . ' ' . $last1;
        }

        return implode(', ', $list);
    }
}
