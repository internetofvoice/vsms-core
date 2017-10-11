<?php

/**
 * Settings fixtures
 *
 * @author	Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */

return [
	'settings' => [
		'displayErrorDetails'    => true,
		'addContentLengthHeader' => false,
		'validateTimestamp'      => false,
		'validateCertificate'    => true,
		'environment'            => 'dev-test',

		'auto_init' => ['logger', 'translator', 'skillHelper'],

		'locale_default' => 'en-US',
		'locales'        => ['de-DE', 'en-US', 'en-GB'],
	],
];
