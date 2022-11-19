<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

abstract class BasePostTypeWriter implements IIndexedEntityWriter {
	use PostTypeEntityWriter;

	/**
	 * Base constructor deliberately requests meta-data and taxonomy repositories
	 * Makes them available for implemented classes
	 *
	 * @param ISetReader $_meta_data_repository
	 * @param ISetReader $_taxonomy_repository
	 */
	public function __construct(
		ISetReader $_meta_data_repository,
		ISetReader $_taxonomy_repository
	) {
		$this->metaDataRepository = $_meta_data_repository;
		$this->taxonomyRepository = $_taxonomy_repository;
	}
}