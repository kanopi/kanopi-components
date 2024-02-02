<?php

namespace Kanopi\Components\Assets\Model;

use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Transformers\Arrays;

/**
 * Build the set of sorted, registered assets with system dependencies
 *
 * @package kanopi/components
 */
class RegisteredAssets {
	const COMBINED_TYPES = [ 'combined' ];
	const SCRIPT_TYPES   = [ 'combined', 'script', 'register-only-script' ];
	const STYLE_TYPES    = [ 'combined', 'style', 'register-only-style' ];
	/**
	 * @var Arrays
	 */
	private Arrays $assetsByType;

	/**
	 * Set of all assets
	 *
	 * @param Configuration  $_configuration Configuration model with entry points
	 * @param EntityIterator $_assetSet      Iterator of EntryAsset models
	 */
	public function __construct( Configuration $_configuration, EntityIterator $_assetSet ) {
		$this->assetsByType = $this->sortDependencies( $_configuration, $_assetSet );
	}

	/**
	 * Sort a set of dependencies
	 *
	 * @param Configuration  $_configuration Configuration model with entry points
	 * @param EntityIterator $_assetSet      Iterator of EntryAsset models
	 *
	 * @return Arrays Set of sorted assets
	 */
	private function sortDependencies( Configuration $_configuration, EntityIterator $_assetSet ): Arrays {
		$assetsByType       = Arrays::fresh();
		$systemDependencies = Arrays::fresh();

		/**
		 * @var SystemEntryPoint $systemEntry
		 */
		foreach ( $_configuration->systemEntryPoints() as $systemEntry ) {
			/**
			 * @var EntryAsset|null $currentAsset
			 */
			$currentAsset = $_assetSet->offsetExists( $systemEntry->handle() )
				? $_assetSet->offsetGet( $systemEntry->handle() )
				: null;

			$path          = $currentAsset ? '' : null;
			$registerAsset = ! empty( $path )
				? new EntryPoint(
					$systemEntry->handle(),
					$path,
					Arrays::from( $systemEntry->dependencies() )->append( $systemDependencies->toArray() )->toArray(),
					$systemEntry->type()
				)
				: null;

			$systemDependencies->addMaybe(
				$systemEntry->handle(),
				$currentAsset && ! $systemEntry->optional()
			);

			$assetsByType->ensureSubArray( $systemEntry->handle() )->addMaybe( $registerAsset, ! empty( $currentAsset ) );
		}

		return $assetsByType;
	}

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

	/**
	 * Process a script name
	 *
	 * @param string     $_type  Type of asset
	 * @param EntryAsset $_asset Incoming asset
	 * @return string|null
	 */
	private function processScript( string $_type, EntryAsset $_asset ): ?string {
		$isScript = in_array( strtolower( $_type ), self::SCRIPT_TYPES, true );

		return '';
	}

	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

	/**
	 * Full set of sorted assets with system dependencies for a give type
	 *
	 * @param string $_type Asset type name
	 *
	 * @return EntityIterator Set of EntryAssets to register
	 */
	public function readByType( string $_type ): EntityIterator {
		return $this->assetsByType->readIndex( $_type );
	}
}
