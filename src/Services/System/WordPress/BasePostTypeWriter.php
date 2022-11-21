<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Repositories\ISetWriter;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

abstract class BasePostTypeWriter implements IIndexedEntityWriter {
	use PostTypeEntityWriter;

	/**
	 * Base constructor deliberately requests all trait requested repositories
	 * Makes them available for implemented classes
	 *
	 * @param ISetWriter $_entity_repository
	 * @param ISetReader $_meta_data_repository
	 * @param ISetReader $_taxonomy_repository
	 */
	public function __construct(
		ISetWriter $_entity_repository,
		ISetReader $_meta_data_repository,
		ISetReader $_taxonomy_repository
	) {
		$this->systemWriter       = $_entity_repository;
		$this->metaDataRepository = $_meta_data_repository;
		$this->taxonomyRepository = $_taxonomy_repository;
	}
}