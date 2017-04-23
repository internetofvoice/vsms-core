<?php

namespace InternetOfVoice\VSMS\Core\Helper;

/**
 * TranslationHelper
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class TranslationHelper
{
    /** @var array $messages  */
    protected $messages;

    /** @var string locale */
    protected $locale;

    /** @var string language */
    protected $language;

 	/**
	 * Constructor
	 *
	 * @access	public
     * @param   string      $locale     Locale
     * @param   string      $language   Language
	 * @author	a.schmidt@internet-of-voice.de
	 */
    public function __construct($locale, $language = '') {
        $this->reset($locale, $language);
    }

    /**
     * Reset to initial state
     *
     * @access	public
     * @param   string      $locale     Locale
     * @param   string      $language   Language
     * @author	a.schmidt@internet-of-voice.de
     */
    public function reset($locale, $language = '') {
        $this->messages = array();
        $this->locale   = $locale;

        if(empty($language)) {
            $this->language = substr($locale, 0, (strpos($locale, '-')));
        } else {
            $this->language = $language;
        }
    }

    /**
     * Add translation
     *
     * @access	public
     * @param   string      $path       Translation base path (up to, but without locale dir)
     * @param   string      $file       Translation file name (without .php extension)
     * @return  bool
     * @author	a.schmidt@internet-of-voice.de
     */
    public function addTranslation($path, $file) {
        $filename = $path . DIRECTORY_SEPARATOR . $this->locale . DIRECTORY_SEPARATOR . $file . '.php';
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
     * Get locale
     *
     * @access	public
     * @return  string
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Get language
     *
     * @access	public
     * @return  string
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Translate text
     *
     * @see     http://php.net/manual/en/function.sprintf.php
	 * @access	public
     * @return  mixed
	 * @author	a.schmidt@internet-of-voice.de
     */
    public function t() {
        $args = func_get_args();
        if(count($args) < 1) {
            return false;
        }

        $message = array_shift($args);
        if(!isset($this->messages[$message])) {
            return vsprintf($message, $args);
        }

        return vsprintf($this->messages[$message], $args);
    }
}
