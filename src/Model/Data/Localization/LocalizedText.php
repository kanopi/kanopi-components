<?php

namespace Kanopi\Components\Model\Data\Localization;

/**
 * Localized text content tagged with ISO 639-1 language code
 *
 * @package kanopi/components
 */
interface LocalizedText {
	/**
	 * Fluent interface to build a localized text entity
	 *
	 * @param string $_text Text contents
	 * @param string $_code ISO 639-1 language code
	 *
	 * @return LocalizedText
	 */
	public static function fromText( string $_text, string $_code ): LocalizedText;

	/**
	 * ISO 639-1 language code
	 *
	 * @return string
	 */
	public function languageCode(): string;

	/**
	 * Text contents
	 *
	 * @return string
	 */
	public function text(): string;
}
