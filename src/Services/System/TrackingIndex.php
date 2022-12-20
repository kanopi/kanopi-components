<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\TrackingIndexStorage;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

class TrackingIndex implements ITrackingIndex {
	/**
	 * @return IGroupSetWriter
	 */
	protected IGroupSetWriter $_trackingStorageRepository;

	public function __construct( IGroupSetWriter $_tracking_repository ) {
		$this->_trackingStorageRepository = $_tracking_repository;
	}

	/**
	 * @inheritDoc
	 */
	function readTrackingIndexByIdentifier(
		string $_unique_identifier,
		callable $_read_fresh_identifier_index,
		bool $_is_fresh_process
	): array {
		$storedIndex = $this->readStoredTrackingIndex( $_unique_identifier );

		return $_is_fresh_process || empty( $storedIndex )
			? $this->readSystemTrackingIndex( $_read_fresh_identifier_index )
			: $storedIndex;
	}

	/**
	 * Read a new tracking index from the system service
	 *    - All current system index identifiers as keys
	 *    - Value of each index is false, not processed
	 */
	protected function readSystemTrackingIndex( callable $_read_fresh ): array {
		$index = [];
		$fresh = call_user_func( $_read_fresh );

		if ( is_array( $fresh ) ) {
			foreach ( $fresh as $_id ) {
				$index[ $_id ] = false;
			}
		}

		return $index;
	}

	/**
	 * Reads any stored tracking index
	 *
	 * @param string $_unique_identifier
	 *
	 * @throws SetReaderException
	 * @return array|null
	 */
	protected function readStoredTrackingIndex( string $_unique_identifier ): ?array {
		$index = $this->_trackingStorageRepository->read( $_unique_identifier );

		return $index->valid() ? $index->current()->systemTransform() : null;
	}

	/**
	 * Update the stored version of the tracking index
	 *
	 * @param string $_unique_identifier
	 * @param array  $_tracking_index
	 *
	 * @throws SetWriterException
	 * @return void
	 */
	function updateByIdentifier( string $_unique_identifier, array $_tracking_index ): void {
		$this->_trackingStorageRepository->update(
			$_unique_identifier,
			new TrackingIndexStorage( $_unique_identifier, $_tracking_index )
		);
	}
}
