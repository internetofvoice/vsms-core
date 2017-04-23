<?php

namespace Tests\InternetOfVoice\VSMS\Core\Fixtures;

use InternetOfVoice\VSMS\Core\Controller\AbstractController;

/**
 * MockController
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class MockController extends AbstractController
{
    /**
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @param 	\Slim\Http\Response		$response 	Slim response
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLanguage($request, $response) {
        $response->getBody()->write($this->i18n->getLanguage());
        return $response;
    }

    /**
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @param 	\Slim\Http\Response		$response 	Slim response
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocale($request, $response) {
        $response->getBody()->write($this->i18n->getLocale());
        return $response;
    }
}
