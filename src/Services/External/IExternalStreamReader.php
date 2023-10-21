<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\IIndexedEntityReader;

interface IExternalStreamReader extends IIndexedEntityReader {
	/**
	 * Read a set of IIndexedEntity entities from an input stream
	 *
	 * @param string     $_stream_path
	 * @param IEntitySet $_transform
	 *
	 * @returns IStreamProperties
	 * @throws SetStreamException
	 */
	function readStream( string $_stream_path, IEntitySet $_transform ): IStreamProperties;
}
