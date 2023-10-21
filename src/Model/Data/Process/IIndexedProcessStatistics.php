<?php

namespace Kanopi\Components\Model\Data\Process;

/**
 * Statistics model for import/update processes
 *
 * @package kanopi/components
 */
interface IIndexedProcessStatistics {
	/**
	 * Mark the provided identifier as created
	 *
	 * @param int $_identifier Processed entity index identifier
	 *
	 * @return void
	 */
	public function created( int $_identifier ): void;

	/**
	 * Amount of indexed entities created
	 *
	 * @return int
	 */
	public function createdAmount(): int;

	/**
	 * Index of entities created
	 *
	 * @return array
	 */
	public function createdIndex(): array;

	/**
	 * Mark the provided identifier as deleted
	 *
	 * @param int $_identifier Processed entity index identifier
	 *
	 * @return void
	 */
	public function deleted( int $_identifier ): void;

	/**
	 * Amount of indexed entities deleted
	 *
	 * @return int
	 */
	public function deletedAmount(): int;

	/**
	 * Index of entities deleted
	 *
	 * @return array
	 */
	public function deletedIndex(): array;

	/**
	 * Set amount of all incoming entities
	 *
	 * @param int $_total Total entities to process
	 *
	 * @return void
	 */
	public function incomingTotal( int $_total ): void;

	/**
	 * Amount of all entities
	 *
	 * @return int
	 */
	public function incomingTotalAmount(): int;

	/**
	 * Amount of entities processed in the entire run (excludes deletions)
	 *
	 * @return int
	 */
	public function processedTotalAmount(): int;

	/**
	 * Amount of indexed entities skipped
	 *
	 * @return int
	 */
	public function skippedAmount(): int;

	/**
	 * Index of entities skipped
	 *
	 * @return array
	 */
	public function skippedIndex(): array;

	/**
	 * Mark the provided identifier as skipped
	 *
	 * @param int $_identifier Processed entity index identifier
	 *
	 * @return void
	 */
	public function skipped( int $_identifier ): void;

	/**
	 * Mark the provided identifier as updated
	 *
	 * @param int $_identifier Processed entity index identifier
	 *
	 * @return void
	 */
	public function updated( int $_identifier ): void;

	/**
	 * Amount of indexed entities updated
	 *
	 * @return int
	 */
	public function updatedAmount(): int;

	/**
	 * Index of entities updated
	 *
	 * @return array
	 */
	public function updatedIndex(): array;
}
