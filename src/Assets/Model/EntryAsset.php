<?php

namespace Kanopi\Components\Assets\Model;

/**
 * Asset loader entry asset paths model
 *
 * @package kanopi/components
 */
class EntryAsset {
	/**
	 * @var string
	 */
	private string $entryName;
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
	 * @param string $_entryName  Entry point name
	 * @param array  $_assetPaths Asset entry array
	 */
	public function __construct( string $_entryName, array $_assetPaths ) {
		$this->entryName = $_entryName;
		$this->script    = $_assetPaths['js'] ?? null;
		$this->style     = $_assetPaths['css'] ?? null;
	}

	/**
	 * Entry point name
	 *
	 * @return string
	 */
	public function entry(): string {
		return $this->entryName;
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
	 * @param string $_entryName  Entry point name
	 * @param array  $_assetPaths Asset manifest paths
	 *
	 * @return EntryAsset
	 */
	public static function fromArray( string $_entryName, array $_assetPaths ): EntryAsset {
		return new static( $_entryName, $_assetPaths );
	}
}
