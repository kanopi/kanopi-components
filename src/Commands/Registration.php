<?php

namespace Kanopi\Components\Commands;

use Kanopi\Components\Registration\CLIPrefix;

/**
 * Opt-in registration for bundled library commands
 *
 * @package kanopi-components
 */
final class Registration {
	/**
	 * Register all bundled WP-CLI commands
	 */
	public static function WPCLICommands(): void {
		CLIPrefix::prefix( 'kanopi-' )->commandsInNamespaceDirectory(
			'\\Kanopi\\Components\\Commands\\WordPress\\',
			__DIR__ . '/WordPress'
		);
	}
}
