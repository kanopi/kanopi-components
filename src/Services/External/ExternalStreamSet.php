<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Data\Entities;
use Kanopi\Components\Model\Data\IStreamProperties;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Repositories\ISetStream;
use Kanopi\Components\Repositories\IStreamReader;

class ExternalStreamSet implements IExternalStreamReader {
	/**
	 * Entity set holds the set of read, transformed external data models
	 */
	use Entities;

	/**
	 * @var IStreamReader
	 */
	protected IStreamReader $stream_reader;

	/**
	 * @var ISetStream
	 */
	protected ISetStream $set_reader;

	/**
	 * @param IStreamReader $_stream_reader
	 * @param ISetStream    $_set_reader
	 */
	public function __construct(
		IStreamReader $_stream_reader,
		ISetStream $_set_reader
	) {
		$this->stream_reader = $_stream_reader;
		$this->set_reader    = $_set_reader;
	}

	/**
	 * @inheritDoc
	 */
	function readStream( string $_stream_path, IEntitySet $_transform ): IStreamProperties {
		$stream = $this->stream_reader->read( $_stream_path );
		$streamCollection = $this->set_reader->read( $stream );
		$this->entities   = $_transform->transform( $streamCollection->collection() );

		return $stream->properties();
	}
}