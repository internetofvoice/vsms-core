<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use Alexa\Request\IntentRequest as AlexaIntentRequest;
use Alexa\Request\LaunchRequest as AlexaLaunchRequest;
use Alexa\Request\Request as AlexaRequest;
use Alexa\Request\SessionEndedRequest as AlexaSessionEndedRequest;
use Alexa\Response\Response as AlexaResponse;
use InvalidArgumentException;
use Slim\Container;

/**
 * AbstractSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractSkillController extends AbstractController
{
    protected $alexaRequest;

    /** @var \Alexa\Response\Response $alexaResponse */
    protected $alexaResponse;

    /** @var array $askApplicationIds */
    protected $askApplicationIds;

    /**
     * Constructor
     *
     * @param   Container   $container  Slim app container
     * @access  public
     * @author  a.schmidt@internet-of-voice.de
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->alexaResponse = new AlexaResponse;
    }


    /**
     * Create Alexa Request from Slim Request
     *
     * @param   \Slim\Http\Request      $request    Slim request
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function createAlexaRequest($request) {
        // Instantiate AlexaRequest from request object
        $alexa = new AlexaRequest(
            $request->getBody()->getContents(),
            $this->askApplicationIds[$this->settings['environment']]
        );

        // Create AlexaRequest from request data
        $this->alexaRequest = $alexa->fromData($this->settings['validateCertificate']);

        // Reset i18n as Alexa request might contain a locale
        if($this->alexaRequest->locale) {
            $locale = $this->chooseLocale($this->alexaRequest->locale);
            $this->i18n->reset($locale, substr($locale, 0, (strpos($locale, '-'))));
        }
    }

    /**
     * Dispatch Alexa Request
     *
     * Dispatches Alexa Request to either:
     * - launch()
     * - sessionEnded()
     * - a method derived from intent name by removing non-word chars and prefixing with "intent", example:
     *   "AMAZON.HelpIntent" -> "intentAMAZONHelpIntent()"
     *
     * @param   \Slim\Http\Response     $response   Slim response
     * @return  \Slim\Http\Response
     * @throws  \InvalidArgumentException
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function dispatchAlexaRequest($response) {
        switch(true) {
            case $this->alexaRequest instanceof AlexaLaunchRequest:
                $this->launch();
            break;

            case $this->alexaRequest instanceof AlexaIntentRequest:
                // derive handler method name from intent name
                $method = 'intent' . preg_replace('#\W#', '', $this->alexaRequest->intentName);

                if(method_exists($this, $method)) {
                    call_user_func(array($this, $method));
                } else {
                    throw new InvalidArgumentException('Undefined intent handler: ' . $method);
                }
            break;

            case $this->alexaRequest instanceof AlexaSessionEndedRequest:
                $this->sessionEnded();
            break;

            default:
                throw new InvalidArgumentException('Unknown Alexa request: ' . get_class($this->alexaRequest));
            break;
        }

        return $response->withJson($this->alexaResponse->render());
    }


    /** Required handler for AlexaLaunchRequest */
    abstract protected function launch();

    /** Required handler for AlexaSessionEndedRequest */
    abstract protected function sessionEnded();
}