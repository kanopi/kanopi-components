<?php

namespace Kanopi\Components\Repositories\Localization;

use Kanopi\Components\Model\Data\Localization\{LocalizedText, TranslationText};

/**
 * Translation service
 *
 * @package kanopi/components
 */
interface TranslationProvider {
	/**
	 * Change future translate() calls to use the provided ISO 639-1 language code
	 *
	 * @param string $_languageCode Language code
	 *
	 * @return void
	 */
	public function changeTargetLanguage( string $_languageCode ): void;

	/**
	 * Translate text to the current target language
	 *
	 * @param LocalizedText $_source Text to translate
	 *
	 * @return TranslationText Translated text
	 */
	public function translate( LocalizedText $_source ): TranslationText;
}