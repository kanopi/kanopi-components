<?php

namespace Kanopi\Utilities\Services\External;

use Kanopi\Utilities\Model\Exception\SetStreamException;
use Kanopi\Utilities\Model\Transform\IEntitySet;
use Kanopi\Utilities\Services\IIndexedEntityReader;

interface IExternalStreamReader extends IIndexedEntityReader {
	/**
	 * Read a set of IIndexedEntity entities from an input stream
	 *
	 * @param string     $_stream_path
	 * @param IEntitySet $_transform
	 *
	 * @throws SetStreamException
	 */
	function readStream( string $_stream_path, IEntitySet $_transform ): void;
}