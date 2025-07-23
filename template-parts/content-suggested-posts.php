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
            if (has_category('Podcasts', get_the_ID())) {
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
            $current_post_id = get_the_ID();
            $current_post_categories = wp_get_post_categories($current_post_id);
            $current_post_tags = wp_get_post_tags($current_post_id);

            if (has_category('Podcasts', $current_post_id)) {
                $current_post_date = get_the_date('Y-m-d H:i:s', $current_post_id);

                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 10,
                    'post__not_in'   => array($current_post_id),
                    'category_name'  => 'Podcasts',
                    'orderby'        => 'date',
                    'order'          => 'DESC', // Fetch previous posts in reverse order
                    'date_query'     => array(
                        array(
                            'before'    => $current_post_date,
                            'inclusive' => false,
                        ),
                    ),
                );
            } else {
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 10,
                    'post__not_in'   => array($current_post_id),
                    'orderby'        => 'rand',
                    'category__in'   => $current_post_categories,
                    'tag__in'        => wp_list_pluck($current_post_tags, 'term_id'),
                    'ignore_sticky_posts' => 1,
                );
            }
            $query = new WP_Query($args);
        ?>
        <?php 
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php get_template_part('template-parts/content', 'post-cards', array('card_type' => 'browse')); ?>
        <?php
        endwhile;
            wp_reset_postdata();
        endif; 
        ?>
    </div>
</div>