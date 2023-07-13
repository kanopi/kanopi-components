<?php

namespace Kanopi\Components\Model\Data\Stream;

class Stream implements IStream {
	/**
	 * @var IStreamProperties
	 */
	protected IStreamProperties $_properties;

	/**
	 * @var string
	 */
	protected string $_stream;

	/**
	 * Build a stream data entity
	 *
	 * @param string            $_stream     Stream content
	 * @param IStreamProperties $_properties Raw properties of the stream
	 */
	public function __construct(
		string $_stream,
		IStreamProperties $_properties
	) {
		$this->_properties = $_properties;
		$this->_stream     = $_stream;
	}

	/**
	 * @inheritDoc
	 */
	public function properties(): IStreamProperties {
		return $this->_properties;
	}

	/**
	 * @inheritDoc
	 */
	public function stream(): string {
		return $this->_stream;
	}
}