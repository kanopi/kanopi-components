<?php

namespace Kanopi\Components\Services\Localization;

use Kanopi\Components\Model\Data\Localization\TranslationText;

/**
 * Coordinate a Localization system with Translation Provider
 *  - Works assuming the system default language code is the source language unless overridden
 *
 * @package kanopi/components
 */
interface LocaleTranslation {
	/**
	 * All non-default ISO 639-1 language codes
	 *
	 * @return string[]
	 */
	public function additionalLanguageCodes(): array;

	/**
	 * All available ISO 639-1 language codes
	 *
	 * @return string[]
	 */
	public function availableLanguageCodes(): array;

	/**
	 * Current target ISO 639-1 language code
	 *
	 * @param string $_code Target ISO 639-1 language code
	 *
	 * @return LocaleTranslation Updated service
	 */
	public function changeLanguageCode( string $_code ): LocaleTranslation;

	/**
	 * Default system ISO 639-1 language code
	 *
	 * @return string
	 */
	public function defaultLanguageCode(): string;

	/**
	 * Reset to the default ISO 639-1 language code
	 *
	 * @return LocaleTranslation Updated service
	 */
	public function resetLanguageCode(): LocaleTranslation;

	/**
	 * Current target ISO 639-1 language code
	 *
	 * @return string
	 */
	public function targetLanguageCode(): string;

	/**
	 * Translate text to the current target language
	 *
	 * @param string      $_text               Text to translate
	 * @param string|null $_sourceLanguageCode Optional source language code, default system language is assumed
	 *
	 * @return TranslationText
	 */
	public function translate( string $_text, string $_sourceLanguageCode = null ): TranslationText;
}
