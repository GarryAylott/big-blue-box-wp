<?php
/**
 * Template part for displaying a post card in AJAX loaded content.
 *
 * @package Big_Blue_Box
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card-alt'); ?>>
    <a href="<?php the_permalink(); ?>">
        <div class="article-top">
            <?php if (has_post_thumbnail()) : ?>
                <img class="post-thumb-img img-hover rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>" width="391" height="220" alt="<?php echo the_title() ?>">
            <?php endif; ?>

            <header class="entry-header">
                <?php get_template_part( 'template-parts/content', 'category-tag' ); ?>
                <h5 class="balance">
                    <?php
                    $thetitle = $post->post_title;
                    $getlength = strlen($thetitle);
                    $thelength = 55;
                    echo substr($thetitle, 0, $thelength);
                    if ($getlength > $thelength) echo "...";
                    ?>
                </h5>
            </header>
        </div>
    </a>
    <footer class="entry-footer">
        <?php get_template_part( 'template-parts/content', 'author-meta' ); ?>
    </footer>
</article>