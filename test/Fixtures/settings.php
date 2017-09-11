<?php

/**
 * Settings fixture
 *
 * @author	Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

return [
    'settings' => [
        'displayErrorDetails'       => true,
        'addContentLengthHeader'    => false,
        'validateCertificate'       => false,
        'environment'               => 'dev-test',

        'auto_init'                 => ['logger', 'translator', 'skillHelper'],

        'locale_default'            => 'en-US',
        'locales'                   => ['de-DE', 'en-US', 'en-GB'],
    ]
];
