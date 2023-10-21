<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;

/**
 * Batch configuration for import/update processes
 *
 * @package kanopi/components
 */
interface IStreamBatchConfiguration extends IIndexedEntity {
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
	 * Ending stream index for current batch
	 *
	 * @return int
	 */
	public function endIndex(): int;

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
	 * Read the current entity batch
	 *
	 * @param array $_source Incoming batch data
	 *
	 * @return array
	 */
	public function readCurrentBatch( array $_source ): array;

	/**
	 * Properties of the target batched stream
	 *
	 * @return IStreamProperties
	 */
	public function streamProperties(): IStreamProperties;

	/**
	 * Starting stream index for current batch
	 *
	 * @return int
	 */
	public function startIndex(): int;

	/**
	 * Maximum number of batches
	 *
	 * @return int
	 */
	public function totalBatches(): int;

	/**
	 * Update a given batch
	 *
	 * @param int $_batch_number Batch index to use
	 *
	 * @return void
	 */
	public function updateBatch( int $_batch_number ): void;

	/**
	 * Update the stream properties associated with this batch process
	 *
	 * @param IStreamProperties $_properties Stream properties to process
	 *
	 * @return void
	 */
	public function updateStreamProperties( IStreamProperties $_properties ): void;
}
