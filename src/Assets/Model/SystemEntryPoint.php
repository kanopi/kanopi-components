<?php

namespace Kanopi\Components\Assets\Model;

use Kanopi\Components\Transformers\Arrays;

/**
 * Asset loader system entry point model
 *  - System entry points are considered dependencies for all other entry points, unless optional
 *  - Mark a system entry point Optional if it may not always be present and should not be a dependency
 *
 * @package kanopi/components
 */
class SystemEntryPoint {
	/**
	 * Allowed Entry Point Types (replace with Enum when we support only 8.1+)
	 */
	const ALLOWED_ENTRY_TYPES = [ 'combined', 'style', 'script' ];
	/**
	 * Default entry type to assign
	 */
	const DEFAULT_ENTRY_TYPE = 'script';
	/**
	 * Set of handles on which this entry point depends
	 *
	 * @var Arrays
	 */
	private Arrays $dependencies;
	/**
	 * Entry point handle
	 *
	 * @var string
	 */
	private string $handle;
	/**
	 * Mark if the entry point is optional (i.e. might exist conditionally in Development mode)
	 *
	 * @var bool
	 */
	private bool $optional;
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
	 * @param array  $_dependencies List of dependency handles
	 * @param string $_type         Asset file type
	 * @param bool   $_optional     Whether the entry point is optional/conditional (default false)
	 */
	public function __construct(
		string $_handle,
		array $_dependencies,
		string $_type,
		bool $_optional = false
	) {
		$this->dependencies = Arrays::from( $_dependencies )->filterUnique();
		$this->handle       = $_handle;
		$this->optional     = $_optional;
		$this->type         = in_array( $_type, self::ALLOWED_ENTRY_TYPES, true ) ? $_type : self::DEFAULT_ENTRY_TYPE;
	}

	/**
	 * Register an additional dependency (prevents duplicates)
	 *
	 * @param string $_dependencyHandle Added dependency
	 * @return SystemEntryPoint
	 */
	public function addDependency( string $_dependencyHandle ): SystemEntryPoint {
		$this->dependencies->add( $_dependencyHandle )->filterUnique();

		return $this;
	}

	/**
	 * Set of handles on which this entry point depends
	 *
	 * @returns array
	 */
	public function dependencies(): iterable {
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
	 * Show if the entry point is declared optional (for instance Development only)
	 *
	 * @return bool
	 */
	public function optional(): bool {
		return $this->optional;
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
	 *      - optional: Sets whether the entry point is optional/conditional
	 *      - path: Source file path
	 *      - type: Entry point type
	 *
	 * For entry type, validates to ALLOWED_ENTRY_TYPES. When type is not specified,
	 * auto-detects SASS/CSS files as style, otherwise it is considered a script
	 *
	 * @param string $_handle             Entry point handle (without prefix)
	 * @param array  $_entryConfiguration Entry point configuration
	 *
	 * @return SystemEntryPoint
	 */
	public static function fromArray( string $_handle, array $_entryConfiguration ): SystemEntryPoint {
		return new static(
			$_handle,
			$_entryConfiguration['dependencies'] ?? [],
			$_entryConfiguration['type'] ?? self::DEFAULT_ENTRY_TYPE,
			$_entryConfiguration['optional'] ?? false
		);
	}
}
