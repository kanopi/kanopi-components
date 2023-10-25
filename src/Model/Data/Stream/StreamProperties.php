<?php

namespace Kanopi\Components\Model\Data\Stream;

/**
 * Stream properties
 *
 * @package kanopi/components
 */
class StreamProperties implements IStreamProperties {
	/**
	 * @var int
	 */
	protected int $lastModifiedTimestamp;
	/**
	 * @var int
	 */
	protected int $length;
	/**
	 * @var int
	 */
	protected int $readTimestamp;
	/**
	 * @var string
	 */
	protected string $uri;

	/**
	 * @param string $_uri                   Source URI
	 * @param int    $_lastModifiedTimestamp Last modified timestamp of source
	 * @param int    $_length                Length of source stream
	 * @param int    $_readTimestamp         Last read timestamp of source
	 */
	public function __construct(
		string $_uri,
		int $_lastModifiedTimestamp,
		int $_length,
		int $_readTimestamp
	) {
		$this->length                = $_length;
		$this->lastModifiedTimestamp = $_lastModifiedTimestamp;
		$this->readTimestamp         = $_readTimestamp;
		$this->uri                   = $_uri;
	}

	/**
	 * {@inheritDoc}
	 */
	public function readTimestamp(): int {
		return $this->readTimestamp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSameStream( IStreamProperties $_comparison ): bool {
		return $this->lastModifiedTimestamp() === $_comparison->lastModifiedTimestamp()
			&& $this->length() === $_comparison->length()
			&& $this->uri() === $_comparison->uri();
	}

	/**
	 * {@inheritDoc}
	 */
	public function lastModifiedTimestamp(): int {
		return $this->lastModifiedTimestamp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function length(): int {
		return $this->length;
	}

	/**
	 * {@inheritDoc}
	 */
	public function uri(): string {
		return $this->uri;
	}
}
