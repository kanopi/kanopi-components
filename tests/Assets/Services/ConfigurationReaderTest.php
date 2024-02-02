<?php

namespace Assets\Services;

use Kanopi\Components\Assets\Model\EntryPoint;
use Kanopi\Components\Assets\Model\SystemEntryPoint;
use Kanopi\Components\Assets\Model\WebpackConfiguration;
use Kanopi\Components\Model\Collection\EntityIterator;
use PHPUnit\Framework\TestCase;

/**
 * Configuration reader test cases
 *
 * @package kanopi/components
 */
class ConfigurationReaderTest extends TestCase {
	const SAMPLE_ENTRY_POINTS = [
		'sample-app'  => [
			'path' => './path/to/application.tsc',
			'type' => 'combined',
		],
		'sample-css'  => [
			'path' => './path/to/whatever.css',
			'type' => 'style',
		],
		'sample-js'   => [
			'path' => './path/to/whatever.js',
			'type' => 'script',
		],
		'sample-jsx'  => [
			'path' => './path/to/whatever.jsx',
			'type' => 'script',
		],
		'sample-sass' => [
			'path' => './path/to/whatever.sass',
			'type' => 'style',
		],
		'sample-scss' => [
			'path' => './path/to/whatever.scss',
			'type' => 'style',
		],
		'sample-tsx'  => [
			'path' => './path/to/whatever.tsc',
			'type' => 'script',
		],
	];

	/**
	 * Test data provider for configuration reads
	 *
	 * @return array[]
	 */
	public function providerRead(): array {
		return [
			'Missing Entry Points' => [
				[
					'error' => 'Some error',
				],
				0,
			],
			'Entry Points'         => [
				[
					'filePatterns' => [
						'entryPoints' => self::SAMPLE_ENTRY_POINTS,
					],
				],
				7,
			],
		];
	}

	/**
	 * Test data provider for reading registered handle prefixes
	 *
	 * @return array[]
	 */
	public function providerPrefixRegisteredHandle(): array {
		return [
			'Default Prefix'    => [
				[
					'filePatterns' => [
						'entryPoints' => self::SAMPLE_ENTRY_POINTS,
					],
				],
				'vendor',
				'kanopi-pack-vendor',
			],
			'Customized Prefix' => [
				[
					'filePatterns' => [
						'entryPoints'            => self::SAMPLE_ENTRY_POINTS,
						'registeredHandlePrefix' => 'custom-prefix-',
					],
				],
				'vendor',
				'custom-prefix-vendor',
			],
		];
	}

	/**
	 * @dataProvider providerRead
	 *
	 * @param iterable $source      Source configuration
	 * @param int      $handleCount Count of valid handles
	 */
	public function testRead( iterable $source, int $handleCount ): void {
		$configuration = WebpackConfiguration::fromJson(
			$source,
			EntityIterator::fromArray(
				[
					SystemEntryPoint::fromArray( 'vendor', [ 'type' => 'script' ] ),
					SystemEntryPoint::fromArray( 'runtime', [ 'type' => 'script' ] ),
					SystemEntryPoint::fromArray( 'central', [ 'type' => 'script' ] ),
				],
				EntryPoint::class
			)
		);
		$this->assertEquals( $handleCount, $configuration->entryPoints()->count() );
		$this->assertEquals( 3, $configuration->systemEntryPoints()->count() );
		$this->assertContainsOnlyInstancesOf( EntryPoint::class, $configuration->entryPoints() );
	}

	/**
	 * @dataProvider providerPrefixRegisteredHandle
	 *
	 * @param iterable $source         Source configuration
	 * @param string   $handle         Handle to prefix
	 * @param string   $expectedHandle Expected handle with prefix
	 */
	public function testPrefixRegisteredHandle( iterable $source, string $handle, string $expectedHandle ): void {
		$configuration = WebpackConfiguration::fromJson(
			$source,
			EntityIterator::fromArray(
				[
					SystemEntryPoint::fromArray( 'vendor', [ 'type' => 'script' ] ),
					SystemEntryPoint::fromArray( 'runtime', [ 'type' => 'script' ] ),
					SystemEntryPoint::fromArray( 'central', [ 'type' => 'script' ] ),
				],
				EntryPoint::class
			)
		);
		$this->assertEquals( $expectedHandle, $configuration->prefixRegisteredHandle( $handle ) );
	}
}
