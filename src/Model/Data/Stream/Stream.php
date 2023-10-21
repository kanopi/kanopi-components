<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * String data stream model implementation
 *
 * @package kanopi/components
 */
class Stream implements IStream {
	/**
	 * @var IStreamProperties
	 */
	protected IStreamProperties $properties;
	/**
	 * @var string
	 */
	protected string $stream;

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
		$this->properties = $_properties;
		$this->stream     = $_stream;
	}

	/**
	 * {@inheritDoc}
	 */
	public function properties(): IStreamProperties {
		return $this->properties;
	}

	/**
	 * {@inheritDoc}
	 */
	public function stream(): string {
		return $this->stream;
	}
}
