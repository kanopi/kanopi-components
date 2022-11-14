<?php
/**
 * Register CLI commands under a particular prefix
 */

namespace Kanopi\Components\Registration;

use Kanopi\Components\Converters;
use WP_CLI;

class CLIPrefix {
	const DEFAULT_PREFIX = 'kanopi-';

	/**
	 * @var string
	 */
	protected $prefix;

	function __construct( string $_prefix = '' ) {
		$this->prefix = empty( $_prefix ) ? self::DEFAULT_PREFIX : $_prefix;
	}

	static function prefix( string $_prefix = '' ) {
		return new CLIPrefix( $_prefix );
	}

	function commands_in_namespace_directory( string $_namespace, string $_directory ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			foreach ( scandir( $_directory ) as $file ) {
				preg_match( '/^([\w\-]+)\.php/', $file, $match );

				if ( ! empty( $match[1] ) ) {
					WP_CLI::add_command(
						$this->prefix . Converters\Strings::from( $match[1] )->pascal_to_separate()->to_string(),
						$_namespace . $match[1]
					);
				}
			}
		}
	}
}
