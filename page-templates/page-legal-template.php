<?php
/**
 * Template Name: Legal Pages
 * Description: The template for displaying legal pages.
 *
 * @package BigBlueBox
 */

get_header();
?>

<div class="hero-bg-image">
    <picture>
        <source srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.avif" type="image/avif">
        <source srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.webp" type="image/webp">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.webp" decoding="async" alt="" fetchpriority="high">
    </picture>
</div>

<main id="primary" class="site-main">
    <div class="wrapper">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="flow" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content flow">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
