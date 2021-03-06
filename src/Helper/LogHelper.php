<?php

namespace InternetOfVoice\VSMS\Core\Helper;

use Analog\Logger;

/**
 * Class LogHelper
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class LogHelper extends Logger {
    /** @var array $mask */
    protected $mask = [];

    /**
     * Get mask
     *
     * @return  array
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function getMask() {
        return $this->mask;
    }

    /**
     * Set mask
     *
     * @param   array   $mask   Keys, whose values should be masked out in logs (helpful to suppress sensitive data)
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function setMask($mask) {
        $this->mask = $mask;
    }

    /**
     * Log server request
     *
     * @param 	\Slim\Http\Request	    $request 	    Request object
     * @param 	array 					$extra          Additional log data
     * @param 	bool 					$includeHeader  Whether to include header data
     * @access	public
     * @author	a.schmidt@internet-of-voice.de
     */
    public function logRequest($request, $extra = [], $includeHeader = true) {
        $extra = count($extra) ? ' ' . json_encode($extra) : '';

        // Caller info
        $trace = debug_backtrace(false, 2);
        if(isset($trace[1])) {
            $caller = substr(strrchr($trace[1]['class'], '\\'), 1) . '::' .  $trace[1]['function'];
            $this->debug($request->getMethod() . ' ' . $caller . $extra);
        }

        // Parameters, if applicable
        $params = $request->getQueryParams();
        if(!empty($params)) {
            $this->debug('Query params: ' . json_encode($params));
        }

        // Request headers, if applicable
	    if($includeHeader) {
		    $headers = array_map(function($element) {
			    return implode(', ', $element);
		    }, $request->getHeaders());

		    $this->debug('Header: ' . json_encode($headers));
	    }

        // Request body, if applicable
        $body = $request->getParsedBody();
        if(!is_null($body)) {
            if(is_array($body)) {
                // Apply logger mask to all nested elements
                $mask = array();
                foreach($this->getMask() as $key) {
                    array_push($mask, $key);
                }

                array_walk_recursive($body, function(&$value, $key) use ($mask) {
                    if(in_array($key, $mask)) {
                        $value = '***';
                    }
                });
            }

            $this->debug('Body: ' . json_encode($body));
        }
    }
}
