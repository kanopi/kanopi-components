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
	 * Test data provider for paths
	 *
	 * @return array[]
	 */
	public function providerPaths(): array {
		return [
			'Invalid - No Paths'        => [
				'theme',
				[],
				null,
				null,
			],
			'Valid - Script Path Only'  => [
				'theme',
				[
					'js' => 'js/theme.1b08d747a2b3b1a1e1ad.js',
				],
				'js/theme.1b08d747a2b3b1a1e1ad.js',
				null,
			],
			'Invalid - Style Path Only' => [
				'editor',
				[
					'css' => 'css/editor.5a1c48eeabe59064833c.css',
				],
				null,
				'css/editor.5a1c48eeabe59064833c.css',
			],
			'Valid - Both Paths'        => [
				'editor',
				[
					'css' => 'css/editor.5a1c48eeabe59064833c.css',
					'js'  => 'js/editor.1b08d747a2b3b1a1e1ad.js',
				],
				'js/editor.1b08d747a2b3b1a1e1ad.js',
				'css/editor.5a1c48eeabe59064833c.css',
			],
		];
	}

	/**
	 * @dataProvider providerPaths
	 *
	 * @param string      $entryName      Entry point name
	 * @param array       $manifestPaths  Entry manifest path array
	 * @param string|null $expectedScript Expected script path
	 * @param string|null $expectedStyle  Expected style path
	 */
	public function testStylePath(
		string $entryName,
		array $manifestPaths,
		?string $expectedScript,
		?string $expectedStyle
	): void {
		$this->assertEquals( $expectedScript, EntryAsset::fromArray( $entryName, $manifestPaths )->script() );
		$this->assertEquals( $expectedStyle, EntryAsset::fromArray( $entryName, $manifestPaths )->style() );
	}
}
