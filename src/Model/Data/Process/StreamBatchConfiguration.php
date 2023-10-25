<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;

/**
 * Stream batch configuration implementation
 *
 * @package kanopi/components
 */
class StreamBatchConfiguration implements IStreamBatchConfiguration {
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
	 * @var array
	 */
	protected array $processedBatches = [];
	/**
	 * @var IStreamProperties|null
	 */
	protected ?IStreamProperties $streamProperties;
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
	public function readCurrentBatch( array $_source ): array {
		return array_slice(
			$_source,
			$this->startIndex(),
			$this->batchSize()
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function startIndex(): int {
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
			'Index End'                 => $this->endIndex(),
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
	public function endIndex(): int {
		return $this->currentBatch() === $this->totalBatches()
			? $this->streamProperties()->length() - 1
			: ( 0 < $this->currentBatch() ? $this->startIndex() + $this->batchSize() - 1 : 0 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function streamProperties(): IStreamProperties {
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
	public function updateBatch( int $_batch_number ): void {
		if ( 1 > $_batch_number || $this->totalBatches() < $_batch_number ) {
			return;
		}

		$this->processedBatches[ $_batch_number ] = true;
		$this->currentBatch                       = null;
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
	public function updateStreamProperties( IStreamProperties $_properties ): void {
		$currentLength          = ! empty( $this->streamProperties ) ? $this->streamProperties->length() : 0;
		$this->streamProperties = $_properties;

		/**
		 * If the total number of entities to process changes, reset the batch tracking array
		 */
		if ( $currentLength !== $this->streamProperties->length() ) {
			$this->resetBatches();
		}
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
		$this->currentBatch = null;
	}
}
