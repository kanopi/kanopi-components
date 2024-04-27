<?php

namespace Kanopi\Components\Services\External;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Model\Data\Entities;
use Kanopi\Components\Model\Data\Stream\{IStreamProperties, StreamProperties};
use Kanopi\Components\Model\Configuration;
use Kanopi\Components\Model\Exception\ImportStreamException;
use Kanopi\Components\Model\Exception\SetStreamException;
use Kanopi\Components\Model\Transform\IEntitySet;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Writer;

/**
 * Service to read and write from a CSV driven data-set with League CSV
 *
 * @package kanopi/components
 */
class LeagueCsv implements IExternalStreamReader {
	use Entities;

	/**
	 * @var Configuration\LeagueCsv
	 */
	protected Configuration\LeagueCsv $configuration;

	/**
	 * LeagueCsv Constructor
	 *
	 * @param Configuration\LeagueCsv $_configuration LeagueCsv Configuration
	 */
	public function __construct( Configuration\LeagueCsv $_configuration ) {
		$this->configuration = $_configuration;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws SetStreamException
	 */
	public function readStream( string $_stream_path, IEntitySet $_transform ): IStreamProperties {
		try {
			$fileLastModified = filemtime( $_stream_path );
			$reader           = Reader::createFromPath( $_stream_path, $this->configuration->fileMode() );
			if ( null !== $this->configuration->headerRow() ) {
				$reader->setHeaderOffset( $this->configuration->headerRow() );
			}
			$this->entities = $_transform->transform( $reader );
			return new StreamProperties(
				$_stream_path,
				false !== $fileLastModified ? $fileLastModified : 0,
				$reader->count(),
				time()
			);
		}
		catch ( Exception $exception ) {
			throw new SetStreamException( "Cannot read CSV | {$exception->getMessage()}" );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function read(): EntityIterator {
		return $this->entities;
	}

	/**
	 * Write a data set out to a given file path
	 *
	 * @param array    $_headers  Data set headers
	 * @param iterable $_dataSet  Data set to process
	 * @param string   $_filePath File path to write the data set to CSV
	 *
	 * @return void
	 * @throws ImportStreamException Cannot write to file
	 */
	public function writeFile( array $_headers, iterable $_dataSet, string $_filePath ): void {
		if ( empty( $_filePath ) ) {
			throw new ImportStreamException( 'File path to write is missing.' );
		}
		if ( ! $this->configuration->canWrite() ) {
			throw new ImportStreamException( 'Write mode is not enabled.' );
		}

		try {
			$writer = Writer::createFromPath( $_filePath, $this->configuration->fileMode() );
			$writer->insertOne( $_headers );
			$writer->insertAll( $_dataSet );
		}
		catch ( CannotInsertRecord $_exception ) {
			throw new ImportStreamException( 'Cannot write to CSV: ' . $_exception->getMessage() );
		}
	}
}
