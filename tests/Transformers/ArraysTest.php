<?php

namespace Transformers;

use Kanopi\Components\Transformers\Arrays;
use PHPUnit\Framework\TestCase;

/**
 * Array test cases
 *
 * @package kanopi/components
 */
class ArraysTest extends TestCase {
	/**
	 * Test data for addMaybe
	 *
	 * @return array[]
	 */
	public function providerAddMaybe(): array {
		return [
			'Adds - Boolean Condition'          => [ [ 'stuff' ], 'things', true, true, 2 ],
			'Adds - Function Condition'         => [
				[ 'new', 'array' ],
				'item',
				function (): bool {
					return true;
				},
				true,
				3,
			],
			'Does not Add - Boolean Condition'  => [ [ 'stuff' ], 'things', false, false, 1 ],
			'Does not Add - Function Condition' => [
				[ 'new', 'array' ],
				'item',
				function (): bool {
					return false;
				},
				false,
				2,
			],
		];
	}

	/**
	 * @dataProvider providerAddMaybe
	 *
	 * @param array         $current    Starting set
	 * @param string        $addition   Text item to add
	 * @param callable|bool $condition  Condition to use
	 * @param bool          $isAdded    Whether to expect the value is added
	 * @param int           $checkCount Final check count
	 *
	 * @return void
	 */
	public function testAddMaybe(
		array $current,
		string $addition,
		callable|bool $condition,
		bool $isAdded,
		int $checkCount
	) {
		$testArrays = Arrays::from( $current )->addMaybe( $addition, $condition );
		$this->assertEquals( $checkCount, $testArrays->count() );
		$this->assertEquals( $isAdded, in_array( $addition, $testArrays->toArray(), true ) );
	}
}
