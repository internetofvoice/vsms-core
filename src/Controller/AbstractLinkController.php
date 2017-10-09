<?php

namespace InternetOfVoice\VSMS\Core\Controller;

use InvalidArgumentException;

/**
 * Class AbstractLinkController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
abstract class AbstractLinkController extends AbstractController {
    /**
     * Validate request parameters
     *
     * @param 	\Slim\Http\Request      $request 	    Slim request
     * @param 	string					$amz_client_id  Client ID as configured in Amazon Developer Console
     * @return  array
     * @throws  \InvalidArgumentException
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function validateRequestParameters($request, $amz_client_id) {
        $errors        = array();
        $state         = $request->getParam('state', false);
        $redirect_uri  = $request->getParam('redirect_uri', false);
        $client_id     = $request->getParam('client_id', false);
        $scope         = $request->getParam('scope', false);
        $response_type = $request->getParam('response_type', false);

        if($state === false) {
            $errors[] = 'state';
        }

        if($redirect_uri === false) {
            $errors[] = 'redirect_uri';
        }

        if($client_id != $amz_client_id) {
            $errors[] = 'client_id';
        }

        if($response_type != 'token') {
            $errors[] = 'response_type';
        }

        if(count($errors)) {
            throw new InvalidArgumentException('Missing or incorrect parameters: ' . implode(', ', $errors));
        } else {
            return [
                'state'         => $state,
                'redirect_uri'  => $redirect_uri,
                'client_id'     => $client_id,
                'scope'         => $scope,
                'response_type' => $response_type,
            ];
        }
    }

    /**
     * Get redirect location
     *
     * @param   array       $parameters     Amazon parameters (see validateRequestParameters())
     * @param   string      $access_token   Generated access token
     * @return  string
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getRedirectLocation($parameters, $access_token) {
        $location = array();
        array_push($location, $parameters['redirect_uri'] . '#state=' . $parameters['state']);
        array_push($location, 'access_token=' . $access_token);
        array_push($location, 'token_type=Bearer');

        return implode('&', $location);
    }
}
