<?php

namespace Kanopi\Components\Commands\WordPress\Report;

use DateTime;
use Exception;
use InvalidArgumentException;
use Kanopi\Components\Logger\ILogger;
use Kanopi\Components\Logger\WordPress\CLI;
use Kanopi\Components\Model\Configuration;
use Kanopi\Components\Model\Data\Process\IndexedProcessStatistics;
use Kanopi\Components\Model\Data\WordPress\{GenericWordPressEntity, WordPressEntityFilters};
use Kanopi\Components\Repositories\IGroupSetWriter;
use Kanopi\Components\Repositories\WordPress\{PostMetaKeys, PostQuery, PostTerms, Taxonomy};
use Kanopi\Components\Services\External\LeagueCsv;
use Kanopi\Components\Services\System\WordPress\GenericWordPressEntityWriter;
use Kanopi\Components\Transformers\Arrays;
use WP_CLI_Command;
use WP_Post_Type;
use WP_Taxonomy;
use WP_Term;

/**
 * Dynamic post type audit report generation
 *
 * @package kanopi-components
 */
class PostType extends WP_CLI_Command {
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
	 * Post entity service
	 *
	 * @var GenericWordPressEntityWriter
	 */
	private GenericWordPressEntityWriter $postWriter;
	/**
	 * Taxonomy term service
	 *
	 * @var IGroupSetWriter
	 */
	private IGroupSetWriter $taxonomyService;

