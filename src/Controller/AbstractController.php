<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;
use Slim\Container;

/**
 * AbstractController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractController
{
    /** @var \Slim\Container $container */
    protected $container;

    /** @var array $settings */
    protected $settings;

    /** @var \InternetOfVoice\VSMS\Core\Helper\TranslationHelper $i18n */
    protected $i18n;

    /**
     * Constructor
     *
     * @param   \Slim\Container $container  Slim app container
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->settings  = $this->container->get('settings');

        $locale = $this->chooseLocale($container->request->getHeaderLine('Accept-Language'));
        $this->i18n = new TranslationHelper($locale, substr($locale, 0, (strpos($locale, '-'))));
    }

    /**
     * Choose Locale
     *
     * Try to match client preferred and application supported locales
     *
     * @param   string  $accept     Accepted languages
     * @return  string
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function chooseLocale($accept) {
        $locales    = array();
        $available  = array_flip($this->settings['locales']);
        $available2 = array();

        foreach($available as $key => $value) {
            $available2[substr($key, 0, 2)] = $key;
        }

        preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', $accept, $matches, PREG_SET_ORDER);

        foreach($matches as $match) {
            $temp  = explode('-', $match[1]) + array('', '');
            $lang  = array_shift($temp);
            $value = isset($match[2]) ? (float)$match[2] : 1.0;

            // Full match (language_territory)?
            if(isset($available[$match[1]]) && !isset($locales[$match[1]])) {
                $locales[$match[1]] = $value;
            }

            // Language match (without territory)?
            if(isset($available2[$lang]) && !isset($locales[$available2[$lang]])) {
                $locales[$available2[$lang]] = $value - 0.05;
            }
        }

        if(count($locales)) {
            arsort($locales);
            return key($locales);
        }

        return $this->settings['locale_default'];
    }
}
