<?php

namespace Kanopi\Components\Model\Data\Localization;

/**
 * Localized text content tagged with ISO 639-1 language code
 *
 * @package kanopi/components
 */
class LocalizedTextAssociation implements LocalizedText {
	/**
	 * ISO 639-1 language code
	 *
	 * @var string
	 */
	protected string $code;
	/**
	 * Text contents
	 *
	 * @var string
	 */
	protected string $text;

	/**
	 * Build a localized text entity
	 *
	 * @param string $_text Text contents
	 * @param string $_code ISO 639-1 language code
	 */
	public function __construct( string $_text, string $_code ) {
		$this->text = trim( $_text );
		$this->code = trim( $_code );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function fromText( string $_text, string $_code ): LocalizedText {
		return new static( $_text, $_code );
	}

	/**
	 * {@inheritDoc}
	 */
	public function languageCode(): string {
		return $this->code;
	}

	/**
	 * {@inheritDoc}
	 */
	public function text(): string {
		return $this->text;
	}
}
