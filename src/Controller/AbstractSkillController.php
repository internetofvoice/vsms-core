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
    /** @var \Alexa\Request\Request $alexaRequest */
    protected $alexaRequest;

    /** @var \Alexa\Response\Response $alexaResponse */
    protected $alexaResponse;

    /** @var array $askApplicationId */
    protected $askApplicationId;

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
     * Handle request
     *
     * @param   \Slim\Http\Request      $request    Slim request
     * @param   \Slim\Http\Response     $response   Slim response
     * @param   array                   $args       Arguments
     * @return  \Slim\Http\Response
     * @throws  \InvalidArgumentException
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function handle($request, $response, $args) {
        // Instantiate AlexaRequest from request object
        $alexa = new AlexaRequest(
            $request->getBody()->getContents(),
            $this->askApplicationId[$this->settings['environment']]
        );

        // Create AlexaRequest from request data
        $this->alexaRequest = $alexa->fromData($this->settings['validateCertificate']);

        // Reset i18n as Alexa request might contain a locale
        if ($this->alexaRequest->locale) {
            $locale = $this->chooseLocale($this->alexaRequest->locale);
            $this->i18n->reset($locale, substr($locale, 0, (strpos($locale, '-'))));
        }

        // Request is a launch request?
        if ($this->alexaRequest instanceof AlexaLaunchRequest) {
            $this->launch();
        }

        // .. an intent request?
        elseif ($this->alexaRequest instanceof AlexaIntentRequest) {
            // find a handler for the given intent in skill controller
            $intent = $this->alexaRequest->intentName;
            $intent_method = preg_replace('#[^a-zA-Z0-9_]#', '', $intent);
            if (method_exists($this, 'intent' . $intent_method)) {
                call_user_func(array($this, 'intent' . $intent_method));
            } else {
                throw new InvalidArgumentException('Unhandled intent: ' . $intent);
            }
        }

        // .. a session ended request?
        elseif ($this->alexaRequest instanceof AlexaSessionEndedRequest) {
            $this->sessionEnded();
        }

        // .. an unknown request?
        else {
            throw new InvalidArgumentException('Unknown Alexa request: ' . get_class($this->alexaRequest));
        }


        // finally write JSON encoded AlexaResponse to response stream
        return $response->withJson($this->alexaResponse->render());
    }

    /**
     * Implement a handler for AlexaLaunchRequest
     */
    abstract protected function launch();

    /**
     * Implement a handler for AlexaSessionEndedRequest
     */
    abstract protected function sessionEnded();
}
