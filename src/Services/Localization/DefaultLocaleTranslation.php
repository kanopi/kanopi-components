<?php

namespace Kanopi\Components\Services\Localization;

use Kanopi\Components\Model\Data\Localization\LocalizedTextAssociation;
use Kanopi\Components\Model\Data\Localization\TranslationText;
use Kanopi\Components\Repositories\Localization\LocalizationProvider;
use Kanopi\Components\Repositories\Localization\TranslationProvider;

/**
 * Coordinate a Localization system with Translation Provider
 *
 * @package kanopi/components
 */
class DefaultLocaleTranslation implements LocaleTranslation {
	/**
	 * System localization provider
	 *
	 * @var LocalizationProvider
	 */
	protected LocalizationProvider $systemProvider;
	/**
	 * External translation provider
	 *
	 * @var TranslationProvider
	 */
	protected TranslationProvider $translationProvider;
	/**
	 * Target language code
	 *
	 * @var string
	 */
	protected string $targetLanguageCode;

	/**
	 * Setup a localization translation service
	 *
	 * @param LocalizationProvider $_systemProvider      System localization provider
	 * @param TranslationProvider  $_translationProvider External translation provider
	 */
	public function __construct(
		LocalizationProvider $_systemProvider,
		TranslationProvider $_translationProvider
	) {
		$this->systemProvider      = $_systemProvider;
		$this->translationProvider = $_translationProvider;
		$this->targetLanguageCode  = $this->systemProvider->readDefaultLanguageCode();
	}

	/**
	 * {@inheritDoc}
	 */
	public function additionalLanguageCodes(): array {
		return $this->systemProvider->readAdditionalLanguageCodes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function changeLanguageCode( string $_code ): LocaleTranslation {
		if ( in_array( $_code, $this->availableLanguageCodes(), true ) ) {
			$this->targetLanguageCode = $_code;
			$this->translationProvider->changeTargetLanguage( $_code );
		}

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function availableLanguageCodes(): array {
		return $this->systemProvider->readLanguageCodes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaultLanguageCode(): string {
		return $this->systemProvider->readDefaultLanguageCode();
	}

	/**
	 * {@inheritDoc}
	 */
	public function resetLanguageCode(): LocaleTranslation {
		$this->targetLanguageCode = $this->systemProvider->readDefaultLanguageCode();

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function targetLanguageCode(): string {
		return $this->targetLanguageCode;
	}

	/**
	 * {@inheritDoc}
	 */
	public function translate( string $_text, string $_sourceLanguageCode = null ): TranslationText {
		$checkSource        = $_sourceLanguageCode ?? $this->systemProvider->readDefaultLanguageCode();
		$sourceLanguageCode = in_array( $checkSource, $this->availableLanguageCodes(), true )
			? $checkSource
			: $this->systemProvider->readDefaultLanguageCode();

		return $this->translationProvider->translate(
			LocalizedTextAssociation::fromText( $_text, $sourceLanguageCode )
		);
	}
}
