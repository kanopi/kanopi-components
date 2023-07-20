<?php

namespace Kanopi\Components\Model\Data\Localization;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Entity which supports localized/translated content of the entire entity model
 *
 * @package kanopi/components
 */
interface TranslatableEntity {
	/**
	 * ISO 639-1 language code
	 *
	 * @return string
	 */
	public function languageCode(): string;

	/**
	 * Index of current translated entity variations
	 *
	 * @return EntityIterator
	 */
	public function translations(): EntityIterator;

	/**
	 * Update the entities' ISO 639-1 language code
	 *
	 * @param string $_languageCode ISO 639-1 language code to update
	 */
	public function updateLanguageCode( string $_languageCode ): void;

	/**
	 * Update the index of entity translations
	 *
	 * @param EntityIterator $_translations Index of all entity translations to update
	 */
	public function updateTranslations( EntityIterator $_translations ): void;
}