<?php

namespace Kanopi\Components\Repositories\Localization;

/**
 * Localization provider system data interface
 *
 * @package kanopi/components
 */
interface LocalizationProvider {
	/**
	 * Set of additional (non-default) supported ISO 639-1 language codes
	 *
	 * @return array
	 */
	public function readAdditionalLanguageCodes(): array;

	/**
	 * Default ISO 639-1 language code for new content
	 *
	 * @return string
	 */
	public function readDefaultLanguageCode(): string;

	/**
	 * Set of available  ISO 639-1 language codes
	 *
	 * @return array
	 */
	public function readLanguageCodes(): array;
}
