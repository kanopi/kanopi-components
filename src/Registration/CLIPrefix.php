<?php

namespace Kanopi\Components\Registration;

use Kanopi\Components\Transformers;
use WP_CLI;

/**
 * Register CLI commands under a particular prefix
 *
 * @package kanopi/components
 */
class CLIPrefix {
	const DEFAULT_PREFIX = 'kanopi-';
	/**
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Construct a CLI command registration with a common prefix
	 *
	 * @param string $_prefix Common command name prefix
	 */
	public function __construct( string $_prefix = '' ) {
		$this->prefix = empty( $_prefix ) ? self::DEFAULT_PREFIX : $_prefix;
	}

	/**
	 * Chainable CLI command registration with a common prefix
	 *
	 * @param string $_prefix Common command name prefix
	 */
	public static function prefix( string $_prefix = '' ): CLIPrefix {
		return new CLIPrefix( $_prefix );
	}

	/**
	 * Register all CLI command in a given directory and PHP namespace
	 *
	 * @param string $_namespace PHP Namespace of commands
	 * @param string $_directory Directory containing command classes
	 *
	 * @return void
	 */
	public function commandsInNamespaceDirectory( string $_namespace, string $_directory ): void {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			foreach ( scandir( $_directory ) as $file ) {
				preg_match( '/^([\w\-]+)\.php/', $file, $match );

				if ( ! empty( $match[1] ) ) {
					WP_CLI::add_command(
						$this->prefix . Transformers\Strings::from( $match[1] )->pascalToSeparate()->toString(),
						$_namespace . $match[1]
					);
				}
			}
		}
	}
}
