<?php

namespace Kanopi\Utilities\Services\System;

use Kanopi\Utilities\Model\Collection\EntityIterator;
use Kanopi\Utilities\Model\Data\Entities;
use Kanopi\Utilities\Repositories\ISetReader;

trait AcmReader {
	/**
	 * The internal entity set, from read() is an index of integers
	 */
	use Entities;

	/**
	 * Location meta data repository
	 *
	 * @var ISetReader
	 */
	protected ISetReader $meta_data_reader;

	/**
	 * Tracking flag tells if the location index is loaded
	 *
	 * @var bool
	 */
	protected bool $is_index_loaded = false;

	/**
	 * System entity name, used for queries
	 *
	 * @return string
	 */
	abstract function systemEntityName(): string;

	/**
	 * @param int $_index_identifier
	 *
	 * @return bool
	 */
	protected function hasEntityByIndex( int $_index_identifier ): bool {
		return in_array( $_index_identifier, $this->read()->getArrayCopy(), true );
	}

	/**
	 * @inheritDoc
	 *
	 * Stores and returns an index of post IDs available for the Location post type
	 */
	public function read( mixed $_filter = null ): EntityIterator {
		if ( !$this->is_index_loaded ) {
			$this->entities = $this->post_writer->read( [
				'post_type'      => $this->systemEntityName(),
				'posts_per_page' => 1000,
				'fields'         => 'ids'
			] );

			$this->is_index_loaded = true;
		}

		return $this->entities;
	}
}