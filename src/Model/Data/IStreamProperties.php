<?php

namespace Kanopi\Components\Model\Data;

interface IStreamProperties {
	/**
	 * Last modified timestamp of the stream
	 *
	 * @return int
	 */
	function lastModifiedTimestamp(): int;

	/**
	 * Count/length of the stream
	 *
	 * @return int
	 */
	function length(): int;

	/**
	 * Timestamp of the stream read event
	 *
	 * @return int
	 */
	function readTimestamp(): int;

	/**
	 * URI of the stream
	 *
	 * @return string
	 */
	function uri(): string;
}