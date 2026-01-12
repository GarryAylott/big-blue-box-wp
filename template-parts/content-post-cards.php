<?php
/**
 * Template part for displaying post card layouts
 *
 * @package Big_Blue_Box
 */

// Default to 'browse' if not set
$card_type = isset($args['card_type']) ? $args['card_type'] : 'browse';
?>

<?php if ( 'browse' === $card_type ) : ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card-alt' ); ?>>
    <a href="<?php the_permalink(); ?>">
        <div class="article-top">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="img-container">
                    <?php
                    echo wp_get_attachment_image(
                        get_post_thumbnail_id(),
                        'post-featured-card',
                        false,
                        [
                            'class'   => 'post-thumb-img img-hover',
                            'sizes'   => '(min-width: 1400px) 33vw, (min-width: 900px) 50vw, 100vw',
                            'loading' => 'lazy',
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>

            <header class="entry-header">
                <?php get_template_part( 'template-parts/content', 'category-tag' ); ?>
                <h4 class="balance">
                    <?php
                    $title_excerpt = wp_html_excerpt( get_the_title(), 55, '…' );
                    echo esc_html( $title_excerpt );
                    ?>
                </h4>
            </header>
        </div>
    </a>
    <footer class="entry-footer">
        <?php get_template_part( 'template-parts/content', 'author-meta', array( 'link_author_name' => false ) ); ?>
    </footer>
</article>
<?php elseif ($card_type === 'latest') : ?>
<!-- Latest Articles Card Layout -->
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card-author'); ?>>
    <a href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="img-container">
                    <?php
                    echo wp_get_attachment_image(
                        get_post_thumbnail_id(),
                        'post-featured-card',
                        false,
                        [
                            'class'   => 'post-thumb-img img-hover',
                            'sizes'   => '(min-width: 1400px) 33vw, (min-width: 900px) 50vw, 100vw',
                            'loading' => 'lazy',
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>

        <header class="entry-header">
            <!-- <?php get_template_part( 'template-parts/content', 'category-tag' ); ?> -->
            <h4 class="balance">
                <?php
                $title_excerpt = wp_html_excerpt( get_the_title(), 80, '…' );
                echo esc_html( $title_excerpt );
                ?>
            </h4>
        </header>
    </a>
    <div class="post-card-content">
        <footer class="entry-footer">
            <?php get_template_part( 'template-parts/content', 'author-meta', array( 'link_author_name' => true ) ); ?>
        </footer>
    </div>
</article>
<?php endif; ?>
