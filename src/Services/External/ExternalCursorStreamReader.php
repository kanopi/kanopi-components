<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Data\Stream\CursorPagination;
use Kanopi\Components\Model\Data\Stream\StreamCursorProperties;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\IIndexedEntityReader;

/**
 * Read an external input stream via a paginated cursor, into a set of system entities for processing
 *
 * @package kanopi/components
 */
interface ExternalCursorStreamReader extends IIndexedEntityReader {
	/**
	 * Read a set of IIndexedEntity entities from an input stream
	 *  - This method is expected to mark the stream complete when appropriate
	 *
	 * @param string           $_streamPath URI of input stream
	 * @param CursorPagination $_pagination Cursor pagination
	 * @param IEntitySet       $_transform  Transform for input entities to system entities
	 *
	 * @returns StreamCursorProperties
	 * @throws SetStreamException Unable to process stream
	 */
	public function readStream(
		string $_streamPath,
		CursorPagination $_pagination,
		IEntitySet $_transform
	): StreamCursorProperties;
}
