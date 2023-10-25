<?php

namespace Kanopi\Components\Model\Data;

/**
 * Entity used in collections with the following properties:
 *  - Unique cross/multi-system identifier string
 *  - Unique integer based index
 *  - Version string to compare matching Entities for changes
 *
 * @package kanopi/components
 */
interface IIndexedEntity {
	/**
	 * Local index identifier
	 *
	 * @return int
	 */
	public function indexIdentifier(): int;

	/**
	 * Name of the underlying system content entity
	 *
	 * @return string
	 */
	public function systemEntityName(): string;

	/**
	 * Transform to a system compatible entity array
	 *    - Target system dependent, structure per implementation
	 *
	 * @return array
	 */
	public function systemTransform(): array;

	/**
	 * Cross-system unique identifier (hash, etc)
	 *
	 * @return string
	 */
	public function uniqueIdentifier(): string;

	/**
	 * Chainable update the index identifier, useful for create/update operations
	 *
	 * @param int $_index Index to assign
	 *
	 * @return IIndexedEntity
	 */
	public function updateIndexIdentifier( int $_index ): IIndexedEntity;

	/**
	 * Version of entity, compare between version to determine change
	 *
	 * @return string
	 */
	public function version(): string;
}
