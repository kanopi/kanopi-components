<?php

namespace Kanopi\Components\Model\Data\Localization;

/**
 * Association between a system-indexed Entity and Language Code
 *
 * @package kanopi/components
 */
class TranslationEntityIndexItem implements TranslationIndexItem {
	/**
	 * Entity identifier
	 *
	 * @var int
	 */
	protected int $entityIdentifier;
	/**
	 * ISO 639-1 Language code for the entity
	 *
	 * @var string
	 */
	protected string $languageCode;

	/**
	 * Build an index association for translated entities
	 *
	 * @param int    $_entityIdentifier Entity identifier
	 * @param string $_languageCode     ISO 639-1 language code
	 */
	public function __construct( int $_entityIdentifier, string $_languageCode ) {
		$this->entityIdentifier = $_entityIdentifier;
		$this->languageCode     = $_languageCode;
	}

	/**
	 * {@inheritDoc}
	 */
	public function entityIdentifier(): int {
		return $this->entityIdentifier;
	}

	/**
	 * {@inheritDoc}
	 */
	public function languageCode(): string {
		return $this->languageCode;
	}
}
