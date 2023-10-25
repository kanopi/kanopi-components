<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream properties
 *
 * @package kanopi/components
 */
interface IStreamProperties {
	/**
	 * Last modified timestamp of the stream
	 *
	 * @return int
	 */
	public function lastModifiedTimestamp(): int;

	/**
	 * Count/length of the stream
	 *
	 * @return int
	 */
	public function length(): int;

	/**
	 * Timestamp of the stream read event
	 *
	 * @return int
	 */
	public function readTimestamp(): int;

	/**
	 * URI of the stream
	 *
	 * @return string
	 */
	public function uri(): string;

	/**
	 * Compare two stream to see if they are the same
	 *
	 * @param IStreamProperties $_comparison Comparison stream properties
	 *
	 * @return bool
	 */
	public function isSameStream( IStreamProperties $_comparison ): bool;
}
