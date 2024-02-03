<?php

namespace Assets\Model;

use Kanopi\Components\Assets\Model\EntryPoint;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class EntryPointTest extends TestCase {
	/**
	 * Test data provider for array versions
	 *  - Parameters are: Entry handle name, Entry configuration array, Expected entry type
	 *
	 * @return array[]
	 */
	public function providerArrayType(): array {
		return [
			'Invalid Type - Is a script'  => [
				'sample-default',
				[
					'path' => './path/to/register-only.css',
					'type' => 'something-invalid',
				],
				'script',
			],
			'Missing Type - Auto-detects' => [
				'sample-default',
				[
					'path' => './path/to/not-a-script.css',
				],
				'style',
			],
			'Valid Type'                  => [
				'sample-default',
				[
					'path' => './path/to/register-only.css',
					'type' => 'register-only-style',
				],
				'register-only-style',
			],
		];
	}

	/**
	 * Test data provider for string versions
	 *  - Parameters are: Entry handle name, Entry file path, Expected entry type
	 * @return array[]
	 */
	public function providerStringType(): array {
		return [
			'CSS File'  => [ 'sample-css', './path/to/whatever.css', 'style' ],
			'JS File'   => [ 'sample-js', './path/to/whatever.js', 'script' ],
			'JSX File'  => [ 'sample-jsx', './path/to/whatever.jsx', 'script' ],
			'SASS File' => [ 'sample-sass', './path/to/whatever.sass', 'style' ],
			'SCSS File' => [ 'sample-scss', './path/to/whatever.scss', 'style' ],
			'TSX File'  => [ 'sample-tsx', './path/to/whatever.tsc', 'script' ],
		];
	}

	/**
	 * @dataProvider providerArrayType
	 *
	 * @param string $handle       Entry point handle
	 * @param array  $entry        Entry configuration array
	 * @param string $expectedType Expected entry type
	 */
	public function testCheckArrayBuiltType( string $handle, array $entry, string $expectedType ): void {
		$this->assertEquals( $expectedType, EntryPoint::fromArray( $handle, $entry )->type() );
	}

	/**
	 * @dataProvider providerStringType
	 *
	 * @param string $handle       Entry point handle
	 * @param string $path         Source file path
	 * @param string $expectedType Expected entry type
	 */
	public function testCheckStringBuiltType( string $handle, string $path, string $expectedType ): void {
		$this->assertEquals( $expectedType, EntryPoint::fromString( $handle, $path )->type() );
	}
}
