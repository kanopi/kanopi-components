<?php

namespace Kanopi\Components\Services\System;

use Kanopi\Components\Model\Data\ContentEntityFilters;

/**
 * Manage dynamic content filters
 *  - Useful for CLI reports requesting different sets of metadata
 *
 * @package kanopi-components
 */
interface DynamicFilters {
	/**
	 * Change the requested content filters
	 *
	 * @param ContentEntityFilters $_filters New set of content filters
	 *
	 * @return DynamicFilters
	 */
	public function changeFilters( ContentEntityFilters $_filters ): DynamicFilters;
}
