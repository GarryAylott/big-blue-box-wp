<?php
/**
 * Image Optimisation
 *
 * - Disables WordPress from auto-adding fetchpriority="high"
 * - Keeps lazy-loading and async decoding intact
 * - Explicitly sets fetchpriority="high" only on your chosen LCP image
 *
 * @package BigBlueBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Remove WordPress’s automatic fetchpriority across all images.
 *    (Still allows lazy-loading and decoding="async".)
 */
add_filter( 'wp_get_loading_optimization_attributes', function( $attributes, $context ) {
    if ( isset( $attributes['fetchpriority'] ) ) {
        unset( $attributes['fetchpriority'] );
    }
    return $attributes;
}, 10, 2 );

/**
 * 2. Explicitly add fetchpriority="high" only to the intended LCP:
 *    - Your manual hero image (class="hero-img")
 *    - Or fallback: the featured image if no hero exists
 */
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
    if ( is_singular() && in_the_loop() && is_main_query() ) {
        // Hero image detected
        if ( isset( $attr['class'] ) && str_contains( $attr['class'], 'hero-img' ) ) {
            $attr['fetchpriority'] = 'high';
            unset( $attr['loading'] );
        }
        // Fallback: featured image
        elseif ( $attachment->ID === get_post_thumbnail_id() ) {
            $attr['fetchpriority'] = 'high';
            unset( $attr['loading'] );
        }
    }
    return $attr;
}, 20, 3 );