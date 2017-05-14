<?php

namespace InternetOfVoice\VSMS\Core\Controller;

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

    /** @var \InternetOfVoice\VSMS\Core\Helper\LogHelper $logger */
    protected $logger;

    /** @var \InternetOfVoice\VSMS\Core\Helper\TranslationHelper $translator */
    protected $translator;

    /**
     * Constructor
     *
     * @param   \Slim\Container $container  Slim app container
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(Container $container) {
        $this->container  = $container;
        $this->settings   = $this->container->get('settings');
        $this->logger     = $this->container->get('logger');
        $this->translator = $this->container->get('translator');
        $this->translator->chooseLocale($container->request->getHeaderLine('Accept-Language'));
    }
}
