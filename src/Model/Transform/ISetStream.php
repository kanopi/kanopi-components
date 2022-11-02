<?php
/**
 * Transform an input stream into an iterable set
 */

namespace Kanopi\Components\Model\Transform;

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
	function transform( string $_input_stream ): iterable;
}