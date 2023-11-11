<?php

namespace Kanopi\Components\Assets\Model;

/**
 * Asset loader entry point model
 *
 * @package kanopi/components
 */
class EntryPoint {
	/**
	 * Allowed Entry Point Types (replace with Enum when we support only 8.1+)
	 */
	const ALLOWED_ENTRY_TYPES = [ 'combined', 'register-only-style', 'register-only-script', 'style', 'script' ];
	/**
	 * Default entry type to assign
	 */
	const DEFAULT_ENTRY_TYPE = 'script';
	/**
	 * Set of handles on which this entry point depends
	 *
	 * @var array
	 */
	private array $dependencies;
	/**
	 * Entry point handle
	 *
	 * @var string
	 */
	private string $handle;
	/**
	 * Path to asset
	 *
	 * @var string
	 */
	private string $path;
	/**
	 * Type of asset
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * Build an entry point model
	 *
	 * @param string $_handle       Entry point handle
	 * @param string $_path         Source asset path
	 * @param array  $_dependencies List of dependency handles
	 * @param string $_type         Asset file type
	 */
	public function __construct(
		string $_handle,
		string $_path,
		array $_dependencies,
		string $_type
	) {
		$type = ! empty( $_type ) ? strtolower( $_type ) : $this->autoDetectType( $_path );
		if ( ! in_array( $type, self::ALLOWED_ENTRY_TYPES, true ) ) {
			$type = self::DEFAULT_ENTRY_TYPE;
		}

		$this->handle       = $_handle;
		$this->path         = $_path;
		$this->dependencies = $_dependencies;
		$this->type         = $type;
	}

	/**
	 * Auto-detects the file type based on file path
	 *
	 * @param string $_path Source file path
	 *
	 * @return string
	 */
	private function autoDetectType( string $_path ): string {
		$styleTypeMatchCount = preg_match( '/\.(sass|css)$/', $_path );

		return empty( $styleTypeMatchCount ) ? 'script' : 'style';
	}

	/**
	 * Set of handles on which this entry point depends
	 *
	 * @returns array
	 */
	public function dependencies(): array {
		return $this->dependencies;
	}

	/**
	 * Entry point handle (no prefix)
	 *
	 * @return string
	 */
	public function handle(): string {
		return $this->handle;
	}

	/**
	 * Source path of asset
	 *
	 * @return string
	 */
	public function path(): string {
		return $this->path;
	}

	/**
	 * Source asset file type
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->type;
	}

	/**
	 * Build an entry point from a configuration array
	 *
	 * Configuration array expected key/value pairs:
	 *      - dependencies: Set of required prior entry point handles
	 *      - path: Source file path
	 *      - type: Entry point type
	 *
	 * For entry type, validates to ALLOWED_ENTRY_TYPES. When type is not specified,
	 * auto-detects SASS/CSS files as style, otherwise it is considered a script
	 *
	 * @param string $_handle             Entry point handle (without prefix)
	 * @param array  $_entryConfiguration Entry point configuration
	 *
	 * @return EntryPoint
	 */
	public static function fromArray( string $_handle, array $_entryConfiguration ): EntryPoint {
		return new static(
			$_handle,
			$_entryConfiguration['path'] ?? '',
			$_entryConfiguration['dependencies'] ?? [],
			$_entryConfiguration['type'] ?? ''
		);
	}

	/**
	 * Build an entry point from a file path, auto-detects SASS/CSS files as styles, otherwise as a script
	 *
	 * @param string $_handle Entry point handle (without prefix)
	 * @param string $_path   Source file path
	 *
	 * @return EntryPoint
	 */
	public static function fromString( string $_handle, string $_path ): EntryPoint {
		return new static( $_handle, $_path, [], '' );
	}
}
