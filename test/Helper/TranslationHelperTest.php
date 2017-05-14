<?php

namespace Tests\InternetOfVoice\VSMS\Core\Helper;

use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;

/**
 * TranslationHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 */

class TranslationHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testHelper() {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $helper = new TranslationHelper($settings['settings']['locales'], $settings['settings']['locale_default']);

        $helper->chooseLocale('de-DE');
        $this->assertEquals('de-DE', $helper->getLocale());
        $this->assertEquals('de', $helper->getLanguage());
        $this->assertEquals('I am afraid I did not understand you.', $helper->t('I am afraid I did not understand you.'));

        $helper->addTranslation(__DIR__ . '/../Fixtures', 'messages');
        $this->assertEquals('Ich habe Sie leider nicht verstanden.', $helper->t('I am afraid I did not understand you.'));

        $this->assertEquals('NON_EXISTENT_WORD_COMBINATION', $helper->a('NON_EXISTENT_WORD_COMBINATION'));
    }
}
