<?php

namespace Assets\Model;

use Kanopi\Components\Assets\Model\EntryAsset;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class EntryPointTest extends TestCase {
	/**
	 * Test data provider for array versions
	 *
	 * @return array[]
	 */
	public function providerScriptPath(): array {
		return [
			'Invalid - No Script Path' => [
				[],
				null,
			],
			'Valid - Script Path'      => [
				[
					'js' => 'js/editor.1b08d747a2b3b1a1e1ad.js',
				],
				'js/editor.1b08d747a2b3b1a1e1ad.js',
			],
		];
	}

	/**
	 * Test data provider for style paths
	 *
	 * @return array[]
	 */
	public function providerStylePath(): array {
		return [
			'Invalid - No Style Path' => [
				[
					'js' => 'js/editor.1b08d747a2b3b1a1e1ad.js',
				],
				null,
			],
			'Valid - Style Path'      => [
				[
					'css' => 'css/editor.5a1c48eeabe59064833c.css',
					'js'  => 'js/editor.1b08d747a2b3b1a1e1ad.js',
				],
				'css/editor.5a1c48eeabe59064833c.css',
			],
		];
	}

	/**
	 * @dataProvider providerScriptPath
	 *
	 * @param array       $manifestPaths Entry manifest path array
	 * @param string|null $expected      Expected path
	 */
	public function testScriptPath( array $manifestPaths, ?string $expected ): void {
		$this->assertEquals( $expected, EntryAsset::fromArray( $manifestPaths )->script() );
	}

	/**
	 * @dataProvider providerStylePath
	 *
	 * @param array       $manifestPaths Entry manifest path array
	 * @param string|null $expected      Expected path
	 */
	public function testStylePath( array $manifestPaths, ?string $expected ): void {
		$this->assertEquals( $expected, EntryAsset::fromArray( $manifestPaths )->style() );
	}
}
