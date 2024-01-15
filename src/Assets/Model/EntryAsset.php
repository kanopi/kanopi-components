<?php

namespace Kanopi\Components\Assets\Model;

/**
 * Asset loader entry asset paths model
 *
 * @package kanopi/components
 */
class EntryAsset {
	/**
	 * Entry point script path
	 *
	 * @var string|null
	 */
	private ?string $script;
	/**
	 * Entry point style path
	 *
	 * @var string|null
	 */
	private ?string $style;

	/**
	 * Build an entry asset path model
	 *
	 * @param array $_assetPaths Asset entry array
	 */
	public function __construct( array $_assetPaths ) {
		$this->script = $_assetPaths['js'] ?? null;
		$this->style  = $_assetPaths['css'] ?? null;
	}

	/**
	 * Relative script path
	 *
	 * @return string|null
	 */
	public function script(): ?string {
		return $this->script;
	}

	/**
	 * Relative style path
	 *
	 * @return string|null
	 */
	public function style(): ?string {
		return $this->style;
	}

	/**
	 * Build an entry asset path model from a manifest array
	 *
	 * Configuration array expected key/value pairs:
	 *      - css: Relative path to style file
	 *      - js: Relative path to script file
	 *
	 * @param array $_assetPaths Asset manifest paths
	 *
	 * @return EntryAsset
	 */
	public static function fromArray( array $_assetPaths ): EntryAsset {
		return new static( $_assetPaths );
	}
}
