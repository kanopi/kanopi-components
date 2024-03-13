<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream properties with cursors/offsets
 *
 * @package kanopi/components
 */
interface StreamCursorProperties {
	/**
	 * Mark the stream cursor complete
	 *
	 * @return void
	 */
	public function completeStream(): void;

	/**
	 * Whether the stream cursor is complete
	 *
	 * @return bool
	 */
	public function isStreamCursorComplete(): bool;

	/**
	 * Entities found in the current stream
	 *
	 * @return int
	 */
	public function entityCount(): int;

	/**
	 * Starting offset for the stream cursor
	 *
	 * @return string|null
	 */
	public function offsetStart(): ?string;

	/**
	 * Next offset returned from the stream cursor
	 *
	 * @return string|null
	 */
	public function offsetNext(): ?string;

	/**
	 * Update the stream properties
	 *
	 * @param string $_nextOffset  Next cursor offset
	 * @param int    $_entityCount Number of entities in the stream
	 *
	 * @return void
	 */
	public function updateStream( string $_nextOffset, int $_entityCount ): void;

	/**
	 * URI of the stream
	 *
	 * @return string
	 */
	public function uri(): string;
}
