<?php

namespace Kanopi\Components\Repositories\Localization;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\IIndexedEntity;

/**
 * Localization provider entity data interface
 *
 * @package kanopi/components
 */
interface LocalizationEntity {
	/**
	 * Read the set of translated entity associations
	 *
	 * @param IIndexedEntity $_entity Entity
	 *
	 * @return EntityIterator Set of Translation Entity associations
	 */
	public function readAssociatedEntities( IIndexedEntity $_entity ): EntityIterator;

	/**
	 * Read the language code for an entity by system identifier
	 *
	 * @param int $_entityId Entity identifier
	 *
	 * @return string ISO 639-1 Language code
	 */
	public function readEntityLanguage( int $_entityId ): string;

	/**
	 * Update the set of translated entity associations
	 *
	 * @param EntityIterator $_entities Set of Associated Entities*
	 */
	public function updateAssociatedEntities( EntityIterator $_entities ): void;

	/**
	 * Read the language code for an entity by system identifier
	 *
	 * @param int    $_entityId     Entity identifier
	 * @param string $_languageCode ISO 639-1 Language code
	 */
	public function updateEntityLanguage( int $_entityId, string $_languageCode ): void;
}