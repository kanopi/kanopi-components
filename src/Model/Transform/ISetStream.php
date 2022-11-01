<?php
/**
 * Transform an input stream into an iterable set
 */

namespace Kanopi\Utilities\Model\Transform;

use Kanopi\Utilities\Model\Exception\SetStreamException;

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
	function transform( string $_input_stream ): iterable;
}