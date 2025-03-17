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
    <div class="wrapper">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content', get_post_type() );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'bigbluebox' ) . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'bigbluebox' ) . '</span> <span class="nav-title">%title</span>',
			)
		);

	endwhile; // End of the loop.
	?>
	</div>
</main><!-- #main -->

<?php
get_footer();
