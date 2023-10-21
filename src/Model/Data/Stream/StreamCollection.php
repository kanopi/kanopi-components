<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * String data stream processed collection implementation
 *
 * @package kanopi/components
 */
class StreamCollection implements IStreamCollection {
	/**
	 * @var IStream
	 */
	protected IStream $stream;
	/**
	 * @var iterable
	 */
	protected iterable $collection;

	/**
	 * Stream collection constructor
	 *
	 * @param iterable $_collection Collection built from the stream
	 * @param IStream  $_stream     Original stream
	 */
	public function __construct(
		iterable $_collection,
		IStream $_stream
	) {
		$this->collection = $_collection;
		$this->stream     = $_stream;
	}

	/**
	 * {@inheritDoc}
	 */
	public function collection(): iterable {
		return $this->collection;
	}

	/**
	 * {@inheritDoc}
	 */
	public function stream(): IStream {
		return $this->stream;
	}
}
