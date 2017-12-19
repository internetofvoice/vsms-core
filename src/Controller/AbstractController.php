<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use \Interop\Container\Exception\ContainerException;
use \Slim\Container;

/**
 * Class AbstractController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
abstract class AbstractController {
    /** @var \Slim\Container $container */
    protected $container;

    /** @var array $settings */
    protected $settings;

    /** @var \InternetOfVoice\VSMS\Core\Helper\LogHelper $logger */
    protected $logger;

    /** @var \InternetOfVoice\VSMS\Core\Helper\TranslationHelper $translator */
    protected $translator;

    /**
     * Constructor
     *
     * @param   \Slim\Container $container  Slim app container
     * @access  public
     * @throws  ContainerException
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->settings  = $this->container->get('settings');

        if(in_array('logger', $this->settings['auto_init'])) {
            $this->logger = $this->container->get('logger');
        }

        if(in_array('translator', $this->settings['auto_init'])) {
            $this->translator = $this->container->get('translator');
            $this->translator->chooseLocale($container->request->getHeaderLine('Accept-Language'));
        }
    }
}
