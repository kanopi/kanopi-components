<?php
/**
 * Register CLI commands under a particular prefix
 */

namespace Kanopi\Components\Registration;

use Kanopi\Components\Transformers;
use WP_CLI;

class CLIPrefix {
	const DEFAULT_PREFIX = 'kanopi-';

	/**
	 * @var string
	 */
	protected string $prefix;

	function __construct( string $_prefix = '' ) {
		$this->prefix = empty( $_prefix ) ? self::DEFAULT_PREFIX : $_prefix;
	}

	static function prefix( string $_prefix = '' ) {
		return new CLIPrefix( $_prefix );
	}

	function commandsInNamespaceDirectory( string $_namespace, string $_directory ) {
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
