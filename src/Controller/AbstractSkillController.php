<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use Alexa\Request\IntentRequest as AlexaIntentRequest;
use Alexa\Request\LaunchRequest as AlexaLaunchRequest;
use Alexa\Request\Request as AlexaRequest;
use Alexa\Request\SessionEndedRequest as AlexaSessionEndedRequest;
use Alexa\Response\Response as AlexaResponse;
use Exception;
use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;
use Slim\Container;

/**
 * AbstractSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractSkillController extends AbstractController
{
    /** @var \Alexa\Request\Request $alexaRequest */
    protected $alexaRequest;

    /** @var \Alexa\Response\Response $alexaResponse */
    protected $alexaResponse;

    /** @var string $skillHandle */
    protected $skillHandle;

    /** @var array $askApplicationId */
    protected $askApplicationId = [
        'dev'   => '',
        'test'  => '',
        'stage' => '',
        'prod'  => '',
    ];

    /** @var array $messages */
    protected $messages = [
        'default' => 'I am afraid I did not understand you.',
    ];

    /** @var array $cars */
    protected $cars = [
    ];

    /**
     * Constructor
     *
     * @param   Container   $container  Slim app container
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->alexaResponse = new AlexaResponse;
    }

    /**
     * Invoke method
     *
     * @param   \Slim\Http\Request      $request    Slim request
     * @param   \Slim\Http\Response     $response   Slim response
     * @param   array                   $args       Arguments
     * @return  \Slim\Http\Response
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function invoke($request, $response, $args)
    {
        // $logger = $this->container->get('logger');
        // $this->logRequest(get_class($this) . '::invoke', $request, $args);

        if ($this->createAlexaRequest($request)) {
            if ($this->alexaRequest instanceof AlexaLaunchRequest) {
                // $logger->info('LaunchRequest');
                $this->launch();
            }

            if ($this->alexaRequest instanceof AlexaIntentRequest) {
                $intent = $this->alexaRequest->intentName;
                $intent_method = preg_replace('#[^a-zA-Z0-9_]#', '', $intent);
                // $logger->info('IntentRequest: ' . $intent, $this->alexaRequest->slots);

                if (method_exists($this, 'intent' . $intent_method)) {
                    call_user_func(array($this, 'intent' . $intent_method));
                } else {
                    // $logger->warning('* Warning: unknown intent "' . $intent . '"');
                    // $this->alexaResponse->respond($this->i18n->t($this->messages['default']));
                    $this->alexaResponse->respond($this->messages['default']);
                }
            }

            if ($this->alexaRequest instanceof AlexaSessionEndedRequest) {
                // $logger->info('SessionEndedRequest');
                $this->sessionEnded();
            }
        }

        // $logger->debug('Reply: ' . json_encode($this->alexaResponse));

        return $response->withJson($this->alexaResponse->render());
    }

    /**
     * Create Alexa request
     *
     * @param   \Slim\Http\Request  $request    Slim request
     * @return  bool
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function createAlexaRequest($request)
    {
        // $logger = $this->container->get('logger');

        try {
            $alexa = new AlexaRequest(
                $request->getBody()->getContents(),
                $this->askApplicationId[$this->settings['environment']]
            );

            $this->alexaRequest = $alexa->fromData($this->settings['validateCertificate']);

            // reload i18n as Alexa request might contain different locale
            if ($this->alexaRequest->locale) {
                $this->locale = $this->chooseLocale(
                    $this->alexaRequest->locale,
                    $this->settings['locales'],
                    $this->settings['locale_default']
                );

                $this->language = substr($this->locale, 0, (strpos($this->locale, '-')));
                $this->i18n     = new TranslationHelper($this->locale, $this->language);
            }

            return true;
        } catch (Exception $e) {
            // $logger->critical('*** Exception', [$e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace()]);
            // $this->alexaResponse->respond($this->i18n->t($this->messages['default']));
            $this->alexaResponse->respond($this->messages['default']);
            return false;
        }
    }

    abstract protected function launch();

    abstract protected function sessionEnded();
}
