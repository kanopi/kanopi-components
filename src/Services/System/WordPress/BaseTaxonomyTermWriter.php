<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Repositories\IGroupSetWriter;
use Kanopi\Components\Services\System\IIndexedGroupWriter;

abstract class BaseTaxonomyTermWriter implements IIndexedGroupWriter {
	use TaxonomyTermWriter;

	/**
	 * Base constructor deliberately requests all trait requested repositories
	 * Makes them available for implemented classes
	 *
	 * @param IGroupSetWriter $_entity_repository
	 */
	public function __construct( IGroupSetWriter $_entity_repository ) {
		$this->systemWriter = $_entity_repository;
	}
}