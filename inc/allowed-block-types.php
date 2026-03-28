<?php
/**
 * Restrict available block types in the editor.
 *
 * @package bigbluebox
 */

add_filter( 'allowed_block_types_all', function ( $allowed_block_types, $block_editor_context ) {
	return [
		// Core blocks.
		'core/paragraph',
		'core/heading',
		'core/image',
		'core/gallery',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/pullquote',
		'core/separator',
		'core/spacer',
		'core/embed',
		'core/html',
		'core/shortcode',
		'core/video',
		'core/audio',
		'core/table',

		// Custom blocks.
		'bbb/info-block',
		'bbb/thoughts-from-team',
		'bbb/tardis-separator',
	];
}, 10, 2 );
