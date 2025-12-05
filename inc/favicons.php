<?php
/**
 * Custom favicon and app-icon handling for Big Blue Box Theme.
 *
 * This file disables WordPress's default Site Icon markup
 * and injects the theme's own favicon set via wp_head.
 */

// -----------------------------------------------------------------------------
// Disable WordPress' default favicon / site icon output.
// -----------------------------------------------------------------------------

add_filter( 'get_site_icon_url', '__return_false' );

// WP core prints site icons on wp_head at priority 99.
// Remove that so we can supply our own clean set.
remove_action( 'wp_head', 'wp_site_icon', 99 );


// -----------------------------------------------------------------------------
// Output custom favicon and PWA-related markup.
// -----------------------------------------------------------------------------

add_action( 'wp_head', function () {

    // Base directory for favicon assets inside your theme.
    $base = get_template_directory_uri() . '/favicons/';

    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $base . 'favicon-96x96.png' ); ?>" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $base . 'favicon.svg' ); ?>">
    <link rel="shortcut icon" href="<?php echo esc_url( $base . 'favicon.ico' ); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $base . 'apple-touch-icon.png' ); ?>">
    <meta name="apple-mobile-web-app-title" content="Big Blue Box">
    <link rel="manifest" href="<?php echo esc_url( $base . 'site.webmanifest' ); ?>">
    <?php
} );
