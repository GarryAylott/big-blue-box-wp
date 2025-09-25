<?php
/**
 * Big Blue Box – Custom Block Loader
 *
 * Auto-registers all blocks in /inc/blocks/{block-name}/block.json
 *
 * @package BigBlueBoxTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all blocks in /inc/blocks
 */
function bbb_register_blocks() {
    $blocks_dir = get_template_directory() . '/inc/blocks';

    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    // Scan for block.json files inside subfolders
    $directories = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

    foreach ( $directories as $dir ) {
        if ( file_exists( $dir . '/block.json' ) ) {
            register_block_type( $dir );
        }
    }
}
add_action( 'init', 'bbb_register_blocks' );