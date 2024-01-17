<?php

namespace Assets\Model;

use Kanopi\Components\Assets\Model\EntryAsset;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class EntryAssetTest extends TestCase {
	/**
	 * Test data provider for array versions
	 *
	 * @return array[]
	 */
	public function providerScriptPath(): array {
		return [
			'Invalid - No Script Path' => [
				'theme',
				[],
				null,
			],
			'Valid - Script Path'      => [
				'theme',
				[
					'js' => 'js/theme.1b08d747a2b3b1a1e1ad.js',
				],
				'js/theme.1b08d747a2b3b1a1e1ad.js',
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
				'editor',
				[
					'js' => 'js/editor.1b08d747a2b3b1a1e1ad.js',
				],
				null,
			],
			'Valid - Style Path'      => [
				'editor',
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
	 * @param string      $entryName     Entry point name
	 * @param array       $manifestPaths Entry manifest path array
	 * @param string|null $expected      Expected path
	 */
	public function testScriptPath( string $entryName, array $manifestPaths, ?string $expected ): void {
		$this->assertEquals( $expected, EntryAsset::fromArray( $entryName, $manifestPaths )->script() );
	}

	/**
	 * @dataProvider providerStylePath
	 *
	 * @param string      $entryName     Entry point name
	 * @param array       $manifestPaths Entry manifest path array
	 * @param string|null $expected      Expected path
	 */
	public function testStylePath( string $entryName, array $manifestPaths, ?string $expected ): void {
		$this->assertEquals( $expected, EntryAsset::fromArray( $entryName, $manifestPaths )->style() );
	}
}
