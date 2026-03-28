<?php
/**
 * Add guidance text to the core Heading block in the editor sidebar.
 *
 * @package bigbluebox
 */

add_action( 'enqueue_block_editor_assets', function () {
	$script_path = get_template_directory() . '/inc/editor-heading-guidance.js';
	$script_ver  = file_exists( $script_path ) ? filemtime( $script_path ) : _S_VERSION;

	wp_enqueue_script(
		'bbb-heading-guidance',
		get_template_directory_uri() . '/inc/editor-heading-guidance.js',
		[ 'wp-hooks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-compose' ],
		$script_ver,
		true
	);
} );
