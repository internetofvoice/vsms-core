<?php

namespace InternetOfVoice\VSMS\Core\Service;

use \InternetOfVoice\VSMS\Core\Helper\LogHelper;
use \InternetOfVoice\VSMS\Core\Helper\SkillHelper;
use \InternetOfVoice\VSMS\Core\Helper\TranslationHelper;

/**
 * AbstractService
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractService
{
    /** @var LogHelper $logger */
    protected $logger;

    /** @var skillHelper $skillHelper */
    protected $skillHelper;

    /** @var TranslationHelper $translator */
    protected $translator;


    /**
     * @param   LogHelper           $logger
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }

    /**
     * @param   SkillHelper         $skillHelper
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function setSkillHelper($skillHelper) {
        $this->skillHelper = $skillHelper;
    }

    /**
     * @param   TranslationHelper   $translator
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function setTranslator($translator) {
        $this->translator = $translator;
    }
}
