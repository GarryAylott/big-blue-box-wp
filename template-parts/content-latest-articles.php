<?php
/**
 * Template part for displaying the latest three articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="wrapper">
    <h4 class="section-title">Latest articles</h4>
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
                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img class="post-thumb-img rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>">
                        </a>
                    <?php endif; ?>

                    <?php get_template_part( 'template-parts/content', 'category-tag' ); ?>

                    <header class="entry-header">
                        <a href="<?php the_permalink(); ?>">
                            <h5>
                                <?php
                                    $thetitle = $post->post_title;
                                    $getlength = strlen($thetitle);
                                    $thelength = 80;
                                    echo substr($thetitle, 0, $thelength);
                                    if ($getlength > $thelength) echo "...";
                                ?>
                            </h5>
                        </a>
                    </header>

                    <footer class="entry-footer">
                        <?php get_template_part( 'template-parts/content', 'author-meta' ); ?>
                    </footer>

                </article>
        <?php
        $displayed_posts[] = get_the_ID();
        endwhile;
            wp_reset_postdata();
        endif; 
        set_query_var('displayed_posts', $displayed_posts);
        ?>
    </div>
</div>