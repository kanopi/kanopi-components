<?php

namespace Assets\Transformers;

use DateTime;
use Kanopi\Components\Assets\Transformers\JavaScriptConfigurationParser;
use Kanopi\Components\Repositories\LocalFileReader;
use PHPUnit\Framework\TestCase;

/**
 * Entry point test cases
 *
 * @package kanopi/components
 */
class JavaScriptConfigurationParserTest extends TestCase {
	/**
	 * Test data provider for string versions
	 *  - First run is always slower, no matter which file goes first
	 *
	 * @return array[]
	 */
	public function providerPath(): array {
		return [
			'JS File   | Invalid devServer | Valid filePatterns'   => [
				__DIR__ . '/../Configurations/sample.js',
				true,
				false,
			],
			'JSON File | Valid devServer | Valid filePatterns'     => [
				__DIR__ . '/../Configurations/sample.json',
				false,
				false,
			],
			'JS File   | Invalid devServer | Invalid filePatterns' => [
				__DIR__ . '/../Configurations/sample-invalid.js',
				true,
				true,
			],
		];
	}

	/**
	 * @dataProvider providerPath
	 *
	 * @param string $_filePath          Source file path
	 * @param bool   $_devServerError    Has an error/invalid devServer section
	 * @param bool   $_filePatternsError Has an error/invalid filePatterns section
	 */
	public function testConfigurationReader(
		string $_filePath,
		bool $_devServerError,
		bool $_filePatternsError
	): void {
		$start = new DateTime( 'now' );

		// Test
		$localFile     = ( new LocalFileReader() )->read( $_filePath );
		$readEnd       = new DateTime( 'now' );
		$configuration = ( new JavaScriptConfigurationParser() )->read( $localFile )->collection();
		$parseEnd      = new DateTime( 'now' );

		// Calculate times, since DateIntervals are incomparable, check if intervals are longer than 1 ms
		$readLength  = $readEnd->diff( $start );
		$isReadLong  = 0 < $readLength->h || 0 < $readLength->i || 0 < $readLength->s || .002 < $readLength->f;
		$parseLength = $parseEnd->diff( $readEnd );
		$isParseLong = 0 < $parseLength->h || 0 < $parseLength->i || 0 < $parseLength->s || .002 < $parseLength->f;

		$this->assertIsArray( $configuration );
		$this->assertIsArray( $configuration['devServer'] );
		$this->assertIsArray( $configuration['filePatterns'] );
		$this->assertEquals( $_devServerError, isset( $configuration['devServer']['error'] ), 'devServer invalid' );
		$this->assertEquals( $_filePatternsError, isset( $configuration['filePatterns']['error'] ), 'filePatterns invalid' );
		$this->assertFalse( $isReadLong, 'Read longer than 2 ms: ' . $readLength->format( '%h:%i:%s.%f' ) );
		$this->assertFalse( $isParseLong, 'Parse longer than 2 ms: ' . $parseLength->format( '%h:%i:%s.%f' ) );
	}
}
