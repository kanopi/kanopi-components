<?php

namespace Kanopi\Components\Commands\WordPress;

use Exception;
use InvalidArgumentException;
use Kanopi\Components\Model\Configuration;
use Kanopi\Components\Model\Data\WordPress\Acf\FlexContentMetaColumns;
use Kanopi\Components\Repositories\WordPress\Acf\FlexContentAreas;
use Kanopi\Components\Services\External\LeagueCsv;
use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Logger\WordPress\CLI;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Transformers\Arrays;
use WP_CLI_Command;
use WP_Post_Type;

/**
 * WP-CLI Command to Generate a report of Flexible Content usage for a given site
 *
 * @package kanopi-components
 */
class FlexContentReport extends WP_CLI_Command {
	/**
	 * Standard logger interface
	 *
	 * @var ILogger
	 */
	private ILogger $logger;
	/**
	 * CSV writer service
	 *
	 * @var LeagueCsv
	 */
	private LeagueCsv $csvService;
	/**
	 * Post flex content association reader
	 *
	 * @var ISetReader
	 */
	private ISetReader $columnReader;

	/**
	 * Legacy Rewrites constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->csvService   = new LeagueCsv( Configuration\LeagueCsv::ReadWrite() );
		$this->logger       = new CLI();
		$this->columnReader = new FlexContentAreas();
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing -- Thrown errors are caught immediately
	// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag -- WP CLI has a different doc format

	/**
	 * Generate an audit report of the sites ACF flexible content areas
	 *  - Reads from all post types on the site
	 *  - Skips post revisions and any post with the Draft, Expired, or Trash status
	 *  - Matches the set of posts with one or more rows of flexible content in the supplied list of content types
	 *
	 * ## OPTIONS
	 *
	 * <directory>
	 *    : Directory to write the audit exports
	 *
	 * <directory>
	 *   : Directory to write the audit exports
	 *
	 * <filePrefix>
	 *   : Prefix for each of the generated CSV files
	 *
	 * <flexContentTypes>
	 *   : Comma-delimited set of Flex Content Types to audit
	 *
	 * ## EXAMPLES
	 *
	 *        wp pen-flex-content-report audit ./ pen- page_flex_content,report_flex_content
	 *
	 * @subcommand audit
	 */
	public function audit( $_cliArguments ): void {
		try {
			$directory        = $_cliArguments[0] ?? '';
			$filePrefix       = $_cliArguments[1] ?? '';
			$flexContentTypes = $_cliArguments[2] ?? '';

			if ( empty( $directory ) ) {
				throw new InvalidArgumentException( 'Specify a directory for the audit files' );
			}

			if ( empty( $filePrefix ) ) {
				throw new InvalidArgumentException( 'Specify a prefix for each of the audit files' );
			}

			if ( empty( $flexContentTypes ) ) {
				throw new InvalidArgumentException( 'Specify one or more flex content types to audit' );
			}

			$postReport    = Arrays::fresh();
			$typeRowReport = Arrays::fresh();

			$postTypes     = get_post_types( [], 'object' );
			$postTypeNames = Arrays::fresh();

			/**
			 * Form a post type name lookup for the report
			 *
			 * @var WP_Post_Type $type
			 */
			foreach ( $postTypes as $type ) {
				$postTypeNames->writeIndex( $type->name, $type->label );
			}

			$postStatusNames = Arrays::from(
				[
					'draft'   => 'Draft',
					'pending' => 'Pending',
					'private' => 'Private',
					'publish' => 'Published',
				]
			);

			$flexAreas = $this->columnReader->read( explode( ',', $flexContentTypes ) );

			/**
			 * Transform the Flex Content lookup into the report output
			 *
			 * @var FlexContentMetaColumns $post
			 */
			foreach ( $flexAreas as $post ) {
				$postReport->add(
					[
						'Post ID'              => $post->postId,
						'Post Title'           => $post->postTitle,
						'Post Type'            => $postTypeNames->readIndex( $post->postType ) ?? $post->postType,
						'Post Status'          => $postStatusNames->readIndex( $post->postStatus ) ?? $post->postStatus,
						'Flex Content Type'    => $post->flexContentType,
						'Flex Content Columns' => count( $post->flexContentHeaders ),
						'Flex Content Headers' => Arrays::from( $post->flexContentHeaders )->join( ' | ' ),
					]
				);

				foreach ( $post->flexContentHeaders as $header ) {
					$flexType = $typeRowReport->ensureSubArray( $post->flexContentType );
					$flexType->writeIndex( $header, ( $flexType->readIndex( $header ) ?? 0 ) + 1 );
				}
			}

			$this->csvService->writeFile(
				[
					'Post ID',
					'Post Title',
					'Post Type',
					'Post Status',
					'Flex Content Type',
					'Flex Content Columns',
					'Flex Content Headers',
				],
				$postReport->toArray(),
				$directory . $filePrefix . 'flex-areas.csv'
			);

			foreach ( $typeRowReport->readSubArrays() as $flexType => $typeCounts ) {
				$this->csvService->writeFile(
					array_keys( $typeCounts ),
					[ $typeCounts ],
					$directory . $filePrefix . $flexType . '.csv'
				);
			}
		}
		catch ( Exception $exception ) {
			$this->logger->error( 'Error while running the content audit: ' . $exception->getMessage() );
		}
	}
}
