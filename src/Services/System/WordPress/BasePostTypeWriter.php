<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\IIndexedEntityGroupWriter;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Repositories\ISetWriter;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * Base class for read/write to post type repositories
 *
 * @package kanopi/components
 */
abstract class BasePostTypeWriter implements IIndexedEntityWriter {
	use PostTypeEntityWriter;

	/**
	 * System entity repository
	 *
	 * @var ISetWriter
	 */
	protected ISetWriter $entityRepository;
	/**
	 * Meta data repository
	 *
	 * @var ISetReader
	 */
	protected ISetReader $metaDataRepository;
	/**
	 * Taxonomy repository
	 *
	 * @var IIndexedEntityGroupWriter
	 */
	protected IIndexedEntityGroupWriter $taxonomyRepository;

	/**
	 * Base constructor deliberately requests all trait requested repositories
	 * Makes them available for implemented classes
	 *
	 * @param ISetWriter                $_entity_repository    Entity repository
	 * @param ISetReader                $_meta_data_repository Entity meta data repository
	 * @param IIndexedEntityGroupWriter $_taxonomy_repository  Entity taxonomy term repository
	 */
	public function __construct(
		ISetWriter $_entity_repository,
		ISetReader $_meta_data_repository,
		IIndexedEntityGroupWriter $_taxonomy_repository
	) {
		$this->entityRepository   = $_entity_repository;
		$this->metaDataRepository = $_meta_data_repository;
		$this->taxonomyRepository = $_taxonomy_repository;
	}

	/**
	 * @return ISetWriter
	 */
	public function entityRepository(): ISetWriter {
		return $this->entityRepository;
	}
}
