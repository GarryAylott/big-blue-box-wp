<?php
/**
 * @package Big_Blue_Box
 */

get_header();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'   => get_template_directory_uri() . '/images/pagebg_single-post.jpg',
    'sources' => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_single-post.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_single-post.webp',
            'type'   => 'image/webp'
        ]
    ]
]); ?>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part('template-parts/content', get_post_type());
        get_template_part('template-parts/content', 'suggested-posts');

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile; // End of the loop.
	?>
	</div>
</main><!-- #main -->

<?php
get_footer();
