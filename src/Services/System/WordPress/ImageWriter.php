<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\FeaturedImage;

/**
 * System image import service
 *
 * @package kanopi/components
 */
interface ImageWriter {
	/**
	 * Download an external image, then upload it to the Media Library
	 *
	 * @param FeaturedImage $_image Featured image to associate
	 *
	 * @return int New image attachment identifier
	 */
	public function import( FeaturedImage $_image ): int;
}
