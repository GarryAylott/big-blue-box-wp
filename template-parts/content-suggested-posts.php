<?php
/**
 * Template part for displaying suggested articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="suggested-posts">
    <div class="suggested-posts-header">
        <h4>
            <?php
            $header_type = isset($args['header_type']) ? $args['header_type'] : '';
            if ($header_type === 'latest') {
                echo 'Latest articles & podcast episodes';
            } elseif (has_category('Podcasts', get_the_ID())) {
                echo 'Continue listening';
            } else {
                echo 'Continue reading';
            }
            ?>
        </h4>
        <div class="scroll-nav">
            <button class="scroll-nav-btn scroll-left" aria-label="Scroll left">
                <svg width="26" height="22" viewBox="0 0 29 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.0625 11.125L11.0625 1.125C11.8125 0.3125 13.125 0.3125 13.875 1.125C14.6875 1.875 14.6875 3.1875 13.875 3.9375L7.3125 10.5H26.5C27.5625 10.5 28.5 11.4375 28.5 12.5C28.5 13.625 27.5625 14.5 26.5 14.5H7.3125L13.875 21.125C14.6875 21.875 14.6875 23.1875 13.875 23.9375C13.125 24.75 11.8125 24.75 11.0625 23.9375L1.0625 13.9375C0.25 13.1875 0.25 11.875 1.0625 11.125Z" fill=""/>
                </svg>
            </button>
            <button class="scroll-nav-btn scroll-right" aria-label="Scroll right">
                <svg width="26" height="22" viewBox="0 0 29 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M27.875 13.9375L17.875 23.9375C17.125 24.75 15.8125 24.75 15.0625 23.9375C14.25 23.1875 14.25 21.875 15.0625 21.125L21.625 14.5H2.5C1.375 14.5 0.5 13.625 0.5 12.5C0.5 11.4375 1.375 10.5 2.5 10.5H21.625L15.0625 3.9375C14.25 3.1875 14.25 1.875 15.0625 1.125C15.8125 0.3125 17.125 0.3125 17.875 1.125L27.875 11.125C28.6875 11.875 28.6875 13.1875 27.875 13.9375Z" fill=""/>
                </svg>
            </button>
        </div>
    </div>
    <div class="posts-hori-scroll">
        <?php
            // Keep the special "latest" carousel for search results exactly as-is
            $is_latest_header = ( is_search() && isset($args['header_type']) && $args['header_type'] === 'latest' );

            if ( $is_latest_header ) {
                $latest_args = array(
                    'post_type'           => 'post',
                    'posts_per_page'      => 10,
                    'category_name'       => 'Podcasts,Articles',
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                    'ignore_sticky_posts' => 1,
                );
                $latest_query = new WP_Query( $latest_args );
                if ( $latest_query->have_posts() ) :
                    while ( $latest_query->have_posts() ) : $latest_query->the_post(); ?>
                        <?php get_template_part( 'template-parts/content', 'post-cards', array( 'card_type' => 'browse' ) ); ?>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
            } else {
                // 🔁 Reuse the single source of truth for related content
                // Requires: inc/related-articles.php (bbb_get_related_articles)
                $context = has_category( 'Podcasts', get_the_ID() ) ? 'podcasts' : 'articles';
                $related_posts = function_exists('bbb_get_related_articles') ? bbb_get_related_articles( 10, $context ) : array();

                if ( ! empty( $related_posts ) ) :
                    foreach ( $related_posts as $post ) :
                        setup_postdata( $post ); ?>
                        <?php get_template_part( 'template-parts/content', 'post-cards', array( 'card_type' => 'browse' ) ); ?>
                    <?php endforeach;
                    wp_reset_postdata();
                endif;
            }
        ?>
    </div>
</div>