<?php

namespace Kanopi\Components\Model\Configuration;

/**
 * League CSV Configuration
 *
 * @package kanopi/cli
 */
class LeagueCsv {
	const VALID_FILE_MODES = [ 'r', 'r+', 'w' ];

	/**
	 * @var string
	 */
	protected string $fileMode = 'r';

	/**
	 * @var int|null
	 */
	protected ?int $headerRow = 0;

	/**
	 * Enabled if LeagueCsv can write to files
	 *
	 * @return bool
	 */
	public function canWrite(): bool {
		return 'w' === $this->fileMode;
	}

	/**
	 * Change to a valid file mode
	 *
	 * @param string $_mode Mode of the League CSV action
	 *
	 * @return LeagueCsv
	 * @see self::VALID_FILE_MODES
	 *
	 */
	public function changeFileMode( string $_mode ): LeagueCsv {
		if ( in_array( $_mode, self::VALID_FILE_MODES, true ) ) {
			$this->fileMode = $_mode;
		}

		return $this;
	}

	/**
	 * Change the header row
	 *    - Specify no header with null
	 *    - Must be a positive integer (zero is included)
	 *
	 * @param int|null $_row Header row index
	 *
	 * @return LeagueCsv
	 */
	public function changeHeaderRow( ?int $_row ): LeagueCsv {
		if ( null === $_row || -1 < $_row ) {
			$this->headerRow = $_row;
		}

		return $this;
	}

	/**
	 * File mode to open the CSV
	 *
	 * @return string
	 * @see self::VALID_FILE_MODES
	 */
	public function fileMode(): string {
		return $this->fileMode;
	}

	/**
	 * CSV Header Row Number (zero-indexed)
	 *    - Row values for a column become the index for the column in the CSV records associative array
	 *    - When null indicates there is no header row
	 *  - Default of 0 (first row)
	 *
	 * @return int|null
	 */
	public function headerRow(): ?int {
		return $this->headerRow;
	}

	/**
	 * Standard read-only with first row header
	 *
	 * @return LeagueCsv
	 */
	public static function ReadOnly(): LeagueCsv {
		return new self();
	}

	/**
	 * Read-write access with first row header
	 *
	 * @return LeagueCsv
	 */
	public static function ReadWrite(): LeagueCsv {
		return ( new self() )->changeFileMode( 'w' );
	}

	/**
	 * Standard read-only with no header row
	 *
	 * @return LeagueCsv
	 */
	public static function ReadOnlyNoHeader(): LeagueCsv {
		$configuration = new self();
		$configuration->changeHeaderRow( null );
		return $configuration;
	}
}
