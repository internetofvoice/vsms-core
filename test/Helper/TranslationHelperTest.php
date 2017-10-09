<?php

namespace Tests\InternetOfVoice\VSMS\Core\Helper;

use InternetOfVoice\VSMS\Core\Helper\TranslationHelper;

/**
 * Class TranslationHelperTest
 *
 * @author  Alexander Schmidt <a.schmidt@internet-of-voice.de>
 * @license http://opensource.org/licenses/MIT
 */
class TranslationHelperTest extends \PHPUnit_Framework_TestCase {
	/**
	 * testHelper
	 */
    public function testHelper() {
        $settings = require __DIR__ . '/../Fixtures/settings.php';
        $helper = new TranslationHelper($settings['settings']['locales'], $settings['settings']['locale_default']);

        $this->assertFalse($helper->addTranslation('/NON_EXISTENT_DIR', 'messages'));

	    $helper->chooseLocale('xy-XY');
	    $this->assertEquals($settings['settings']['locale_default'], $helper->getLocale());

	    $helper->setLanguage('xy');
	    $this->assertEquals(substr($settings['settings']['locale_default'], 0, 2), $helper->getLanguage());

	    $helper->chooseLocale('de-DE');
        $this->assertEquals('de-DE', $helper->getLocale());
        $this->assertEquals('de', $helper->getLanguage());
        $this->assertEquals('I am afraid I did not understand you.', $helper->t('I am afraid I did not understand you.'));

        $helper->addTranslation(__DIR__ . '/../Fixtures', 'messages');
        $this->assertEquals('Ich habe Sie leider nicht verstanden.', $helper->t('I am afraid I did not understand you.'));

	    $this->assertFalse($helper->t());

	    $this->assertEquals('NON_EXISTENT_WORD_COMBINATION', $helper->a('NON_EXISTENT_WORD_COMBINATION'));
        $this->assertArrayHasKey('key1', $helper->a('array'));
    }
}
