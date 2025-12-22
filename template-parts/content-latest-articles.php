<?php
/**
 * Template part for displaying the latest three non-podcast posts.
 *
 * @package Big_Blue_Box
 */
?>

<div class="latest-articles-featured">
    <?php
        $displayed_posts = get_query_var('displayed_posts', array());

        $podcasts_cat = get_category_by_slug( 'podcasts' );
        $podcasts_cat_id = $podcasts_cat ? (int) $podcasts_cat->term_id : 0;

        $args2 = array(
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'post__not_in' => $displayed_posts
        );

        if ( $podcasts_cat_id ) {
            $args2['category__not_in'] = array( $podcasts_cat_id );
        }
        $query2 = new WP_Query($args2);
    ?>
    <?php if ( $query2->have_posts() ) :
        while ( $query2->have_posts() ) : $query2->the_post(); ?>
        <?php get_template_part('template-parts/content', 'post-cards', array('card_type' => 'latest', 'link_author_name' => true)); ?>
    <?php
    $displayed_posts[] = get_the_ID();
    endwhile;
        wp_reset_postdata();
    endif; 
    set_query_var('displayed_posts', $displayed_posts);
    ?>
</div>
