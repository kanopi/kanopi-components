<?php

namespace Kanopi\Components\Assets\Model;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Transformers\Arrays;

/**
 * Asset Loader Configuration file data model
 *
 * @package kanopi/components
 */
class Configuration {
	/**
	 * Incoming JSON configuration
	 *
	 * @var array
	 */
	private array $rawJsonConfiguration;
	/**
	 * Set of validated entry point models
	 *
	 * @var EntityIterator
	 */
	private EntityIterator $entryPoints;

	/**
	 * Build a Configuration model
	 *
	 * @param array $_rawJson Raw JSON configuration
	 */
	public function __construct( array $_rawJson ) {
		$this->rawJsonConfiguration = $_rawJson;
		$this->entryPoints          = $this->buildEntryPoints( $_rawJson['filePatterns']['entryPoints'] ?? [] );
	}

	/**
	 * @param array $_entryPoints Set of entry points
	 *
	 * @return EntityIterator
	 */
	private function buildEntryPoints( array $_entryPoints ): EntityIterator {
		$entryPoints = Arrays::fresh();

		foreach ( $_entryPoints as $_handle => $entryPoint ) {
			$entryPoints->add( EntryPoint::fromString( $_handle, $entryPoint ) );
		}

		return EntityIterator::fromArray( $entryPoints->toArray(), EntryPoint::class );
	}

	/**
	 * Set of validated entry point models
	 *
	 * @returns EntityIterator
	 */
	public function entryPoints(): EntityIterator {
		return $this->entryPoints;
	}

	/**
	 * Raw Asset configuration
	 *
	 * @return array
	 */
	public function rawConfiguration(): array {
		return $this->rawJsonConfiguration;
	}

	/**
	 * Fluent method to build a Configuration model
	 *
	 * @param array $_rawJson Raw JSON configuration
	 *
	 * @returns Configuration
	 */
	public static function fromJson( array $_rawJson ): Configuration {
		return new static( $_rawJson );
	}
}
