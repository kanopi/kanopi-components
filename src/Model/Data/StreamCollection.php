<?php

namespace Kanopi\Components\Model\Data;

class StreamCollection implements IStreamCollection {
	/**
	 * @var IStream
	 */
	protected IStream $_stream;

	/**
	 * @var iterable
	 */
	protected iterable $_collection;

	public function __construct(
		iterable $_collection,
		IStream $_stream
	) {
		$this->_collection = $_collection;
		$this->_stream     = $_stream;
	}

	/**
	 * @inheritDoc
	 */
	function collection(): iterable {
		return $this->_collection;
	}

	/**
	 * @inheritDoc
	 */
	function stream(): IStream {
		return $this->_stream;
	}
}