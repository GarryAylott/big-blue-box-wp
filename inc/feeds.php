<?php
/**
 * RSS feed optimisations and custom feed logic.
 *
 * @package Big_Blue_Box
 */

defined( 'ABSPATH' ) || exit;

/**
 * Force excerpts in feeds (safe and core-supported).
 */
add_filter( 'rss_use_excerpt', '__return_true' );

/**
 * Feed query handling.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! $query->is_main_query() || ! $query->is_feed() ) {
			return;
		}

		// Performance only.
		$query->set( 'no_found_rows', true );

		/**
		 * Articles feed:
		 * /category/articles/feed/
		 * Everything EXCEPT podcasts.
		 */
		if ( $query->is_category( 'articles' ) ) {
			// Remove the implicit "articles" category constraint.
			$query->set( 'cat', '' );
			$query->set( 'category_name', '' );

			// Exclude podcasts.
			$query->set(
				'tax_query',
				array(
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => array( 'podcasts' ),
						'operator' => 'NOT IN',
					),
				)
			);
		}
	}
);

/**
 * Declare Media RSS namespace.
 */
add_action(
	'rss2_ns',
	function () {
		echo ' xmlns:media="http://search.yahoo.com/mrss/"';
	}
);

/**
 * Add featured image to feed items via Media RSS.
 */
add_action(
	'rss2_item',
	function () {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$attachment_id = get_post_thumbnail_id();
		$image         = wp_get_attachment_image_src( $attachment_id, 'post-featured-card' );

		if ( ! $image ) {
			return;
		}

		$mime_type = get_post_mime_type( $attachment_id );

		printf(
			'<media:content url="%s" medium="image" type="%s" />' . PHP_EOL .
			'<media:thumbnail url="%s" />' . PHP_EOL,
			esc_url( $image[0] ),
			esc_attr( $mime_type ),
			esc_url( $image[0] )
		);
	}
);
