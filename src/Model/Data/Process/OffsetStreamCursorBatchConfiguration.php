<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;
use Kanopi\Components\Model\Data\Stream\StreamCursorPagination;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;
use Kanopi\Components\Transformers\Arrays;

/**
 * Stream cursor batch configuration implementation
 *  - Internal properties are all serializable to store in the Options table
 *
 * @package kanopi/components
 */
class OffsetStreamCursorBatchConfiguration implements StreamCursorBatchConfiguration {
	/**
	 * @var int
	 */
	private int $batchSize;
	/**
	 * @var int
	 */
	private int $indexIdentifier = 0;
	/**
	 * @var int
	 */
	private int $maximumEntities;
	/**
	 * @var array
	 */
	private array $processedBatches = [];
	/**
	 * @var StreamCursorProperties|null
	 */
	private ?StreamCursorProperties $currentProperties = null;

	/**
	 * Stream batch constructor
	 *
	 * @param int $_batchSize       Size of each batch
	 * @param int $_maximumEntities Maximum entities to read across all batches
	 */
	public function __construct( int $_batchSize, int $_maximumEntities ) {
		$this->batchSize       = $_batchSize;
		$this->maximumEntities = $_maximumEntities;
	}

	/**
	 * {@inheritDoc}
	 */
	public function currentPage(): StreamCursorPagination {
		$currentPage = new StreamCursorPagination();

		$currentPage->pageSize = $this->batchSize;
		$currentPage->offset   = $this->currentProperties?->offsetNext();
		$currentPage->maxSize  = $this->maximumEntities;

		return $currentPage;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isStreamComplete(): bool {
		return $this->currentProperties?->isStreamCursorComplete() ?? false;
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
	public function processedBatches(): array {
		return $this->processedBatches;
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
		$batchDisplay = Arrays::fresh();

		/**
		 * @var StreamCursorProperties $batchProperties
		 */
		foreach ( $this->processedBatches as $batchProperties ) {
			$start = empty( $batchProperties->offsetStart() ) ? '-' : $batchProperties->offsetStart();
			$next  = empty( $batchProperties->offsetNext() ) ? '-' : $batchProperties->offsetNext();
			$batchDisplay->append(
				[
					'Stream Uri'   => $batchProperties->uri(),
					'Start Offset' => $start,
					'Next Offset'  => $next,
					'Complete?'    => $batchProperties->isStreamCursorComplete() ? 'Yes' : 'No',
				]
			);
		}

		return $batchDisplay->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->currentProperties?->offsetNext() ?? '';
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
	public function processCurrentBatch( StreamCursorProperties $_properties ): void {
		$this->currentProperties  = $_properties;
		$this->processedBatches[] = $_properties;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateIndexIdentifier( int $_index ): IIndexedEntity {
		$this->indexIdentifier = $_index;

		return $this;
	}
}
