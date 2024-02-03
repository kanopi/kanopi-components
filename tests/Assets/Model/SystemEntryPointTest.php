<?php

namespace Assets\Model;

use Kanopi\Components\Assets\Model\SystemEntryPoint;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class SystemEntryPointTest extends TestCase {
	/**
	 * Test data provider for array versions
	 *  - Parameters are: Entry handle name, Entry configuration array, Expected entry type,
	 *      Expected optional status, Expected dependency count
	 *
	 * @return array[]
	 */
	public function providerConfiguration(): array {
		return [
			'Invalid Type - Becomes a script' => [
				'central',
				[
					'type' => 'something-invalid',
				],
				'script',
				false,
				0,
			],
			'Valid Type'                      => [
				'vendor-styles',
				[
					'dependencies' => [ 'some', 'thing' ],
					'type'         => 'style',
				],
				'style',
				false,
				2,
			],
			'Valid Type - Optional'           => [
				'vendor',
				[
					'dependencies' => [ 'other-handle' ],
					'type'         => 'combined',
					'optional'     => true,
				],
				'combined',
				true,
				1,
			],
		];
	}

	/**
	 * Test data provider for array versions
	 *  - Parameters are: Entry handle name, Entry configuration array,
	 *      Dependency handle to add, Expected dependency count
	 *
	 * @return array[]
	 */
	public function providerAddDependencies(): array {
		return [
			'Duplicate Dependency - No added dependencies' => [
				'sample-duplicate',
				[
					'dependencies' => [ 'some', 'thing' ],
				],
				'some',
				2,
			],
			'Add Valid Dependency'                         => [
				'sample-add',
				[
					'dependencies' => [ 'some', 'thing' ],
					'type'         => 'style',
				],
				'extra',
				3,
			],
		];
	}

	/**
	 * @dataProvider providerConfiguration
	 *
	 * @param string $handle               Entry point handle
	 * @param array  $entry                Entry configuration array
	 * @param string $expectedType         Expected entry type
	 * @param bool   $expectedOptional     Expected optional status
	 * @param int    $expectedDependencies Expected dependency count
	 */
	public function testConfiguration(
		string $handle,
		array $entry,
		string $expectedType,
		bool $expectedOptional,
		int $expectedDependencies
	): void {
		$systemEntry = SystemEntryPoint::fromArray( $handle, $entry );
		$this->assertEquals( $expectedDependencies, count( $systemEntry->dependencies() ) );
		$this->assertEquals( $expectedOptional, $systemEntry->optional() );
		$this->assertEquals( $expectedType, $systemEntry->type() );
	}

	/**
	 * @dataProvider providerAddDependencies
	 *
	 * @param string $handle               Entry point handle
	 * @param array  $entry                Entry configuration array
	 * @param string $addedDependency      Dependency handle to add
	 * @param int    $expectedDependencies Expected dependency count
	 */
	public function testAddDependencies(
		string $handle,
		array $entry,
		string $addedDependency,
		int $expectedDependencies
	): void {
		$systemEntry = SystemEntryPoint::fromArray( $handle, $entry )->addDependency( $addedDependency );
		$this->assertEquals( $expectedDependencies, count( $systemEntry->dependencies() ) );
	}
}
