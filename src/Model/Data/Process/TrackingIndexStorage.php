<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;

/**
 * Process tracking by integer based (internal/system) entity index
 *
 * @package kanopi/components
 */
class TrackingIndexStorage implements IIndexedEntity {
	/**
	 * @var int
	 */
	protected int $indexIdentifier = 0;
	/**
	 * @var array
	 */
	protected array $trackingIndex = [];
	/**
	 * @var string
	 */
	protected string $uniqueIdentifier = '';
	/**
	 * @var string
	 */
	protected string $version;

	/**
	 * @param string $_unique_identifier Unique identifier for the process
	 * @param array  $_index             Initial system index to use in tracking
	 */
	public function __construct(
		string $_unique_identifier,
		array $_index
	) {
		$this->trackingIndex    = $_index;
		$this->uniqueIdentifier = $_unique_identifier;
		$this->version          = gmdate( 'U', time() );
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
	public function systemEntityName(): string {
		return 'option';
	}

	/**
	 * {@inheritDoc}
	 */
	public function systemTransform(): array {
		return $this->trackingIndex;
	}

	/**
	 * {@inheritDoc}
	 */
	public function uniqueIdentifier(): string {
		return $this->uniqueIdentifier;
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
	public function version(): string {
		return $this->version;
	}
}
