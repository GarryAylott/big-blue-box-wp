<?php
/**
 * Template part for displaying the latest three articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="latest-articles-featured">
    <?php
        $displayed_posts = get_query_var('displayed_posts', array());

        $args2 = array(
            'category_name' => 'articles',
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'post__not_in' => $displayed_posts
        );
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