<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\MediaPostEntity;
use Kanopi\Components\Model\Exception\DependencyException;

/**
 * System image import service
 *
 * @package kanopi/components
 */
interface ImageWriter {
	/**
	 * Download an external image, then upload it to the Media Library
	 *
	 * @param MediaPostEntity $_image External image to import
	 *
	 * @throws DependencyException Missing dependency service to write the media file
	 *
	 * @return int New image attachment identifier
	 */
	public function import( MediaPostEntity $_image ): int;
}
