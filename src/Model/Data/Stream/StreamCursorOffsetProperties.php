<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream properties
 *  - Timestamps and lengths are considered 0, or irrelevant, for cursor based streams
 *
 * @package kanopi/components
 */
class StreamCursorOffsetProperties implements StreamCursorProperties {
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
	public function readTimestamp(): int {
		return 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSameStream( IStreamProperties $_comparison ): bool {
		return $this->lastModifiedTimestamp() === $_comparison->lastModifiedTimestamp()
			&& $this->length() === $_comparison->length()
			&& $this->uri() === $_comparison->uri();
	}

	/**
	 * {@inheritDoc}
	 */
	public function lastModifiedTimestamp(): int {
		return 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function length(): int {
		return 0;
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
	public function completeStream(): void {
		$this->isStreamComplete = true;
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
	public function updateNextOffset( string $_offset ): void {
		$this->nextOffset = $_offset;
	}
}
