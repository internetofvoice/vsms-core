<?php

namespace InternetOfVoice\VSMS\Core\Service;

use \InternetOfVoice\VSMS\Core\Helper\LogHelper;

/**
 * AbstractService
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractService
{
    /** @var LogHelper $logger */
    protected $logger;

    /**
     * Constructor
     *
     * @param   LogHelper   $logger     optional Logging helper
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(LogHelper $logger = null) {
        $this->logger = $logger;
    }

    /**
     * @return  LogHelper
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * @param   LogHelper   $logger
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }
}
