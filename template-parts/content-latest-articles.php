<?php
/**
 * Template part for displaying the latest three articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="wrapper">
    <h5 class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
            <path d="M5.25 2.5a.74.74 0 0 0-.75.75v9.5c0 .281-.063.531-.156.75h9.406a.74.74 0 0 0 .75-.75v-9.5a.76.76 0 0 0-.75-.75h-8.5Zm-3 12.5C1 15 0 14 0 12.75V3.5a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75v9.25c0 .438.313.75.75.75a.74.74 0 0 0 .75-.75v-9.5C3 2.031 4 1 5.25 1h8.5C14.969 1 16 2.031 16 3.25v9.5A2.26 2.26 0 0 1 13.75 15H2.25ZM5.5 4.25a.74.74 0 0 1 .75-.75h3a.76.76 0 0 1 .75.75v2.5a.74.74 0 0 1-.75.75h-3a.722.722 0 0 1-.75-.75v-2.5Zm6.25-.75h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm-5.5 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Z"/>
        </svg>
        Latest articles
    </h5>
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
</div>