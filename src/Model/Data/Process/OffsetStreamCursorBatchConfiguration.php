<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;

/**
 * Stream cursor batch configuration implementation
 *
 * @package kanopi/components
 */
class OffsetStreamCursorBatchConfiguration implements StreamCursorBatchConfiguration {
	/**
	 * @var int
	 */
	protected int $batchSize = 0;
	/**
	 * @var int|null
	 */
	protected ?int $currentBatch = null;
	/**
	 * @var int
	 */
	protected int $indexIdentifier = 0;
	/**
	 * @var string
	 */
	protected string $nextStartIndex = '';
	/**
	 * @var array
	 */
	protected array $processedBatches = [];
	/**
	 * @var StreamCursorProperties|null
	 */
	protected ?StreamCursorProperties $streamProperties;
	/**
	 * @var int|null
	 */
	protected ?int $totalBatches = null;

	/**
	 * Stream batch constructor
	 *
	 * @param int $_batchSize Size of each batch
	 */
	public function __construct( int $_batchSize ) {
		$this->batchSize = $_batchSize;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isStreamComplete(): bool {
		return 0 === $this->currentBatch();
	}

	/**
	 * {@inheritDoc}
	 */
	public function currentBatch(): int {
		return $this->currentBatch ?? $this->calculateNextBatch();
	}

	/**
	 * Find the next batch in the stream
	 *    - Set to 0 when done or no batches available
	 *
	 * @return int
	 */
	protected function calculateNextBatch(): int {
		$nextBatch = 0;

		if ( ! empty( $this->processedBatches ) ) {
			$total = $this->totalBatches();
			for ( $batch = 1; $batch <= $total; $batch++ ) {
				if ( false === $this->processedBatches[ $batch ] ) {
					$nextBatch = $batch;
					break;
				}
			}
		}

		return $nextBatch;
	}

	/**
	 * {@inheritDoc}
	 */
	public function totalBatches(): int {
		return $this->totalBatches ?? $this->calculateTotalBatches();
	}

	/**
	 * Calculate and cache the total amount of batches
	 *
	 * @return int
	 */
	protected function calculateTotalBatches(): int {
		$this->totalBatches = 0 < $this->streamProperties->length() && 0 < $this->batchSize
			? ceil( $this->streamProperties->length() / $this->batchSize )
			: 1;

		return $this->totalBatches;
	}

	/**
	 * {@inheritDoc}
	 */
	public function indexIdentifier(): int {
		return $this->indexIdentifier;
	}

	/**
	 * {@inheritDoc}
	 */
	public function startIndex(): string {
		return 1 < $this->currentBatch() && $this->totalBatches() >= $this->currentBatch()
			? ( ( $this->currentBatch() - 1 ) * $this->batchSize() )
			: 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function batchSize(): int {
		return $this->batchSize;
	}

	/**
	 * {@inheritDoc}
	 */
	public function systemEntityName(): string {
		return 'option';
	}

	/**
	 * {@inheritDoc}
	 */
	public function systemTransform(): array {
		return [
			'Batch Size'                => $this->batchSize(),
			'Current Batch'             => $this->currentBatch(),
			'Index Start'               => $this->startIndex(),
			'Processed Batches'         => $this->processedBatches(),
			'Stream Last Modified Date' => gmdate( 'm-d-Y H:i:s', $this->version() ),
			'Stream Length'             => $this->streamProperties()->length(),
			'Stream Uri'                => $this->streamProperties()->uri(),
			'Total Batches'             => $this->totalBatches(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function streamProperties(): StreamCursorProperties {
		return $this->streamProperties;
	}

	/**
	 * {@inheritDoc}
	 */
	public function processedBatches(): array {
		return $this->processedBatches;
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->streamProperties->lastModifiedTimestamp();
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifier(): string {
		return $this->uniqueIdentifier();
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateBatch( int $_batchNumber, string $_nextStartIndex ): void {
		if ( 1 > $_batchNumber || $this->totalBatches() < $_batchNumber ) {
			return;
		}

		$this->processedBatches[ $_batchNumber ] = true;
		$this->nextStartIndex                    = $_nextStartIndex;
		$this->currentBatch                      = null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateIndexIdentifier( int $_index ): IIndexedEntity {
		$this->indexIdentifier = $_index;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateStreamProperties( StreamCursorProperties $_properties ): void {
		$this->streamProperties = $_properties;
	}

	/**
	 * Reset the processed batch tracking array
	 *
	 * @return void
	 */
	public function resetBatches(): void {
		$this->processedBatches = [];
		$total                  = $this->totalBatches();
		for ( $batch = 1; $batch <= $total; $batch++ ) {
			$this->processedBatches[ $batch ] = false;
		}
		$this->currentBatch   = null;
		$this->nextStartIndex = '';
	}
}
