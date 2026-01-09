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
 * Disable heavy content filters during feed generation.
 * Runs early to catch all feed requests.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_feed() ) {
			return;
		}

		// Remove DOM-parsing filter
		remove_filter( 'the_content', 'bbbx_clean_gutenberg_images', 20 );

		// Remove article promo banners
		remove_filter( 'the_content', 'bbb_insert_article_promo_banners', 15 );

		// Remove the lead paragraph filter
		remove_filter( 'the_content', 'first_paragraph' );
	},
	1
);

/**
 * Prevent ACF from making unnecessary queries during feed generation.
 * Returns cached/stored values only, skip ACF processing.
 */
add_filter(
	'acf/pre_load_value',
	function ( $value, $post_id, $field ) {
		if ( is_feed() ) {
			// Return whatever is in post meta directly, skip ACF processing
			$meta_key = $field['name'] ?? '';
			if ( $meta_key && is_numeric( $post_id ) ) {
				return get_post_meta( $post_id, $meta_key, true );
			}
		}
		return $value;
	},
	10,
	3
);

/**
 * Feed query handling with performance optimisations.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! $query->is_main_query() || ! $query->is_feed() ) {
			return;
		}

		// Performance optimisations
		$query->set( 'no_found_rows', true );
		$query->set( 'update_post_meta_cache', true ); // We need meta for featured images
		$query->set( 'update_post_term_cache', false );

		// Limit feed items if not already limited
		if ( ! $query->get( 'posts_per_rss' ) ) {
			$query->set( 'posts_per_page', 10 );
		}

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
 * Strip any shortcodes from feed content to prevent execution.
 */
add_filter(
	'the_content_feed',
	function ( $content ) {
		return strip_shortcodes( $content );
	},
	1
);

/**
 * Strip any shortcodes from feed excerpts.
 */
add_filter(
	'the_excerpt_rss',
	function ( $excerpt ) {
		return strip_shortcodes( $excerpt );
	},
	1
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