	/**
	 * Legacy Rewrites constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->csvService      = new LeagueCsv( Configuration\LeagueCsv::ReadWrite() );
		$this->logger          = new CLI();
		$this->postWriter      = new GenericWordPressEntityWriter( new PostQuery(), new PostMetaKeys(), new PostTerms(), 1 );
		$this->taxonomyService = new Taxonomy();
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing -- Thrown errors are caught immediately
	// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag -- WP CLI has a different doc format

	/**
	 * Generate an audit report of all site content
	 *
	 * ## OPTIONS
	 *
	 *  <directory>
	 *   : Directory to write the audit exports
	 *
	 * <filePrefix>
	 *   : Prefix for each of the generated CSV files
	 *
	 * [--meta-keys[=<metaKeyList>]]
	 *     : Comma-delimited list of post meta keys to lookup and report
	 *    ---
	 *    default: ''
	 *    ---
	 *
	 * [--post-statuses[=<postStatusList>]]
	 *    : Comma-delimited list of post status slugs to lookup and report
	 *   ---
	 *   default: all
	 *   ---
	 *
	 *  [--post-types[=<postTypeList>]]
	 *   : Comma-delimited list of post type slugs to lookup and report
	 *  ---
	 *  default: all
	 *  ---
	 *
	 * [--taxonomies[=<taxonomyList>]]
	 *  : Comma-delimited list of taxonomy slugs to lookup and report
	 * ---
	 * default: all
	 * ---
	 *
	 * [--private-post-types]
	 *  : Set to show private post types, otherwise only public are shown
	 * ---
	 * default: false
	 * ---
	 *
	 * [--replace-site-url[=<replacementSiteUrl>]]
	 *   : Rewrite the local domain and protocol in URLs, i.e. turn http://test.docksal.site into https://www.test.com
	 *  ---
	 *  default: ''
	 *  ---
	 *
	 * ## EXAMPLES
	 *
	 *        wp kanopi-report-post-type audit ./ audit-
	 *
	 * @subcommand audit
	 */
	public function audit( $_cliArguments, $_associative_arguments ): void {
		try {
			$directory           = $_cliArguments[0] ?? '';
			$filePrefix          = $_cliArguments[1] ?? '';
			$metaKeys            = $_associative_arguments['meta-keys'] ?? '';
			$replacementSiteUrl  = $_associative_arguments['replace-site-url'] ?? '';
			$statusSlugs         = $_associative_arguments['post-statuses'] ?? '';
			$typeSlugs           = $_associative_arguments['post-types'] ?? '';
			$taxonomySlugs       = $_associative_arguments['taxonomies'] ?? '';
			$usePrivatePostTypes = isset( $_associative_arguments['taxonomies'] );

			if ( empty( $directory ) ) {
				throw new InvalidArgumentException( 'Specify a directory for the audit files' );
			}

			if ( empty( $filePrefix ) ) {
				throw new InvalidArgumentException( 'Specify a prefix for each of the audit files' );
			}

			// Execution tracking
			$start = new DateTime();

			// Post Type audit output array
			$audit = Arrays::fresh();

			// Taxonomy per Post Type statistics array
			$taxonomyStats = Arrays::fresh();

			// Process statistics tracking
			$statistics = new IndexedProcessStatistics();

			// Find all available post types for a name lookup
			$postTypes = $this->readPostTypes( $typeSlugs, $usePrivatePostTypes );

			// Find all requested Post Meta keys
			$postMetaKeys = ! empty( $metaKeys ) ? explode( ',', $metaKeys ) : [];

			// Reads all requested Taxonomies along with their terms and sets up reporting columns
			$taxonomyList = $this->readTaxonomies( $taxonomySlugs );
			$terms        = Arrays::fresh();
			foreach ( $taxonomyList->toArray() as $taxonomy => $label ) {
				/**
				 * @var WP_Term $taxonomyTerm
				 */
				foreach ( $this->taxonomyService->read( $taxonomy ) as $taxonomyTerm ) {
					$terms->ensureSubArray( $taxonomy )->writeIndex( $taxonomyTerm->slug, $taxonomyTerm->name );
				}
			}

			// Set of posts with legacy URLs in the content
			$this->postWriter->changeFilters(
				new WordPressEntityFilters(
					$postMetaKeys,
					! empty( $statusSlugs ) ? explode( ',', $statusSlugs ) : [ 'publish', 'private' ],
					array_keys( $taxonomyList->toArray() ),
					array_keys( $postTypes->toArray() )
				)
			);
			$content = $this->postWriter->read();
			$statistics->incomingTotal( $content->count() );

			// Current site URL
			$siteUrl = home_url();

			/**
			 * Use the Content Index to iterate over and generate the final audit
			 *
			 * @var GenericWordPressEntity $post
			 */
			foreach ( $content as $postIdentifier ) {
				$post         = $this->postWriter->readByIndexIdentifier( $postIdentifier );
				$postTypeName = $postTypes->readIndex( $post->systemEntityName() ) ?? $post->systemEntityName();
				$postUrl      = get_permalink( $post->indexIdentifier() );

				$auditEntry = Arrays::from(
					[
						'ID'        => $post->indexIdentifier(),
						'Title'     => $post->title(),
						'Type'      => $postTypeName,
						'Public'    => 'publish' === $post->status() ? 'Yes' : 'No',
						'Published' => $post->datePublished(),
						'Modified'  => $post->dateLastModified(),
						'Site URL'  => ! empty( $replacementSiteUrl )
							? str_replace( $siteUrl, $replacementSiteUrl, $postUrl )
							: $postUrl,
					]
				);

				foreach ( $postMetaKeys as $key ) {
					$auditEntry->writeIndex( $key, $post->metaFields->readIndex( $key ) ?? '' );
				}

				foreach ( $taxonomyList->readSubArrays() as $taxonomySlug => $name ) {
					$termNames = $this->termNameLookup(
						$terms,
						$taxonomySlug,
						$post->taxonomies->readIndex( $taxonomySlug ) ?? Arrays::fresh()
					);

					// Add the Taxonomy Terms and the column to the Post Report audit report
					$auditEntry->writeIndex( $taxonomySlug, $termNames->join( '; ' ) );

					// Create a reporting view of each Taxonomies Terms to Usage Count per Post Type
					foreach ( $termNames->toArray() as $term ) {
						// Find/create current Taxonomy Term array
						$currentTerm = $taxonomyStats->ensureSubArray( $taxonomySlug )->ensureSubArray( $term );

						// Ensure it has a name
						if ( empty( $currentTerm->readIndex( 'Name' ) ) ) {
							$currentTerm->writeIndex( 'Name', $term );
						}

						$currentTermValue = $currentTerm->readIndex( $postTypeName ) ?? 0;
						$currentTerm->writeIndex( $postTypeName, $currentTermValue + 1 );
					}
				}

				$audit->add( $auditEntry->toArray() );
			}

			// Post Type report columns (without taxonomies, added below)
			$reportColumns = Arrays::from(
				[
					'ID',
					'Title',
					'Type',
					'Public',
					'Published',
					'Modified',
					'Site URL',
				]
			)->append( $postMetaKeys )->append( $taxonomyList->toArray() );

			// List of Unprocessed URLs by post
			$this->csvService->writeFile(
				$reportColumns->toArray(),
				$audit->toArray(),
				$directory . $filePrefix . 'post-report.csv'
			);

			foreach ( $taxonomyList->toArray() as $slug => $name ) {
				$currentTaxonomy = $taxonomyStats->readSubArrays( $slug );
				if ( empty( $currentTaxonomy ) ) {
					$this->logger->info( "No taxonomy terms found for $name" );
					continue;
				}

				$categoryHeaders = Arrays::from( [ 'Name' ] )->append( $postTypes->toArray() );
				$reporting       = Arrays::fresh();

				foreach ( $currentTaxonomy as $name => $postCounts ) {
					foreach ( $categoryHeaders->toArray() as $postType ) {
						$reporting->ensureSubArray( $name )->writeIndex( $postType, $postCounts[ $postType ] ?? 0 );
					}
				}

				$this->csvService->writeFile(
					$categoryHeaders->toArray(),
					$reporting->readSubArrays(),
					$directory . $filePrefix . str_replace( '_', '-', $slug ) . '.csv'
				);
			}

			$this->logger->table(
				[ 'Total', 'Updated', 'Skipped', 'Processed', 'Time Elapsed (h:m:s)' ],
				[
					[
						'Total'                => $statistics->incomingTotalAmount(),
						'Updated'              => $statistics->updatedAmount(),
						'Skipped'              => $statistics->skippedAmount(),
						'Processed'            => $statistics->processedTotalAmount(),
						'Time Elapsed (h:m:s)' => $start->diff( new DateTime() )->format( '%H:%i:%s' ),
					],
				]
			);
		}
		catch ( Exception $exception ) {
			$this->logger->error( 'Error while running the content audit: ' . $exception->getMessage() );
		}
	}

