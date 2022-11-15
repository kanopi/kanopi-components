<?php
/**
 * Reads an input stream into an iterable set
 */

namespace Kanopi\Components\Repositories;

use Kanopi\Components\Model\Exception\SetStreamException;

interface ISetStream {
	/**
	 * Read an input stream value from a requested stream location
	 *
	 * @param string $_input_stream
	 *
	 * @throws SetStreamException
	 *
	 * @return iterable
	 */
	function read( string $_input_stream ): iterable;
}