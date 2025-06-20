<?php
/**
 * Enqueuing all scripts and styles.
 *
 * @package Big_Blue_Box
 */

 function bigbluebox_scripts() {
	wp_enqueue_style( 'bigbluebox-style', get_stylesheet_uri(), array(), _S_VERSION );

	wp_enqueue_script( 'bigbluebox-scripts', get_template_directory_uri() . '/scripts/bbb-scripts.min.js', array(), _S_VERSION, true );

	wp_localize_script( 'bigbluebox-scripts', 'themeSettings', array(
		'themeUrl' => get_template_directory_uri(),
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
	));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bigbluebox_scripts' );