<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;

/**
 * Batch configuration for import/update processes using a cursor to track the next start index
 *
 * @package kanopi/components
 */
interface StreamCursorBatchConfiguration extends IIndexedEntity {
	/**
	 * Maximum size of any given batch
	 *
	 * @return int
	 */
	public function batchSize(): int;

	/**
	 * Current batch to process
	 *
	 * @return int
	 */
	public function currentBatch(): int;

	/**
	 * Check to see if the set of stream batches is complete
	 *
	 * @return bool
	 */
	public function isStreamComplete(): bool;

	/**
	 * Set of processed batch indicators
	 *
	 * @return array
	 */
	public function processedBatches(): array;

	/**
	 * Starting stream index for current batch
	 *
	 * @return string
	 */
	public function startIndex(): string;

	/**
	 * Maximum number of batches
	 *
	 * @return int
	 */
	public function totalBatches(): int;

	/**
	 * Update a given batch
	 *
	 * @param int    $_batchNumber    Batch index to use
	 * @param string $_nextStartIndex Next batch starting index
	 *
	 * @return void
	 */
	public function updateBatch( int $_batchNumber, string $_nextStartIndex ): void;
}
