<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\IGroupSetWriter;
use Kanopi\Components\Services\System\IIndexedGroupWriter;

/**
 * Base class for read/write to taxonomy term repositories
 *
 * @package kanopi/components
 */
abstract class BaseTaxonomyTermWriter implements IIndexedGroupWriter {
	use TaxonomyTermWriter;

	/**
	 * @var IGroupSetWriter
	 */
	protected IGroupSetWriter $entityRepository;

	/**
	 * Base constructor deliberately requests all trait requested repositories
	 * Makes them available for implemented classes
	 *
	 * @param IGroupSetWriter $_entity_repository Term repository
	 */
	public function __construct( IGroupSetWriter $_entity_repository ) {
		$this->entityRepository = $_entity_repository;
	}

	/**
	 * @return IGroupSetWriter
	 */
	public function entityRepository(): IGroupSetWriter {
		return $this->entityRepository;
	}
}
