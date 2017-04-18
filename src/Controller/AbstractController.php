<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use Slim\Container;

/**
 * AbstractController
 *
 * @author	Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

abstract class AbstractController
{
	protected $container;
	protected $settings;

	protected $language;
	protected $locale;

	/**
	 * Constructor
	 *
	 * @param 	\Slim\Container         $container	Slim app container
	 * @access	public
	 * @author	a.schmidt@internet-of-voice.de
	 */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->settings = $this->container->get('settings');

        $accept = $container->request->getHeaderLine('Accept-Language');
        $this->locale = $this->chooseLocale($accept, $this->settings['locales'], $this->settings['locale_default']);
        $this->language = substr($this->locale, 0, (strpos($this->locale, '-')));
    }

    /**
     * Choose Locale
     *
     * Try to match client preferred and application supported locales
     *
     * @param 	string	$accept			Accept-Language header
     * @param 	array	$available		Available locales
     * @param 	string	$default		Default locale
     * @return  string
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function chooseLocale($accept, $available, $default)
    {
        $reply = array();
        $available = array_flip($available);
        $available2 = array();
        foreach ($available as $key => $value) {
            $available2[substr($key, 0, 2)] = $key;
        }

        preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', $accept, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $temp = explode('-', $match[1]) + array('', '');
            $lang = array_shift($temp);
            $value = isset($match[2]) ? (float)$match[2] : 1.0;

            // Full match (language_territory)?
            if (isset($available[$match[1]]) && !isset($reply[$match[1]])) {
                $reply[$match[1]] = $value;
            }

            // Language match (without territory)?
            if (isset($available2[$lang]) && !isset($reply[$available2[$lang]])) {
                $reply[$available2[$lang]] = $value - 0.05;
            }
        }

        arsort($reply);
        if (count($reply)) {
            return key($reply);
        }

        return $default;
    }
}
