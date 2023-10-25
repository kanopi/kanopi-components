<?php

namespace Kanopi\Components\Model\Data\Localization;

/**
 * Association between a system-indexed Entities and Language Code
 *
 * @package kanopi/components
 */
interface TranslationIndexItem {
	/**
	 * System indexed identifier for the entity
	 *
	 * @return int
	 */
	public function entityIdentifier(): int;

	/**
	 * ISO 639-1 Language code for the entity
	 *
	 * @return string
	 */
	public function languageCode(): string;
}
