<?php
/**
 * Enqueuing all scripts and styles.
 *
 * @package Big_Blue_Box
 */

// Update your enqueue script (functions.php) to remove the vLite CDN
function bigbluebox_scripts() {
	// File-based versions for cache busting
	$style_path  = get_stylesheet_directory() . '/style.css';
	$script_path = get_template_directory() . '/scripts/bbb-scripts.min.js';

	$style_ver  = file_exists($style_path) ? filemtime($style_path) : _S_VERSION;
	$script_ver = file_exists($script_path) ? filemtime($script_path) : _S_VERSION;

	// Main stylesheet
	wp_enqueue_style('bigbluebox-style', get_stylesheet_uri(), array(), $style_ver);

	// Main JS bundle — ESM
	wp_enqueue_script('bigbluebox-scripts', get_template_directory_uri() . '/scripts/bbb-scripts.min.js', array(), $script_ver, true);
	wp_script_add_data( 'bigbluebox-scripts', 'type', 'module' );

	wp_localize_script('bigbluebox-scripts', 'themeSettings', array(
		'themeUrl'     => get_template_directory_uri(),
		'ajaxUrl'      => admin_url('admin-ajax.php'),
		'filterNonce'  => wp_create_nonce( 'bbb_filter_posts' ),
	));

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'bigbluebox_scripts');

// Prevent WP from loading mediaelement.js
function bigbluebox_disable_mediaelement() {
	wp_deregister_script('wp-mediaelement');
	wp_deregister_style('wp-mediaelement');
	wp_deregister_script('mediaelement');
	wp_deregister_style('mediaelement');
}
add_action('wp_enqueue_scripts', 'bigbluebox_disable_mediaelement', 100);
