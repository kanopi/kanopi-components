<?php

namespace Kanopi\Components\Model\Data;

class Stream implements IStream {
	/**
	 * @var IStreamProperties
	 */
	protected IStreamProperties $_properties;

	/**
	 * @var string
	 */
	protected string $_stream;

	public function __construct(
		string $_stream,
		IStreamProperties $_properties
	) {

	}

	/**
	 * @inheritDoc
	 */
	function properties(): IStreamProperties {
		return $this->_properties;
	}

	/**
	 * @inheritDoc
	 */
	function stream(): string {
		return $this->_stream;
	}
}