<?php
/**
 * Theme Image Sizes
 *
 * Defines and cleans up WordPress image sizes for this theme.
 *
 * @package BigBlueBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom image sizes.
 */
add_action( 'after_setup_theme', function() {

    // Featured image (consistent landscape crop)
    add_image_size( 'singlepost-feat', 1400, 600, true );

    // Inline full-width images (matches content width, no crop)
    add_image_size( 'singlepost-wide', 1080, 0, false );

    // Square crop for inline or galleries
    add_image_size( 'singlepost-square', 1080, 1080, true );

    // Thumbnails for post lists/cards
    add_image_size( 'post-list-thumb', 400, 225, true );

    // Optional retina step above content width
    add_image_size( 'retina-large', 1536, 0, false );

    // Set max content width for responsive images
    if ( ! isset( $content_width ) ) {
        $content_width = 1080;
    }
});

/**
 * Remove unwanted default sizes to reduce bloat.
 */
add_filter( 'intermediate_image_sizes_advanced', function( $sizes ) {
    // Keep: thumbnail (150x150) + medium (~300px)
    unset( $sizes['medium_large'] );
    unset( $sizes['large'] );
    unset( $sizes['2048x2048'] );
    return $sizes;
});

/**
 * Make custom sizes selectable in the editor.
 */
add_filter( 'image_size_names_choose', function( $sizes ) {
    return array_merge( $sizes, [
        'singlepost-wide'   => __( 'Post Full Width' ),
        'singlepost-square' => __( 'Post Square' ),
        'retina-large'      => __( 'Retina Large' ),
    ] );
});

/**
 * Custom responsive sizes for inline content images.
 * Ensures browsers don’t download oversized files on small screens.
 */
add_filter( 'wp_calculate_image_sizes', function( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
    $width = $size[0] ?? 0;

    // Only apply inside main post content
    if ( is_singular() && in_the_loop() && is_main_query() ) {
        if ( $width >= 1080 ) {
            $sizes = '(max-width: 1080px) 100vw, 1080px';
        } else {
            $sizes = '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
        }
    }

    return $sizes;
}, 10, 5 );