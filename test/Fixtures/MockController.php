<?php

namespace Tests\Fixtures;

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
     * @param 	array 					$args 		Arguments
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLanguage($request, $response, $args)
    {
        $response->getBody()->write($this->language);
        return $response;
    }

    /**
     * @param 	\Slim\Http\Request      $request 	Slim request
     * @param 	\Slim\Http\Response		$response 	Slim response
     * @param 	array 					$args 		Arguments
     * @return  \Slim\Http\Response
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getLocale($request, $response, $args)
    {
        $response->getBody()->write($this->locale);
        return $response;
    }
}
