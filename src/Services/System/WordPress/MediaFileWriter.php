<?php

namespace Kanopi\Components\Services\System\WordPress;

use Kanopi\Components\Model\Data\WordPress\MediaPostEntity;
use Kanopi\Components\Services\System\IIndexedEntityWriter;

/**
 * System Media file import service
 *
 * @package kanopi/components
 */
interface MediaFileWriter extends IIndexedEntityWriter {
	/**
	 * Download an external media file and import it into the Media Library
	 *  - Generally requires write access to the target system temporary and WordPress uploads directories
	 *
	 * @param MediaPostEntity $_media Media file asset to import
	 *
	 * @return int New media attachment identifier
	 */
	public function importFile( MediaPostEntity $_media ): int;

	/**
	 * Read the system URL for a media item
	 *
	 * @param MediaPostEntity $_media Media entity
	 *
	 * @return string
	 */
	public function readSystemUrl( MediaPostEntity $_media ): string;
}
