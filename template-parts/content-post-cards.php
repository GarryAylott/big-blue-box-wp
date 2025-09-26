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
                    <img
                        class="post-thumb-img img-hover"
                        src="<?php echo esc_url( get_the_post_thumbnail_url( null, 'homepage-thumb' ) ); ?>"
                        width="391"
                        height="220"
                        alt="<?php echo esc_attr( get_the_title() ); ?>"
                    >
                </div>
            <?php endif; ?>

            <header class="entry-header">
                <?php get_template_part( 'template-parts/content', 'category-tag' ); ?>
                <h5 class="balance">
                    <?php
                    $title_excerpt = wp_html_excerpt( get_the_title(), 55, '…' );
                    echo esc_html( $title_excerpt );
                    ?>
                </h5>
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
                <img
                    class="post-thumb-img img-hover rounded-small"
                    src="<?php echo esc_url( get_the_post_thumbnail_url( null, 'homepage-thumb' ) ); ?>"
                    width="387"
                    height="217"
                    alt="<?php echo esc_attr( get_the_title() ); ?>"
                >
            </div>
        <?php endif; ?>

        <header class="entry-header">
            <h5 class="balance">
                <?php
                $title_excerpt = wp_html_excerpt( get_the_title(), 80, '…' );
                echo esc_html( $title_excerpt );
                ?>
            </h5>
        </header>
    </a>
    <div class="post-card-content">
        <div class="entry-content">
            <p class="small">
                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
            </p>
        </div>

        <footer class="entry-footer">
            <?php get_template_part( 'template-parts/content', 'author-meta', array( 'link_author_name' => true ) ); ?>
        </footer>
    </div>
</article>
<?php endif; ?>
