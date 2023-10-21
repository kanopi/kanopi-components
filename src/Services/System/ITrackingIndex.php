<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;

interface ITrackingIndex {
	/**
	 * Read a fresh or resume a previous tracking index
	 *    - The callable, $_read_fresh_identifier_index returns an array of system identifiers
	 *  - If $_is_fresh_process is true, always returns a fresh index
	 *  - The returned array has the form (ID => Processed Flag)
	 *
	 * @param string   $_unique_identifier
	 * @param callable $_read_fresh_identifier_index
	 * @param bool     $_is_fresh_process
	 *
	 * @return array
	 * @throws SetReaderException
	 *
	 */
	function readTrackingIndexByIdentifier(
		string   $_unique_identifier,
		callable $_read_fresh_identifier_index,
		bool     $_is_fresh_process
	): array;

	/**
	 * Update the stored version of the tracking index
	 *
	 * @param string $_unique_identifier
	 * @param array  $_tracking_index
	 *
	 * @return void
	 * @throws SetWriterException
	 */
	function updateByIdentifier( string $_unique_identifier, array $_tracking_index ): void;
}
