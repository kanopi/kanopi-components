<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\IStreamBatchConfiguration;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;

interface IStreamBatch {
	/**
	 * Read the current batch configuration by identifier
	 *    - Compares against stored batch configurations
	 *    - Continues matching batch or starts a new batch
	 *
	 * @param string            $_unique_identifier
	 * @param int               $_batch_size
	 * @param IStreamProperties $_properties
	 *
	 * @throws SetReaderException
	 *
	 * @return void
	 */
	function readCurrentByIdentifier(
		string $_unique_identifier,
		int $_batch_size,
		IStreamProperties $_properties
	): IStreamBatchConfiguration;

	/**
	 * Update the stored batch configuration for a give identifier
	 *
	 * @param string                    $_unique_identifier
	 * @param IStreamBatchConfiguration $_configuration
	 *
	 * @throws SetWriterException
	 *
	 * @return void
	 */
	function updateByIdentifier( string $_unique_identifier, IStreamBatchConfiguration $_configuration ): void;
}
