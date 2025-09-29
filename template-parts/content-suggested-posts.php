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
            if ( 'latest' === $header_type ) {
                esc_html_e( 'Latest articles & podcast episodes', 'bigbluebox' );
            } elseif ( has_category( 'podcasts', get_the_ID() ) ) {
                esc_html_e( 'Continue listening', 'bigbluebox' );
            } else {
                esc_html_e( 'Continue reading', 'bigbluebox' );
            }
            ?>
        </h4>
        <div class="scroll-nav">
            <button class="scroll-nav-btn scroll-left" aria-label="Scroll left">
                <i data-lucide="arrow-left" class="icon-step-2"></i>
            </button>
            <button class="scroll-nav-btn scroll-right" aria-label="Scroll right">
                <i data-lucide="arrow-right" class="icon-step-2"></i>
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
                    'category_name'       => 'podcasts,articles',
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
                $context = has_category( 'podcasts', get_the_ID() ) ? 'podcasts' : 'articles';
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
