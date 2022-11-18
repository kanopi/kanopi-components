<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Data\Entities;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Repositories\ISetStream;

class ExternalStreamSet implements IExternalStreamReader {
	use Entities;

	protected ISetStream $stream_repository;

	public function __construct( ISetStream $_stream_repository ) {
		$this->stream_repository = $_stream_repository;
	}

	/**
	 * @inheritDoc
	 */
	function readStream( string $_stream_path, IEntitySet $_transform ): void {
		$this->entities = $_transform->transform( $this->stream_repository->read( $_stream_path ) );
	}}