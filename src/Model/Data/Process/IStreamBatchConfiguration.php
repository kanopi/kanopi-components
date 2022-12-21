<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;

interface IStreamBatchConfiguration extends IIndexedEntity {
	/**
	 * Maximum size of any given batch
	 *
	 * @return int
	 */
	function batchSize(): int;

	/**
	 * Current batch to process
	 *
	 * @return int
	 */
	function currentBatch(): int;

	/**
	 * Ending stream index for current batch
	 *
	 * @return int
	 */
	function endIndex(): int;

	/**
	 * Check to see if the set of stream batches is complete
	 *
	 * @return bool
	 */
	function isStreamComplete(): bool;

	/**
	 * Set of processed batch indicators
	 *
	 * @return array
	 */
	function processedBatches(): array;

	/**
	 * Read the current entity batch
	 *
	 * @param array $_source
	 *
	 * @return array
	 */
	function readCurrentBatch( array $_source ): array;

	/**
	 * Properties of the target batched stream
	 *
	 * @return IStreamProperties
	 */
	function streamProperties(): IStreamProperties;

	/**
	 * Starting stream index for current batch
	 *
	 * @return int
	 */
	function startIndex(): int;

	/**
	 * Maximum number of batches
	 *
	 * @return int
	 */
	function totalBatches(): int;

	/**
	 * Update a given batch
	 *
	 * @param int   $_batch_number
	 *
	 * @return void
	 */
	function updateBatch( int $_batch_number ): void;

	/**
	 * Update the stream properties associated with this batch process
	 *
	 * @param IStreamProperties $_properties
	 *
	 * @return void
	 */
	function updateStreamProperties( IStreamProperties $_properties ): void;
}
