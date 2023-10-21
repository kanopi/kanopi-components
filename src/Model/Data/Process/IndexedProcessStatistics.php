<?php

namespace Kanopi\Components\Model\Data\Process;

/**
 * Statistics model for import/update processes
 *
 * @package kanopi/components
 */
class IndexedProcessStatistics implements IIndexedProcessStatistics {
	/**
	 * Amount of indexed entities created
	 *
	 * @var int
	 */
	protected int $created = 0;
	/**
	 * Indexed entities created
	 *
	 * @var array
	 */
	protected array $createdIndex = [];
	/**
	 * Amount of indexed entities deleted
	 *
	 * @var int
	 */
	protected int $deleted = 0;
	/**
	 * Indexed entities deleted
	 *
	 * @var array
	 */
	protected array $deletedIndex = [];
	/**
	 * Amount of total incoming entities
	 *
	 * @var int
	 */
	protected int $incomingTotal = 0;
	/**
	 * Amount of indexed entities skipped
	 *
	 * @var int
	 */
	protected int $skipped = 0;
	/**
	 * Indexed entities skipped
	 *
	 * @var array
	 */
	protected array $skippedIndex = [];
	/**
	 * Amount of indexed entities updated
	 *
	 * @var int
	 */
	protected int $updated = 0;
	/**
	 * Indexed entities updated
	 *
	 * @var array
	 */
	protected array $updatedIndex = [];

	/**
	 * {@inheritDoc}
	 */
	public function created( int $_identifier ): void {
		$this->increment_type( '_created', $_identifier );
	}

	/**
	 * Increment the count for a property type at a given identifier
	 *
	 * @param string $_type       Property type
	 * @param int    $_identifier Unique index identifier
	 *
	 * @return void
	 */
	protected function increment_type( string $_type, int $_identifier ): void {
		$property = "{$_type}_index";
		if ( ! isset( $this->{$property}[ $_identifier ] ) ) {
			$this->{$property}[ $_identifier ] = 0;
		}

		$this->{$property}[ $_identifier ] = $this->{$property}[ $_identifier ] + 1;
		$this->{$_type}                    = $this->{$_type} + 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createdAmount(): int {
		return $this->created;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createdIndex(): array {
		return $this->createdIndex;
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted( int $_identifier ): void {
		$this->increment_type( '_deleted', $_identifier );
	}

	/**
	 * {@inheritDoc}
	 */
	public function deletedAmount(): int {
		return $this->deleted;
	}

	/**
	 * {@inheritDoc}
	 */
	public function deletedIndex(): array {
		return $this->deletedIndex;
	}

	/**
	 * {@inheritDoc}
	 */
	public function skippedAmount(): int {
		return $this->skipped;
	}

	/**
	 * {@inheritDoc}
	 */
	public function skippedIndex(): array {
		return $this->skippedIndex;
	}

	/**
	 * {@inheritDoc}
	 */
	public function skipped( int $_identifier ): void {
		$this->increment_type( '_skipped', $_identifier );
	}

	/**
	 * {@inheritDoc}
	 */
	public function updated( int $_identifier ): void {
		$this->increment_type( '_updated', $_identifier );
	}

	/**
	 * {@inheritDoc}
	 */
	public function updatedAmount(): int {
		return $this->updated;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updatedIndex(): array {
		return $this->updatedIndex;
	}

	/**
	 * {@inheritDoc}
	 */
	public function incomingTotal( int $_total ): void {
		$this->incomingTotal = $_total;
	}

	/**
	 * {@inheritDoc}
	 */
	public function incomingTotalAmount(): int {
		return $this->incomingTotal;
	}

	/**
	 * {@inheritDoc}
	 */
	public function processedTotalAmount(): int {
		return $this->created + $this->skipped + $this->updated;
	}
}
