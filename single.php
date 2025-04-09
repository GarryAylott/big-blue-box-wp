<?php
/**
 * @package Big_Blue_Box
 */

get_header();
?>

<div class="page-bg-inline bg-image-fade">
	<img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg_single-post.webp" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part('template-parts/content', get_post_type());
        get_template_part('template-parts/content', 'suggested-posts');
		get_template_part('template-parts/content', 'read-progress');

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
