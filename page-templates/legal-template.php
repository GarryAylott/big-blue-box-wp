<?php
/**
 * Template Name: Legal Pages
 * Description: The template for displaying legal pages.
 *
 * @package BigBlueBox
 */

get_header();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'   => get_template_directory_uri() . '/images/pagebg_legal.webp',
    'sources' => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.webp',
            'type'   => 'image/webp'
        ]
    ]
]); ?>

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
