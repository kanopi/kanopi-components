<?php

namespace Kanopi\Components\Model\Data\Process;

class IndexedProcessStatistics implements IIndexedProcessStatistics {
	/**
	 * Amount of indexed entities created
	 *
	 * @var int
	 */
	protected int $_created = 0;

	/**
	 * Indexed entities created
	 *
	 * @var array
	 */
	protected array $_created_index = [];

	/**
	 * Amount of indexed entities deleted
	 *
	 * @var int
	 */
	protected int $_deleted = 0;

	/**
	 * Indexed entities deleted
	 *
	 * @var array
	 */
	protected array $_deleted_index = [];

	/**
	 * Amount of total incoming entities
	 *
	 * @var int
	 */
	protected int $_incoming_total = 0;

	/**
	 * Amount of indexed entities skipped
	 *
	 * @var int
	 */
	protected int $_skipped = 0;

	/**
	 * Indexed entities skipped
	 *
	 * @var array
	 */
	protected array $_skipped_index = [];

	/**
	 * Amount of indexed entities updated
	 *
	 * @var int
	 */
	protected int $_updated = 0;

	/**
	 * Indexed entities updated
	 *
	 * @var array
	 */
	protected array $_updated_index = [];

	/**
	 * @inheritDoc
	 */
	public function created( int $_identifier ): void {
		$this->increment_type( '_created', $_identifier );
	}

	/**
	 * @inheritDoc
	 */
	public function createdAmount(): int {
		return $this->_created;
	}

	/**
	 * @inheritDoc
	 */
	public function createdIndex(): array {
		return $this->_created_index;
	}

	/**
	 * @inheritDoc
	 */
	public function deleted( int $_identifier ): void {
		$this->increment_type( '_deleted', $_identifier );
	}

	/**
	 * @inheritDoc
	 */
	public function deletedAmount(): int {
		return $this->_deleted;
	}

	/**
	 * @inheritDoc
	 */
	public function deletedIndex(): array {
		return $this->_deleted_index;
	}

	/**
	 * Increment the count for a property type at a given identifier
	 *
	 * @param string $_type
	 * @param int    $_identifier
	 *
	 * @return void
	 */
	protected function increment_type( string $_type, int $_identifier ): void {
		$property = "{$_type}_index";
		if ( !isset( $this->{$property}[ $_identifier ] ) ) {
			$this->{$property}[ $_identifier ] = 0;
		}

		$this->{$property}[ $_identifier ] = $this->{$property}[ $_identifier ] + 1;
		$this->{$_type}                    = $this->{$_type} + 1;
	}

	/**
	 * @inheritDoc
	 */
	public function skippedAmount(): int {
		return $this->_skipped;
	}

	/**
	 * @inheritDoc
	 */
	public function skippedIndex(): array {
		return $this->_skipped_index;
	}

	/**
	 * @inheritDoc
	 */
	public function skipped( int $_identifier ): void {
		$this->increment_type( '_skipped', $_identifier );
	}

	/**
	 * @inheritDoc
	 */
	public function updated( int $_identifier ): void {
		$this->increment_type( '_updated', $_identifier );
	}

	/**
	 * @inheritDoc
	 */
	public function updatedAmount(): int {
		return $this->_updated;
	}

	/**
	 * @inheritDoc
	 */
	public function updatedIndex(): array {
		return $this->_updated_index;
	}

	/**
	 * @inheritDoc
	 */
	function incomingTotal( int $_total ): void {
		$this->_incoming_total = $_total;
	}

	/**
	 * @inheritDoc
	 */
	function incomingTotalAmount(): int {
		return $this->_incoming_total;
	}

	/**
	 * @inheritDoc
	 */
	function processedTotalAmount(): int {
		return $this->_created + $this->_skipped + $this->_updated;
	}
}
