<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\Process\TrackingIndexStorage;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetWriterException;
use Kanopi\Components\Repositories\IGroupSetWriter;

/**
 * Common service to track processed entities
 *
 * @package kanopi/components
 */
class TrackingIndex implements ITrackingIndex {
	/**
	 * @var IGroupSetWriter
	 */
	protected IGroupSetWriter $trackingRepository;

	/**
	 * Build the tracking service
	 *
	 * @param IGroupSetWriter $_tracking_repository Tracking storage
	 */
	public function __construct( IGroupSetWriter $_tracking_repository ) {
		$this->trackingRepository = $_tracking_repository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readTrackingIndexByIdentifier(
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
	 * Reads any stored tracking index
	 *
	 * @param string $_unique_identifier Tacking index unique identifier
	 *
	 * @return array|null
	 * @throws SetReaderException Unable to read existing tracking index
	 */
	protected function readStoredTrackingIndex( string $_unique_identifier ): ?array {
		$index = $this->trackingRepository->read( $_unique_identifier );

		return $index->valid() ? $index->current()->systemTransform() : null;
	}

	/**
	 * Read a new tracking index from the system service
	 *    - All current system index identifiers as keys
	 *    - Value of each index is false, not processed
	 *
	 * @param callable $_read_fresh Method to generate a new tracking index
	 */
	protected function readSystemTrackingIndex( callable $_read_fresh ): array {
		$index = [];
		$fresh = call_user_func( $_read_fresh );

		if ( is_iterable( $fresh ) ) {
			foreach ( $fresh as $_id ) {
				$index[ $_id ] = false;
			}
		}

		return $index;
	}

	/**
	 * Update the stored version of the tracking index
	 *
	 * @param string $_unique_identifier Tacking index unique identifer
	 * @param array  $_tracking_index    New tracking index state
	 *
	 * @return void
	 * @throws SetWriterException Unable to update tracking index
	 */
	public function updateByIdentifier( string $_unique_identifier, array $_tracking_index ): void {
		$this->trackingRepository->update(
			$_unique_identifier,
			new TrackingIndexStorage( $_unique_identifier, $_tracking_index )
		);
	}
}
