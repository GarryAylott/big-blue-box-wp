<?php
/**
 * Template part for displaying the latest three articles.
 *
 * @package Big_Blue_Box
 */
?>

<?php
    $args = array(
        'category_name' => 'articles',
        'posts_per_page' => 3,
        'post_status' => 'publish'
    );
    $latest_articles = new WP_Query( $args );
?>

<div class="wrapper latest-articles">
    <h4 class="section-title">Latest articles</h4>
    <div class="latest-articles-featured">
        <?php if ( $latest_articles->have_posts() ) :
            while ( $latest_articles->have_posts() ) : $latest_articles->the_post(); ?>
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

                </article><!-- #post-<?php the_ID(); ?> -->
            <?php endwhile;

            wp_reset_postdata();
        else : ?>
            <p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>
    </div>
</div>
