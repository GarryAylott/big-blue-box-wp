<?php
/**
 * Template part for displaying the latest three articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="wrapper">
    <h5 class="section-title">
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
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img class="post-thumb-img img-hover rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>" width="387" height="217" alt="<?php echo the_title() ?>">
                        </a>
                    <?php endif; ?>

                    <div class="post-card-content">
                        <header class="entry-header">
                            <a href="<?php the_permalink(); ?>">
                                <h5 class="balance">
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

                        <div class="entry-content">
                            <p class="small">
                                <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                            </p>
                        </div>

                        <footer class="entry-footer">
                            <?php get_template_part( 'template-parts/content', 'author-meta' ); ?>
                        </footer>
                    </div>
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