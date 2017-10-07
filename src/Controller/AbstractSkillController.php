<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use InternetOfVoice\LibVoice\Alexa\Request\AlexaRequest;
use InternetOfVoice\LibVoice\Alexa\Response\AlexaResponse;
use InvalidArgumentException;
use Slim\Container;

/**
 * AbstractSkillController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */
abstract class AbstractSkillController extends AbstractController
{
	/** @var AlexaRequest */
    protected $alexaRequest;

    /** @var AlexaResponse $alexaResponse */
    protected $alexaResponse;

    /** @var array $askApplicationIds */
    protected $askApplicationIds;

    /** @var \InternetOfVoice\VSMS\Core\Helper\skillHelper $skillHelper */
    protected $skillHelper;


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

        if(in_array('skillHelper', $this->settings['auto_init'])) {
            $this->skillHelper = $this->container->get('skillHelper');
        }
    }


    /**
     * Create Alexa Request from Slim Request
     *
     * @param   \Slim\Http\Request      $request    Slim request
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function createAlexaRequest($request) {
        // Create AlexaRequest from HTTP request
        $this->alexaRequest = new AlexaRequest(
            $request->getBody()->getContents(),
            $this->askApplicationIds,
	        $request->getHeaderLine('Signaturecertchainurl'),
	        $request->getHeaderLine('Signature'),
	        $this->settings['validateCertificate']
        );

        // Update auto initialized translator as Alexa request might contain a locale
        if($this->alexaRequest->getRequest()->getLocale() && in_array('translator', $this->settings['auto_init'])) {
            $this->translator->chooseLocale($this->alexaRequest->getRequest()->getLocale());
        }
    }

    /**
     * Dispatch Alexa Request
     *
     * Dispatches Alexa Request to either:
     * - launch()
     * - sessionEnded()
     * - a method derived from intent name by removing non-word chars and prefixing with "intent", examples:
     *   "AMAZON.HelpIntent" -> "intentAMAZONHelpIntent()"
     *   "MyIntent" -> "intentMyIntent()"
     *
     * @param   \Slim\Http\Response     $response   Slim response
     * @return  \Slim\Http\Response
     * @throws  \InvalidArgumentException
     * @access  protected
     * @author  a.schmidt@internet-of-voice.de
     */
    protected function dispatchAlexaRequest($response) {
        switch($this->alexaRequest->getRequest()->getType()) {
            case 'LaunchRequest':
                $this->launch();
            break;

	        case 'IntentRequest':
	        	/** @var \InternetOfVoice\LibVoice\Alexa\Request\Request\IntentRequest $intentRequest */
	        	$intentRequest = $this->alexaRequest->getRequest();

                // derive handler method name from intent name
                $method = 'intent' . preg_replace('#\W#', '', $intentRequest->getIntent()->getName());

                if(method_exists($this, $method)) {
                    call_user_func(array($this, $method));
                } else {
                    throw new InvalidArgumentException('Undefined intent handler: ' . $method);
                }
            break;

	        case 'SessionEndedRequest':
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

    /** Handlers for IntentRequests as per Amazon requirements */
    abstract protected function intentAMAZONHelpIntent();
    abstract protected function intentAMAZONStopIntent();
    abstract protected function intentAMAZONCancelIntent();

    /** Required handler for AlexaSessionEndedRequest */
    abstract protected function sessionEnded();
}
