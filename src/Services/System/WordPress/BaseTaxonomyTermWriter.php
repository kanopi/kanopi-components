<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\IGroupSetWriter;
use Kanopi\Components\Services\System\IIndexedGroupWriter;

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
	 * @param IGroupSetWriter $_entity_repository
	 */
	public function __construct( IGroupSetWriter $_entity_repository ) {
		$this->entityRepository = $_entity_repository;
	}

	/**
	 * @return IGroupSetWriter
	 */
	function entityRepository(): IGroupSetWriter {
		return $this->entityRepository;
	}
}
