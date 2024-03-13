<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream properties for offset based cursors
 *  - Stream only provides the next offset, no other pagination properties
 *
 * @package kanopi/components
 */
class OffsetStreamCursorProperties implements StreamCursorProperties {
	/**
	 * Whether the stream is complete
	 *
	 * @var bool
	 */
	private bool $isStreamComplete = false;
	/**
	 * Next offset
	 *
	 * @var string|null
	 */
	private ?string $nextOffset = null;
	/**
	 * Number of entities from the incoming stream
	 *
	 * @var int
	 */
	private int $entityCount = 0;
	/**
	 * Starting offset, if provided
	 *
	 * @var string|null
	 */
	private ?string $startingOffset;
	/**
	 * @var string
	 */
	protected string $uri;

	/**
	 * @param string      $_uri            Source URI
	 * @param string|null $_startingOffset Initial/starting cursor offset
	 */
	public function __construct(
		string $_uri,
		?string $_startingOffset = null
	) {
		$this->startingOffset = $_startingOffset;
		$this->uri            = $_uri;
	}

	/**
	 * {@inheritDoc}
	 */
	public function completeStream(): void {
		$this->isStreamComplete = true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function entityCount(): int {
		return $this->entityCount;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isStreamCursorComplete(): bool {
		return $this->isStreamComplete;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetStart(): ?string {
		return $this->startingOffset;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetNext(): ?string {
		return $this->nextOffset;
	}

	/**
	 * {@inheritDoc}
	 */
	public function uri(): string {
		return $this->uri;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateStream( string $_nextOffset, int $_entityCount ): void {
		$this->nextOffset  = $_nextOffset;
		$this->entityCount = $_entityCount;
	}
}
