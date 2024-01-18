<?php

namespace Kanopi\Components\Assets\Model;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Transformers\Arrays;

class RegisteredAssets {
	/**
	 * @var Arrays
	 */
	private Arrays $assetsByType;
	/**
	 * @var Configuration
	 */
	private Configuration $configuration;

	/**
	 * Set of all assets
	 *
	 *
	 * @param Configuration  $_configuration Configuration model with entry points
	 * @param EntityIterator $_assetSet      Iterator of EntryAsset models
	 */
	public function __construct(
		Configuration $_configuration,
		EntityIterator $_assetSet
	) {
		$this->configuration = $_configuration;
		$this->assetsByType  = Arrays::fresh();

		/**
		 * @var EntryAsset $asset
		 */
		foreach ( $_assetSet as $asset ) {
			$this->assetsByType->writeIndex( $asset->entry(), $asset );
		}
	}

	public function readByType( string $_type ): EntityIterator {
	}
}
