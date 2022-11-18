<?php

namespace Kanopi\Components\Model\Data;

interface IIndexedEntity {
	/**
	 * Name of the underlying system content entity
	 *
	 * @return string
	 */
	function entityName(): string;

	/**
	 * Local index identifier
	 *
	 * @return int
	 */
	function indexIdentifier(): int;

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
	 * Change to entities index identifier, useful for create/update operations
	 *
	 * @param int $_index
	 *
	 * @return void
	 */
	function updateIndexIdentifier( int $_index ): void;

	/**
	 * Version of entity, compare between version to determine change
	 *
	 * @return string
	 */
	function version(): string;
}