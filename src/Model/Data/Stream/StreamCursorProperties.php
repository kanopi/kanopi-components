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
	 * Set the next offset
	 *
	 * @param string $_offset Next cursor offset
	 *
	 * @return void
	 */
	public function updateNextOffset( string $_offset ): void;

	/**
	 * URI of the stream
	 *
	 * @return string
	 */
	public function uri(): string;
}
