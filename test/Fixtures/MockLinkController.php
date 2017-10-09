<?php

namespace Tests\InternetOfVoice\VSMS\Core\Fixtures;

use InternetOfVoice\VSMS\Core\Controller\AbstractLinkController;

/**
 * Class MockLinkController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class MockLinkController extends AbstractLinkController {
    /**
     * Get parameters
     *
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @return  array
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getParameters($request) {
        return json_encode($this->validateRequestParameters($request, 'test'));
    }

    /**
     * Get location
     *
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @return  string
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocation($request) {
        $parameters = $this->validateRequestParameters($request, 'test');
        return $this->getRedirectLocation($parameters, 'my_token');
    }
}
