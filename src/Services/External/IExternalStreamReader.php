<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\IIndexedEntityReader;

/**
 * Read an external input stream into a set of system entities for processing
 *
 * @package kanopi/components
 */
interface IExternalStreamReader extends IIndexedEntityReader {
	/**
	 * Read a set of IIndexedEntity entities from an input stream
	 *
	 * @param string     $_stream_path URI of input stream
	 * @param IEntitySet $_transform   Transform for input entities to system entities
	 *
	 * @returns IStreamProperties
	 * @throws SetStreamException Unable to process stream
	 */
	public function readStream( string $_stream_path, IEntitySet $_transform ): IStreamProperties;
}
