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
        $this->i18n      = $this->container->get('i18n');

        $this->i18n->chooseLocale($container->request->getHeaderLine('Accept-Language'));
    }
}
