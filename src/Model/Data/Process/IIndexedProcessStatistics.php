<?php

namespace Kanopi\Components\Model\Data\Process;

interface IIndexedProcessStatistics {
	/**
	 * Mark the provided identifier as created
	 *
	 * @param int $_identifier
	 *
	 * @return void
	 */
	function created( int $_identifier ): void;

	/**
	 * Amount of indexed entities created
	 *
	 * @return int
	 */
	function createdAmount(): int;

	/**
	 * Index of entities created
	 *
	 * @return array
	 */
	function createdIndex(): array;

	/**
	 * Mark the provided identifier as deleted
	 *
	 * @param int $_identifier
	 *
	 * @return void
	 */
	function deleted( int $_identifier ): void;

	/**
	 * Amount of indexed entities deleted
	 *
	 * @return int
	 */
	function deletedAmount(): int;

	/**
	 * Index of entities deleted
	 *
	 * @return array
	 */
	function deletedIndex(): array;

	/**
	 * Set amount of all incoming entities
	 *
	 * @param int $_total
	 *
	 * @return void
	 */
	function incomingTotal( int $_total ): void;

	/**
	 * Amount of all entities
	 *
	 * @return int
	 */
	function incomingTotalAmount(): int;

	/**
	 * Amount of entities processed in the entire run (excludes deletions)
	 *
	 * @return int
	 */
	function processedTotalAmount(): int;

	/**
	 * Amount of indexed entities skipped
	 *
	 * @return int
	 */
	function skippedAmount(): int;

	/**
	 * Index of entities skipped
	 *
	 * @return array
	 */
	function skippedIndex(): array;

	/**
	 * Mark the provided identifier as skipped
	 *
	 * @param int $_identifier
	 *
	 * @return void
	 */
	function skipped( int $_identifier ): void;

	/**
	 * Mark the provided identifier as updated
	 *
	 * @param int $_identifier
	 *
	 * @return void
	 */
	function updated( int $_identifier ): void;

	/**
	 * Amount of indexed entities updated
	 *
	 * @return int
	 */
	function updatedAmount(): int;

	/**
	 * Index of entities updated
	 *
	 * @return array
	 */
	function updatedIndex(): array;
}
