<?php

namespace Kanopi\Components\Processor;

use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Model\Data\Stream\IStreamProperties;
use Kanopi\Components\Model\Exception\SetReaderException;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use Kanopi\Components\Services\External\IExternalStreamReader;

/**
 * Reads and validates an external stream
 */
trait ReadValidateStream {
	/**
	 * External data import source service
	 *
	 * @return IExternalStreamReader
	 */
	abstract protected function externalService(): IExternalStreamReader;

	/**
	 * Entity transformer for all incoming data
	 *
	 * @return IEntitySet
	 */
	abstract protected function incomingEntityTransformer(): IEntitySet;

	/**
	 * Logging interface
	 *
	 * @return ILogger
	 */
	abstract protected function logger(): ILogger;

	/**
	 * Stream validity check
	 *    - Return false to stop processing
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return bool
	 */
	abstract protected function isStreamProcessValid( IStreamProperties $_streamProperties ): bool;

	/**
	 * Events before process validation
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 * @throws SetReaderException Could not validate input set
	 *
	 */
	abstract protected function preProcessValidationEvents( IStreamProperties $_streamProperties ): void;

	/**
	 * Read the external data entities
	 *
	 * @param string $_input_stream_uri URI of the input stream
	 *
	 * @return void
	 *
	 * @throws SetStreamException Failure to process the import stream
	 */
	protected function processExternalStreamEvents( string $_input_stream_uri ): void {
		try {
			$streamProperties = $this->readExternalStream( $_input_stream_uri );

			$this->preProcessValidationEvents( $streamProperties );
			$this->preProcessBanner( $streamProperties );
			if ( false === $this->isStreamProcessValid( $streamProperties ) ) {
				throw new SetStreamException( 'External stream is invalid' );
			}
		}
		catch ( SetReaderException | SetStreamException $exception ) {
			throw new SetStreamException( $exception->getMessage() );
		}
	}

	/**
	 * Read the external data stream
	 *  - Separate to override as needed for alternate external data sources
	 *
	 * @param string $_input_stream_uri External string URI
	 *
	 * @return IStreamProperties Processed stream with properties
	 * @throws SetStreamException Failure to read external import stream
	 *
	 */
	protected function readExternalStream( string $_input_stream_uri ): IStreamProperties {
		return $this->externalService()->readStream( $_input_stream_uri, $this->incomingEntityTransformer() );
	}

	/**
	 * Logging banner to show before processing begins
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return void
	 */
	protected function preProcessBanner( IStreamProperties $_streamProperties ): void {
		$this->logger()->table(
			$this->preProcessingBannerHeader(),
			[
				$this->preProcessingBannerDataRow( $_streamProperties ),
			]
		);
	}

	/**
	 * Override to add more banner columns
	 *    - Each entry is an index in the data row
	 *
	 * @return array
	 */
	protected function preProcessingBannerHeader(): array {
		return [
			'Stream Last Modified Date',
			'Stream Length',
			'Stream Uri',
		];
	}

	/**
	 * Override to add more fields
	 *    - Single data row, each index must correspond to a Header entry
	 *
	 * @param IStreamProperties $_streamProperties Incoming stream properties
	 *
	 * @return string[]
	 */
	protected function preProcessingBannerDataRow( IStreamProperties $_streamProperties ): array {
		return [
			'Stream Last Modified Date' => gmdate( 'm-d-Y H:i:s', $_streamProperties->lastModifiedTimestamp() ),
			'Stream Length'             => $_streamProperties->length(),
			'Stream Uri'                => $_streamProperties->uri(),
		];
	}
}
