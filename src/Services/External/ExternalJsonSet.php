<?php
/**
 * External JSON based Location data model set
 */

namespace Kanopi\Utilities\Services\External;

use Kanopi\Utilities\Model\Data\Entities;
use Kanopi\Utilities\Model\Transform\IEntitySet;
use Kanopi\Utilities\Model\Transform\ISetStream;
use Kanopi\Utilities\Repositories\IStreamReader;

class ExternalJsonSet implements IExternalStreamReader {
	/**
	 * Entity set holds the set of read, transformed external data models
	 */
	use Entities;

	/**
	 * @var IStreamReader
	 */
	protected IStreamReader $file_reader;

	/**
	 * @var ISetStream
	 */
	protected ISetStream $stream_reader;

	/**
	 * @param IStreamReader $_local_file_reader
	 * @param ISetStream    $_json_stream_reader
	 */
	public function __construct(
		IStreamReader $_local_file_reader,
		ISetStream $_json_stream_reader
	) {
		$this->file_reader   = $_local_file_reader;
		$this->stream_reader = $_json_stream_reader;
	}

	/**
	 * @inheritDoc
	 */
	function readStream( string $_stream_path, IEntitySet $_transform ): void {
		$stream         = $this->file_reader->read( $_stream_path );
		$json_set       = $this->stream_reader->transform( $stream );
		$this->entities = $_transform->transform( $json_set );
	}
}