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
	 * Generate an audit report of all Site media, segmented to show those in and out of the Media Library
	 *
	 * ## OPTIONS
	 *
	 *  <directory>
	 *   : Directory to write the audit exports
	 *
	 * <filePrefix>
	 *   : Prefix for each of the generated CSV files
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
	 *        wp kanopi-report-post-type media ./ audit-
	 *
	 * @subcommand media
	 */
	public function media( $_cliArguments, $_associative_arguments ): void {
		try {
			$directory           = $_cliArguments[0] ?? '';
			$filePrefix          = $_cliArguments[1] ?? '';
			$replacementSiteUrl  = $_associative_arguments['replace-site-url'] ?? '';
			$statusSlugs         = $_associative_arguments['post-statuses'] ?? '';
			$typeSlugs           = $_associative_arguments['post-types'] ?? '';
			$usePrivatePostTypes = isset( $_associative_arguments['private-post-types'] );

			if ( empty( $directory ) ) {
				throw new InvalidArgumentException( 'Specify a directory for the audit files' );
			}

			if ( empty( $filePrefix ) ) {
				throw new InvalidArgumentException( 'Specify a prefix for each of the audit files' );
			}

			$defaultPathPattern = '/(?:href|src)=["\']([^"\']+\.(mp3|mp4|avi|mov|jpg|jpeg|png|gif|svg|webp|apng|avif|pdf))["\']/i';
			$defaultSizePattern = '/(https?:\/\/[^\/]+\/(?:[^\/]+\/)*[^\/]+)(?:-(\d+x\d+))\.(\w+)/i';

			/**
			 * Filter the regex pattern to parse all media paths from each posts content
			 *  - Default searches for all 'href' and 'src' URLs in the provided content
			 *  - Regex is a preg_match_all and uses match array 1
			 *
			 * @param string $defaultPathPattern Default Path regex pattern
			 */
			$mediaPathPattern = apply_filters( 'kanopi_report_media_path_regex_pattern', $defaultPathPattern );

			/**
			 * Filter the regex pattern to parse out resized image paths, extract the size if present
			 *  - Processing to read and determine if a Media URL is the Default URL or contains a sized variant
			 *  - Constructs the Default URL from concatenating matches 1 and 3
			 *  - The size variant is expected in match 2
			 *  - For no match, the URL is considered the Default, i.e. it is the full image or a document with no size
			 *
			 * @param string $defaultSizePattern Default URL Size regex pattern
			 */
			$mediaUrlSizePattern = apply_filters( 'kanopi_report_media_url_size_regex_pattern', $defaultSizePattern );

			// Execution tracking
			$start = new DateTime();

			// Image audit output array
			$audit = Arrays::fresh();

			// Image size tracking array
			$sizes = Arrays::from( [ 'Default' => 0 ] );

			// Process statistics tracking
			$statistics = new IndexedProcessStatistics();

			// Find all available post types for a name lookup
			$postTypes = $this->readPostTypes( $typeSlugs, $usePrivatePostTypes );

			// Set of published/private posts in requested post types, including any assigned template file
			$this->postWriter->changeFilters(
				new WordPressEntityFilters(
					[ '_wp_page_template' ],
					! empty( $statusSlugs ) ? explode( ',', $statusSlugs ) : [ 'publish', 'private' ],
					[],
					array_keys( $postTypes->toArray() )
				)
			);
			$content = $this->postWriter->read();
			$statistics->incomingTotal( $content->count() );

			// Current site URL
			$siteUrl = home_url();
			if ( str_ends_with( $siteUrl, '/' ) ) {
				$siteUrl = rtrim( $siteUrl, '/' );
			}
			$baseMediaUrl = wp_upload_dir( null, true, true )['baseurl'] ?? $siteUrl . '/wp-content/uploads';

			$mediaUrls = Arrays::fresh();

			/**
			 * Use the Content Index to iterate over and generate the final audit
			 *
			 * @var GenericWordPressEntity $contentPost Current WordPress post entity
			 */
			foreach ( $content as $postIdentifier ) {
				$contentPost   = $this->postWriter->readByIndexIdentifier( $postIdentifier );
				$postUrl       = get_permalink( $contentPost->indexIdentifier() );
				$postTypeName  = $postTypes->readIndex( $contentPost->systemEntityName() )
					?? $contentPost->systemEntityName();
				$template      = $contentPost->metaFields->readIndex( '_wp_page_template' );
				$postTimestamp = strtotime( $contentPost->datePublished() );

				/**
				 * Standard WordPress content filters to mimic the front-end post content template process
				 */
				$postContent = apply_filters(
					'the_content',
					get_the_content(
						null,
						false,
						$contentPost->internalSystemEntity() ?? $contentPost->indexIdentifier()
					)
				);

				/**
				 * Filters the content for a given post, allows additional processing for content stored
				 * outside the standard provided $postContent, for instance in ACF flex content areas.
				 *
				 * Separate from 'the_content' to avoid conflicts with normal site operation.
				 *
				 * @param string                 $postContent Current post content with 'the_content' filters applied
				 * @param GenericWordPressEntity $contentPost WordPress post entity
				 * @param string                 $template    Template meta '_wp_page_template' of the current post
				 */
				$renderContent = apply_filters(
					'kanopi_report_media_path_rendered_content',
					$postContent,
					$contentPost,
					$template
				);

				// Process the Post content to find all referenced Media items
				preg_match_all( $mediaPathPattern, $renderContent, $matches );

				$mediaPaths     = Arrays::from( $matches[1] ?? [] );
				$siteMedia      = $mediaPaths->filter(
					function ( string $_item ) use ( $siteUrl ): bool {
						return str_starts_with( $_item, $siteUrl );
					}
				)->filterUnique();
				$nonUploadMedia = Arrays::fresh();

				/**
				 * @var string $media Media URL to process
				 */
				foreach ( $siteMedia as $media ) {
					// Track if the file is inside or outside the Media Library
					$inMediaLibrary = str_starts_with( $media, $baseMediaUrl );

					$nonUploadMedia->addMaybe( $media, ! $inMediaLibrary );

					// Find the image size and base file
					$matchCount = preg_match( $mediaUrlSizePattern, $media, $mediaUrlSizes );
					$hasSize    = 0 < $matchCount;
					$fileUrl    = $hasSize ? $mediaUrlSizes[1] . '.' . $mediaUrlSizes[3] : $media;
					$fileSize   = $hasSize ? $mediaUrlSizes[2] : 'Default';

					// Check for replacement URL base
					$fileUrl = ! empty( $replacementSiteUrl )
						? str_replace( $siteUrl, $replacementSiteUrl, $fileUrl )
						: $fileUrl;

					// Track the amount of images of a certain size
					$sizeCount = ( $sizes->readIndex( $fileSize ) ?? 0 ) + 1;
					$sizes->writeIndex( $fileSize, $sizeCount );

					/**
					 * Create/Update the Audit Media Tracking item
					 *
					 * @var Arrays $currentMediaItem
					 */
					$currentMediaItem = $mediaUrls->readIndex( $fileUrl ) ??
						Arrays::from(
							[
								'Type'      => $inMediaLibrary ? 'Library' : 'Legacy',
								'URL'       => $fileUrl,
								'Last Used' => '',
								'Default'   => 0,
							]
						);
					$currentMediaItem->writeIndex( $fileSize, ( $currentMediaItem->readIndex( $fileSize ) ?? 0 ) + 1 );
					$currentMediaTimestamp = strtotime( $currentMediaItem->readIndex( 'Last Used' ) );
					if ( $currentMediaTimestamp < $postTimestamp ) {
						$currentMediaItem->writeIndex( 'Last Used', $contentPost->datePublished() );
					}
					$mediaUrls->writeIndex( $fileUrl, $currentMediaItem );
				}

				$auditEntry = Arrays::from(
					[
						'ID'            => $contentPost->indexIdentifier(),
						'Title'         => $contentPost->title(),
						'Type'          => $postTypeName,
						'Public'        => 'publish' === $contentPost->status() ? 'Yes' : 'No',
						'Published'     => $contentPost->datePublished(),
						'Modified'      => $contentPost->dateLastModified(),
						'Template'      => $template,
						'Site URL'      => ! empty( $replacementSiteUrl )
							? str_replace( $siteUrl, $replacementSiteUrl, $postUrl )
							: $postUrl,
						'Total Images'  => $siteMedia->count(),
						'Legacy Images' => $nonUploadMedia->count(),
					]
				);

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
					'Template',
					'Site URL',
					'Total Images',
					'Legacy Images',
				]
			);

			// Create CSV Report of all Posts and their Media counts
			$this->csvService->writeFile(
				$reportColumns->toArray(),
				$audit->readSubArrays(),
				$directory . $filePrefix . 'content-media-report.csv'
			);

			// Sort the list of used images sizes, ensuring Default is first
			$currentSizes = Arrays::fresh();
			foreach ( array_keys( $sizes->toArray() ) as $size ) {
				if ( 'Default' === $size ) {
					continue;
				}
				$currentSizes->add( $size );
			}
			$currentSizes->filterUnique();

			// Rebuild the Media URL listing to zero out all sizes
			$auditUrls = Arrays::fresh();
			foreach ( $mediaUrls as $mUrl ) {
				$current = Arrays::fresh()
					->writeIndex( 'Type', $mUrl->readIndex( 'Type' ) )
					->writeIndex( 'URL', $mUrl->readIndex( 'URL' ) )
					->writeIndex( 'Last Used', $mUrl->readIndex( 'Last Used' ) )
					->writeIndex( 'Default', $mUrl->readIndex( 'Default' ) );

				foreach ( $currentSizes as $size ) {
					$current->writeIndex( $size, $mUrl->readIndex( $size ) ?? 0 );
				}
				$auditUrls->add( $current );
			}

			// Create CSV Report of all Media URLs with counts of usage by size
			$this->csvService->writeFile(
				Arrays::from(
					[
						'Type',
						'URL',
						'Last Used',
						'Default',
					]
				)->append( $currentSizes->toArray() )->toArray(),
				$auditUrls->readSubArrays(),
				$directory . $filePrefix . 'content-media-urls.csv'
			);

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
