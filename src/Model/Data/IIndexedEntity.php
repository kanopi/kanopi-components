<?php

namespace Kanopi\Components\Model\Data;

interface IIndexedEntity {
	/**
	 * Local index identifier
	 *
	 * @return int
	 */
	function indexIdentifier(): int;

	/**
	 * Name of the underlying system content entity
	 *
	 * @return string
	 */
	function systemEntityName(): string;

	/**
	 * Transform to a system compatible entity array
	 * 	- Target system dependent, structure per implementation
	 *
	 * @return array
	 */
	function systemTransform() : array;

	/**
	 * Cross-system unique identifier (hash, etc)
	 *
	 * @return string
	 */
	function uniqueIdentifier(): string;

	/**
	 * Chainable update the index identifier, useful for create/update operations
	 *
	 * @param int $_index
	 *
	 * @return IIndexedEntity
	 */
	function updateIndexIdentifier( int $_index ): IIndexedEntity;

	/**
	 * Version of entity, compare between version to determine change
	 *
	 * @return string
	 */
	function version(): string;
}