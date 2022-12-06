<?php

namespace Kanopi\Components\Model\Data;

interface IStreamCollection {
	/**
	 * Iterable collection read from the stream
	 *
	 * @return iterable
	 */
	function collection(): iterable;

	/**
	 * The original stream source for the collection
	 *
	 * @return IStream
	 */
	function stream(): IStream;
}