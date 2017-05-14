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
     * Get language
     *
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @param 	\Slim\Http\Response		$response 	Slim response
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLanguage($request, $response) {
        $response->getBody()->write($this->translator->getLanguage());
        return $response;
    }

    /**
     * Get locale
     *
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @param 	\Slim\Http\Response		$response 	Slim response
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocale($request, $response) {
        $response->getBody()->write($this->translator->getLocale());
        return $response;
    }
}
