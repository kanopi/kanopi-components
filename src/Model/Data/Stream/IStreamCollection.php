<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * String data stream processed collection
 *
 * @package kanopi/components
 */
interface IStreamCollection {
	/**
	 * Iterable collection read from the stream
	 *
	 * @return iterable
	 */
	public function collection(): iterable;

	/**
	 * The original stream source for the collection
	 *
	 * @return IStream
	 */
	public function stream(): IStream;
}
