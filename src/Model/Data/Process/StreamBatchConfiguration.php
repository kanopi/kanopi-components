<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;

class StreamBatchConfiguration implements IStreamBatchConfiguration {
	/**
	 * @var int
	 */
	protected int $_batchSize = 0;

	/**
	 * @var int|null
	 */
	protected ?int $_currentBatch = null;

	/**
	 * @var int
	 */
	protected int $_indexIdentifier = 0;

	/**
	 * @var array
	 */
	protected array $_processedBatches = [];

	/**
	 * @var IStreamProperties|null
	 */
	protected ?IStreamProperties $_streamProperties;

	/**
	 * @var int|null
	 */
	protected ?int $_totalBatches = null;

	public function __construct( int $_batchSize ) {
		$this->_batchSize = $_batchSize;
	}

	/**
	 * @inheritDoc
	 */
	function batchSize(): int {
		return $this->_batchSize;
	}

	/**
	 * Find the next batch in the stream
	 *    - Set to 0 when done or no batches available
	 *
	 * @return int
	 */
	protected function calculateNextBatch(): int {
		$nextBatch = 0;

		if ( !empty( $this->_processedBatches ) ) {
			for ( $batch = 1; $batch <= $this->totalBatches(); $batch++ ) {
				if ( false === $this->_processedBatches[ $batch ] ) {
					$nextBatch = $batch;
					break;
				}
			}
		}

		return $nextBatch;
	}

	/**
	 * Calculate and cache the total amount of batches
	 *
	 * @return int
	 */
	protected function calculateTotalBatches(): int {
		$this->_totalBatches = 0 < $this->_streamProperties->length() && 0 < $this->_batchSize
			? ceil( $this->_streamProperties->length() / $this->_batchSize )
			: 1;

		return $this->_totalBatches;
	}

	/**
	 * @inheritDoc
	 */
	function currentBatch(): int {
		return $this->_currentBatch ?? $this->calculateNextBatch();
	}

	/**
	 * @inheritDoc
	 */
	function endIndex(): int {
		return $this->currentBatch() === $this->totalBatches()
			? $this->streamProperties()->length() - 1
			: ( 0 < $this->currentBatch() ? $this->startIndex() + $this->batchSize() - 1 : 0 );
	}

	/**
	 * @inheritDoc
	 */
	function isStreamComplete(): bool {
		return 0 === $this->currentBatch();
	}

	/**
	 * @inheritDoc
	 */
	function indexIdentifier(): int {
		return $this->_indexIdentifier;
	}

	/**
	 * @inheritDoc
	 */
	function processedBatches(): array {
		return $this->_processedBatches;
	}

	/**
	 * @inheritDoc
	 */
	function readCurrentBatch( array $_source ): array {
		return array_slice(
			$_source,
			$this->startIndex(),
			$this->batchSize()
		);
	}

	/**
	 * Reset the processed batch tracking array
	 *
	 * @return void
	 */
	function resetBatches(): void {
		$this->_processedBatches = [];
		for ( $batch = 1; $batch <= $this->totalBatches(); $batch++ ) {
			$this->_processedBatches[ $batch ] = false;
		}
		$this->_currentBatch = null;
	}

	/**
	 * @inheritDoc
	 */
	function startIndex(): int {
		return 1 < $this->currentBatch() && $this->totalBatches() >= $this->currentBatch()
			? ( ( $this->currentBatch() - 1 ) * $this->batchSize() )
			: 0;
	}

	/**
	 * @inheritDoc
	 */
	function streamProperties(): IStreamProperties {
		return $this->_streamProperties;
	}

	/**
	 * @inheritDoc
	 */
	function systemEntityName(): string {
		return 'option';
	}

	/**
	 * @inheritDoc
	 */
	function systemTransform(): array {
		return [
			'Batch Size'                => $this->batchSize(),
			'Current Batch'             => $this->currentBatch(),
			'Index Start'               => $this->startIndex(),
			'Index End'                 => $this->endIndex(),
			'Processed Batches'         => $this->processedBatches(),
			'Stream Last Modified Date' => gmdate( "m-d-Y H:i:s", $this->version() ),
			'Stream Length'             => $this->streamProperties()->length(),
			'Stream Uri'                => $this->streamProperties()->uri(),
			'Total Batches'             => $this->totalBatches()
		];
	}

	/**
	 * @inheritDoc
	 */
	function totalBatches(): int {
		return $this->_totalBatches ?? $this->calculateTotalBatches();
	}

	/**
	 * @inheritDoc
	 */
	function uniqueIdentifier(): string {
		return $this->uniqueIdentifier();
	}

	/**
	 * @inheritDoc
	 */
	function updateBatch( int $_batch_number ): void {
		if ( 1 > $_batch_number || $this->totalBatches() < $_batch_number ) {
			return;
		}

		$this->_processedBatches[ $_batch_number ] = true;
		$this->_currentBatch                       = null;
	}

	/**
	 * @inheritDoc
	 */
	function updateIndexIdentifier( int $_index ): IIndexedEntity {
		$this->_indexIdentifier = $_index;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function updateStreamProperties( IStreamProperties $_properties ): void {
		$currentLength           = !empty( $this->_streamProperties ) ? $this->_streamProperties->length() : 0;
		$this->_streamProperties = $_properties;

		/**
		 * If the total number of entities to process changes, reset the batch tracking array
		 */
		if ( $currentLength !== $this->_streamProperties->length() ) {
			$this->resetBatches();
		}
	}

	/**
	 * @inheritDoc
	 */
	function version(): string {
		return $this->_streamProperties->lastModifiedTimestamp();
	}
}
