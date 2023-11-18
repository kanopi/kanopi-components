<?php

namespace Kanopi\Components\Repositories\WordPress\Acf;

use Kanopi\Components\Model\Data\WordPress\Acf\FlexContentMetaColumns;
use Kanopi\Components\Model\Collection\EntityIterator;
use Kanopi\Components\Repositories\ISetReader;
use Kanopi\Components\Transformers\Arrays;

/**
 * Access ACF Flex Content Areas information for each post
 *
 * @package kanopi-components
 */
class FlexContentAreas implements ISetReader {
	/**
	 *  {@inheritDoc}
	 *
	 *  Finds all posts associated with requested Flex Content areas passed in the $filter parameter
	 *      - Pass an array of Flex Content slugs
	 *
	 */
	public function read( $_filter = null ): EntityIterator {
		global $wpdb;

		$transform = [];

		if ( ! empty( $_filter ) && is_array( $_filter ) ) {
			$areaPlaceholders = Arrays::from( array_fill( 0, count( $_filter ), '%s' ) )->join();

			/**
			 * - Intended to read current areas for varying filters in CLI Commands/Dashboards/etc. Cache elsewhere.
			 * - Dynamic number of areas formatted in $typePlaceholders, i.e. %s,%s,%s for 3
			 */
			// phpcs:disable WordPress.DB.DirectDatabaseQuery
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
			// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
					    wp.ID as postId,
					    wp.post_status as postStatus,
					    wp.post_title as postTitle,
					    wp.post_type as postType,
					    wpm.meta_key as contentType,
					    wpm.meta_value as contentHeaders
					FROM
					    {$wpdb->prefix}postmeta wpm
					LEFT OUTER JOIN
					    {$wpdb->prefix}posts wp
					ON
					    wpm.post_id = wp.ID
					WHERE
					    wpm.meta_value != ''
					    AND wp.post_type != 'revision'
					    AND wp.post_status NOT IN ('draft', 'expired', 'trash')
					    AND wpm.meta_value NOT LIKE 'a:0:%'
					    AND wpm.meta_key IN ($areaPlaceholders)",
					$_filter
				),
				ARRAY_A
			);

			$transform = array_map(
				function ( $item ) {
					$post = new FlexContentMetaColumns();

					$post->postId          = $item['postId'] ?? 0;
					$post->postStatus      = $item['postStatus'] ?? '';
					$post->postTitle       = $item['postTitle'] ?? '';
					$post->postType        = $item['postType'] ?? '';
					$post->flexContentType = $item['contentType'] ?? '';

					// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- ACF uses serialize()
					$headers                  = unserialize( $item['contentHeaders'] ?? '' );
					$post->flexContentHeaders = is_array( $headers ) ? $headers : [];

					return $post;
				},
				$results
			);
		}

		return new EntityIterator( $transform, FlexContentMetaColumns::class );
	}
}
