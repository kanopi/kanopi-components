<?php

namespace Kanopi\Components\Repositories\WordPress\Localization;

use Kanopi\Components\Repositories\Localization\LocalizationProvider;

/**
 * Polylang localization provider data
 *
 * @package kanopi/components
 */
class PolylangProvider implements LocalizationProvider {
	/**
	 * Set of available  ISO 639-1 language codes
	 *
	 * @var array
	 */
	protected array $additionalSupportedLanguages;

	/**
	 * Default ISO 639-1 language code for new content
	 *
	 * @return string
	 */
	protected string $defaultLanguage;

	/**
	 * Initialize the Polylang Provider
	 */
	public function __construct() {
		$this->defaultLanguage = pll_default_language();

		$allLanguageCodes = pll_languages_list( [
			'hide_empty' => false
		] );

		$this->additionalSupportedLanguages = array_filter(
			$allLanguageCodes,
			function ( string $_code ): bool {
				return $this->defaultLanguage !== $_code;
			}
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function readAdditionalLanguageCodes(): array {
		return $this->additionalSupportedLanguages;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readDefaultLanguageCode(): string {
		return $this->defaultLanguage;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readLanguageCodes(): array {
		return array_merge(
			[ $this->defaultLanguage ],
			$this->additionalSupportedLanguages
		);
	}
}