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
