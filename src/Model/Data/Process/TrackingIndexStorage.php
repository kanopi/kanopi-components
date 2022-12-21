<?php

namespace Kanopi\Components\Model\Data\Process;

use Kanopi\Components\Model\Data\IIndexedEntity;

class TrackingIndexStorage implements IIndexedEntity {
	/**
	 * @var int
	 */
	protected int $_indexIdentifier = 0;

	/**
	 * @var array
	 */
	protected array $_trackingIndex = [];

	/**
	 * @var string
	 */
	protected string $_uniqueIdentifier = '';

	/**
	 * @var string
	 */
	protected string $_version;

	public function __construct(
		string $_unique_identifier,
		array $_index
	) {
		$this->_trackingIndex    = $_index;
		$this->_uniqueIdentifier = $_unique_identifier;
		$this->_version          = gmdate( 'U', time() );
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
	function systemEntityName(): string {
		return 'option';
	}

	/**
	 * @inheritDoc
	 */
	function systemTransform(): array {
		return $this->_trackingIndex;
	}

	/**
	 * @inheritDoc
	 */
	function uniqueIdentifier(): string {
		return $this->_uniqueIdentifier;
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
	function version(): string {
		return $this->_version;
	}
}
