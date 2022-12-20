<?php

namespace Kanopi\Components\Model\Data\Stream;

class StreamProperties implements IStreamProperties {
	/**
	 * @var int
	 */
	protected int $_lastModifiedTimestamp;

	/**
	 * @var int
	 */
	protected int $_length;

	/**
	 * @var int
	 */
	protected int $_readTimestamp;

	/**
	 * @var string
	 */
	protected string $_uri;

	/**
	 * @param string $_uri
	 * @param int    $_lastModifiedTimestamp
	 * @param int    $_length
	 * @param int    $_readTimestamp
	 */
	public function __construct(
		string $_uri,
		int $_lastModifiedTimestamp,
		int $_length,
		int $_readTimestamp
	) {
		$this->_length                = $_length;
		$this->_lastModifiedTimestamp = $_lastModifiedTimestamp;
		$this->_readTimestamp         = $_readTimestamp;
		$this->_uri                   = $_uri;
	}

	/**
	 * @inheritDoc
	 */
	function lastModifiedTimestamp(): int {
		return $this->_lastModifiedTimestamp;
	}

	/**
	 * @inheritDoc
	 */
	function length(): int {
		return $this->_length;
	}

	/**
	 * @inheritDoc
	 */
	function readTimestamp(): int {
		return $this->_readTimestamp;
	}

	/**
	 * @inheritDoc
	 */
	function uri(): string {
		return $this->_uri;
	}

	/**
	 * @inheritDoc
	 */
	function isSameStream( IStreamProperties $_comparison ): bool {
		return $this->lastModifiedTimestamp() === $_comparison->lastModifiedTimestamp()
			&& $this->length() === $_comparison->length()
			&& $this->uri() === $_comparison->uri();
	}
}