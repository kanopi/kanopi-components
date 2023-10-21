<?php

namespace Kanopi\Components\Model\Data\Localization;

/**
 * Entity to consolidate translation data and any exceptions
 *
 * @package kanopi/components
 */
interface TranslationText {
	/**
	 * Message from any exception
	 *
	 * @return string
	 */
	public function exceptionMessage(): string;

	/**
	 * If a translation exception occurred
	 *
	 * @return bool
	 */
	public function hasException(): bool;

	/**
	 * The original text
	 *
	 * @return LocalizedText
	 */
	public function originalText(): LocalizedText;

	/**
	 * The translated text
	 *
	 * @return LocalizedText
	 */
	public function translatedText(): LocalizedText;
}
