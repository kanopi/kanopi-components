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
	 * @var iterable
	 */
	private iterable $rawJsonConfiguration;
	/**
	 * Set of validated entry point models
	 *
	 * @var EntityIterator
	 */
	private EntityIterator $entryPoints;

	/**
	 * Build a Configuration model
	 *
	 * @param iterable $_rawJson Raw JSON configuration
	 */
	public function __construct( iterable $_rawJson ) {
		$this->rawJsonConfiguration = $_rawJson;
		$this->entryPoints          = $this->buildEntryPoints( $_rawJson['filePatterns']['entryPoints'] ?? [] );
	}

	/**
	 * @param iterable $_entryPoints Set of entry points
	 *
	 * @return EntityIterator
	 */
	private function buildEntryPoints( iterable $_entryPoints ): EntityIterator {
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
	 * @return iterable
	 */
	public function rawConfiguration(): iterable {
		return $this->rawJsonConfiguration;
	}

	/**
	 * Fluent method to build a Configuration model
	 *
	 * @param iterable $_rawJson Raw JSON configuration
	 *
	 * @returns Configuration
	 */
	public static function fromJson( iterable $_rawJson ): Configuration {
		return new static( $_rawJson );
	}
}
