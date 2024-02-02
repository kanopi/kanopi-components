<?php

namespace Kanopi\Components\Assets\Model;

use Kanopi\Components\Model\Collection\EntityIterator;

/**
 * Asset Loader Configuration file interface
 *
 * @package kanopi/components
 */
interface Configuration {
	/**
	 * Set of validated entry point models
	 *
	 * @returns EntityIterator
	 */
	public function entryPoints(): EntityIterator;

	/**
	 * Add a prefix to an asset handle to avoid duplication/overlap in the DOM
	 *  - Useful if the consuming system needs prefixed entities to avoid DOM ID duplication
	 *  - Separate as the handle keys in the asset manifest will not have the prefix, add these after lookup
	 *
	 * @param string $_handle Handle without prefix
	 * @return string
	 */
	public function prefixRegisteredHandle( string $_handle ): string;

	/**
	 * Raw Asset configuration
	 *
	 * @return iterable
	 */
	public function rawConfiguration(): iterable;

	/**
	 * Set of expected, system-generated entry point models
	 *
	 * @returns EntityIterator
	 */
	public function systemEntryPoints(): EntityIterator;
}
