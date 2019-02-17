<?php

namespace InternetOfVoice\VSMS\Core\Helper;

/**
 * Class TranslationHelper
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class TranslationHelper {
    /** @var array $locales  */
    protected $locales;

    /** @var string $locale_default  */
    protected $locale_default;

    /** @var array $messages  */
    protected $messages;

    /** @var string locale */
    protected $locale;

    /** @var string language */
    protected $language;


 	/**
	 * Constructor
	 *
     * @param   array       $locales            Supported locales
     * @param   string      $locale_default     Default locale
     * @access	public
	 * @author	a.schmidt@internet-of-voice.de
	 */
    public function __construct($locales, $locale_default) {
        $this->locales = $locales;
        $this->locale_default = $locale_default;
    }

    /**
     * Choose Locale
     *
     * Try to match preferred and supported locales/languages
     *
     * @param   string  $preferred  Preferred locales or languages, e.g. Accept-Language header
     * @return  bool
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function chooseLocale($preferred) {
        $available_locales   = array_flip($this->locales);
        $available_languages = array();
        foreach($available_locales as $key => $value) {
            $available_languages[substr($key, 0, 2)] = $key;
        }

        preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', $preferred, $matches, PREG_SET_ORDER);

        $matched_locales = array();
        foreach($matches as $match) {
            $temp  = explode('-', $match[1]);
            $lang  = array_shift($temp);
            $value = isset($match[2]) ? (float)$match[2] : 1.0;

            // Full match (language_territory)?
            if(isset($available_locales[$match[1]]) && !isset($matched_locales[$match[1]])) {
                $matched_locales[$match[1]] = $value;
            }

            // Language match (without territory)?
            if(isset($available_languages[$lang]) && !isset($matched_locales[$available_languages[$lang]])) {
                $matched_locales[$available_languages[$lang]] = $value - 0.05;
            }
        }

        if(count($matched_locales)) {
            arsort($matched_locales);
            $this->setLocale(key($matched_locales));
            return true;
        } else {
            $this->setLocale($this->locale_default);
            return false;
        }
    }


    /**
     * Add translation
     *
     * @param   string      $path       Translation base path (up to, but without locale dir)
     * @param   string      $file       Translation file name (without .php extension)
     * @return  bool
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function addTranslation($path, $file) {
        $filename = $path . '/' . $this->locale . '/' . $file . '.php';
        if(!is_readable($filename)) {
            return false;
        } else {
            /** @noinspection PhpIncludeInspection */
            $add_messages = require($filename);
            $this->messages = array_merge($this->messages, $add_messages);
            return true;
        }
    }


    /**
     * Translate text
     *
     * @return  mixed
     * @see     http://php.net/manual/en/function.sprintf.php
	 * @access	public
	 * @author	a.schmidt@internet-of-voice.de
     */
    public function t() {
        $args = func_get_args();
        if(count($args) < 1) {
            return false;
        }

        $message = array_shift($args);

        if(!isset($this->messages[$message])) {
	        // No translation available, return original string
            return vsprintf($message, $args);
        }

		$translation = $this->messages[$message];
        if(is_array($translation)) {
        	// Variations found, pick a translation by random
        	$translation = $translation[array_rand($translation)];
        }

        // Return translation with optional replacements
        return vsprintf($translation, $args);
    }

    /**
     * Get translation array (no content substitution)
     *
     * @param   string      $message
     * @return  mixed
     * @access	public
     * @author	a.schmidt@anschluss80.de
     */
    public function a($message) {
        return isset($this->messages[$message]) ? $this->messages[$message] : $message;
    }

    /**
     * Get locale
     *
     * @return  string
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Set locale
     *
     * @param   string      $locale
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function setLocale($locale) {
        $this->locale   = $locale;
        $this->language = substr($locale, 0, (strpos($locale, '-')));
        $this->messages = array();
    }

    /**
     * Get language
     *
     * @return  string
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param   string      $language
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function setLanguage($language) {
        $this->$language = $language;
    }
}
