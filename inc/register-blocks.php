<?php
/**
 * Register custom Gutenberg blocks for Big Blue Box.
 */

function bbb_register_custom_blocks() {
    $blocks_root      = get_template_directory() . '/inc/blocks/';
    $block_json_files = glob( $blocks_root . '*/block.json' );

    if ( ! empty( $block_json_files ) ) {
        foreach ( $block_json_files as $block_json ) {
            register_block_type( dirname( $block_json ) );
        }
    }
}
add_action( 'init', 'bbb_register_custom_blocks' );

function bbb_register_block_category( $categories ) {
    $bbb_category = array(
        'slug'  => 'bbb-blocks',
        'title' => __( 'Big Blue Box Custom Blocks', 'bigbluebox' ),
        'icon'  => null,
    );

    array_unshift( $categories, $bbb_category );

    return $categories;
}
add_filter( 'block_categories_all', 'bbb_register_block_category' );