	/**
	 * Read an associative array of "post type slugs" to "post type name/label"
	 *  - When provided, limits post types to those requested, otherwise returns all
	 *
	 * @param string $_postTypesSlugs      Optional comma-delimited string of taxonomy slugs
	 * @param bool   $_usePrivatePostTypes True to show Private and Public post types only, otherwise only Public show
	 *
	 * @return Arrays
	 */
	private function readPostTypes( string $_postTypesSlugs, bool $_usePrivatePostTypes ): Arrays {
		$slugs  = ! empty( $_postTypesSlugs ) ? explode( ',', strtolower( $_postTypesSlugs ) ) : [];
		$useAll = empty( $slugs );

		$postTypeQuery = $_usePrivatePostTypes ? [] : [ 'public' => true ];
		$postTypes     = Arrays::fresh();

		/**
		 * @var WP_Post_Type $taxonomy
		 */
		foreach ( get_post_types( $postTypeQuery, 'objects' ) as $postType ) {
			if ( $useAll || in_array( $postType->name, $slugs, true ) ) {
				$postTypes->writeIndex( $postType->name, $postType->label );
			}
		}

		return $postTypes;
	}

	/**
	 * Read an associative array of "taxonomy slugs" to "taxonomy name/label"
	 *  - When provided, limits taxonomies to those requested, otherwise returns all
	 *
	 * @param string $_taxonomySlugs Optional comma-delimited string of taxonomy slugs
	 *
	 * @return Arrays
	 */
	private function readTaxonomies( string $_taxonomySlugs ): Arrays {
		$slugs      = ! empty( $_taxonomySlugs ) ? explode( ',', strtolower( $_taxonomySlugs ) ) : [];
		$useAll     = empty( $slugs );
		$taxonomies = Arrays::fresh();

		/**
		 * @var WP_Taxonomy $taxonomy
		 */
		foreach ( get_taxonomies( [], 'objects' ) as $taxonomy ) {
			if ( $useAll || in_array( $taxonomy->name, $slugs, true ) ) {
				$taxonomies->writeIndex( $taxonomy->name, $taxonomy->label );
			}
		}

		return $taxonomies;
	}

	/**
	 * Find the names for a given set of taxonomy terms
	 *
	 * @param Arrays $_taxonomyTerms Full set of taxonomy terms
	 * @param string $_taxonomyName  Taxonomy to target
	 * @param Arrays $_terms         Set of terms to find names
	 *
	 * @return Arrays
	 */
	private function termNameLookup( Arrays $_taxonomyTerms, string $_taxonomyName, Arrays $_terms ): Arrays {
		$names    = Arrays::fresh();
		$taxonomy = $_taxonomyTerms->ensureSubArray( $_taxonomyName );

		foreach ( $_terms->toArray() as $term ) {
			$names->add( $taxonomy->readIndex( $term->slug )?->name ?? $term->name );
		}

		return $names;
	}
}
