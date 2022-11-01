<?php

namespace Kanopi\Utilities\Services\System;

use Kanopi\Utilities\Model\Collection\EntityIterator;

interface ISystemGroupAcmWriter extends ISystemAcmWriter {
	/**
	 * Read the set of entities associated with a group/foreign key
	 *
	 * @param string $_group_key
	 *
	 * @return EntityIterator
	 */
	function readByGroupKey( string $_group_key ): EntityIterator;
}