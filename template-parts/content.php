<?php
/**
 * @package Big_Blue_Box
 */

?>

<article class="single-post-article region-normal flow" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">

		<?php get_template_part( 'template-parts/content', 'category-tag' );

        the_title( '<h1 class="entry-title">', '</h1>' );?>

		<div class="single-post-article__header-meta">
			<?php printf(
				'<div class="post-meta small flex">%s <span>•</span> %s</div>',
					'<p> By ' . esc_html( get_the_author() ) . '</p>',
					'<p>' . esc_html( get_the_date() ) . '</p>'
				);
			?>
			<?php if ( has_tag() ) : ?>
				<section class="post-tags">
					<p class="small">Tags:</p>
					<?php the_tags( '<ul role="list"><li>', '</li><li>', '</li></ul>' ); ?>
				</section>
			<?php endif; ?>
		</div>
    </header>

	<section class="post-featured-image">
		<?php
		if (has_post_thumbnail()) :
			?>
				<img class="single-post_feat-img rounded" src="<?php echo the_post_thumbnail_url('singlepost-feat'); ?>" width="595" height="335" alt="<?php echo the_title() ?>">
			<?php
		endif;
		?>
	</section>

	<div class="single-post-article__container">
		<div class="article-body flow-large">
			<?php
			if ( function_exists( 'get_field' ) ) {
				$summary_point_one = get_field( 'summary_point_1' );
				$summary_point_two = get_field( 'summary_point_2' );
				$summary_point_three = get_field( 'summary_point_3' );

				if ( $summary_point_one || $summary_point_two || $summary_point_three ) {
					?>

					<section class="post-summary call-out-panel-large rounded">
					<h4>Summary</h4>
					<?php
					$summary_points = array(
						$summary_point_one,
						$summary_point_two,
						$summary_point_three
					);

					echo '<ul class="article-summary-list" role="list">';
					foreach ($summary_points as $point) {
						if ($point) {
							echo '<li>' . esc_html($point) . '</li>';
						}
					}
					echo '</ul>';
					?>
					</section>
					<?php
				}
			}
			?>

			<section class="post-content flow">
				<?php the_content(); ?>
			</section>

			<section class="post-closing">
				<h2>Final Thoughts</h2>
				<?php
				// Display the final summary and review score
				if ( function_exists( 'get_field' ) ) {
					$final_summary = get_field( 'final_summary' ); // ACF field
					$review_score = get_field( 'review_score' ); // ACF field

					if ( $final_summary ) {
						echo '<p>' . esc_html( $final_summary ) . '</p>';
					}
					if ( $review_score ) {
						echo '<p>Review Score: ' . esc_html( $review_score ) . '/10</p>';
					}
				}
				?>
			</section>
		</div>

		<aside class="post-sidebar flow-large">
			<section class="author-info flow-small">
				<?php
				$author_id = get_the_author_meta( 'ID' );
				echo get_avatar( $author_id, 72, '', get_the_author() );
				echo '<p class="author-info__name">By ' . esc_html( get_the_author() ) . '</p>';
				echo '<p class="small">' . esc_html( get_the_author_meta( 'description' ) ) . '</p>';
				echo '<a class="link-alt" href="' . esc_url( get_author_posts_url( $author_id ) ) . '"><p class="small">More about ' . esc_html( get_the_author() ) . '</p></a>';
				?>
			</section>

			<section class="share-post">
				<h5>Share</h5>
				<ul>
					<li><a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank">Facebook</a></li>
					<li><a href="https://x.com/share?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank">Twitter</a></li>
					<li><a href="https://linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>" target="_blank">LinkedIn</a></li>
				</ul>
			</section>
    	</aside>
	</div>
</article>
