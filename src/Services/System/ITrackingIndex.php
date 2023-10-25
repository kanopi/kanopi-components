<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;

/**
 * Service interface to track processed entities
 *
 * @package kanopi/components
 */
interface ITrackingIndex {
	/**
	 * Read a fresh or resume a previous tracking index
	 *    - The callable, $_read_fresh_identifier_index returns an array of system identifiers
	 *  - If $_is_fresh_process is true, always returns a fresh index
	 *  - The returned array has the form (ID => Processed Flag)
	 *
	 * @param string   $_unique_identifier           Tracking index unique identifier
	 * @param callable $_read_fresh_identifier_index Callable function to generate an index identifier
	 * @param bool     $_is_fresh_process            Flag if a process is new (ignore existing)
	 *
	 * @return array
	 * @throws SetReaderException Unable to read existing tracking index data
	 *
	 */
	public function readTrackingIndexByIdentifier(
		string $_unique_identifier,
		callable $_read_fresh_identifier_index,
		bool $_is_fresh_process
	): array;

	/**
	 * Update the stored version of the tracking index
	 *
	 * @param string $_unique_identifier Tracking index unique identifier
	 * @param array  $_tracking_index    Current tracking index state
	 *
	 * @return void
	 * @throws SetWriterException Unable to update tracking index
	 */
	public function updateByIdentifier( string $_unique_identifier, array $_tracking_index ): void;
}
