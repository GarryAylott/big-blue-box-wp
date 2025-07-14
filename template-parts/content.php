<?php
/**
 * @package Big_Blue_Box
 */

?>
<article class="post-article region-small flow" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="post-hero entry-header">
		<?php
		if (has_post_thumbnail()) :
			?>
				<img class="post-thumb-img rounded" src="<?php echo the_post_thumbnail_url(); ?>" width="595" height="335" alt="<?php echo the_title() ?>">
			<?php
		endif;
		?>
		<div class="post-hero__details">
			<div class="post-article-title">
				<?php get_template_part( 'template-parts/content', 'category-tag' ); ?>
				<?php the_title( '<h1 class="entry-title">', '</h1>' );?>
			</div>

			<div class="post-article-meta flex">
				<?php
					echo '<p class="small">By ' . esc_html( get_the_author() ) . '</p>';
					echo '<span class="separator">|</span>';
					echo '<p class="small">' . esc_html( get_the_date() ) . '</p>';
					if ( ! has_category( 'podcasts' ) ) {
						echo '<span class="separator">|</span>';
						$reading_time = sprintf(
							'<p class="reading-time small">%s%s</p>',
							bbb_get_icon( 'icon-clock' ),
							esc_html( bbb_estimated_reading_time() )
						);
						echo $reading_time;
					}
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="post-tags">
					<?php the_tags( '<ul role="list"><li>', '</li><li>', '</li></ul>' ); ?>
				</div>
			<?php endif; ?>
		</div>
    </div>

	<div class="post-article__container">
		<?php get_template_part('template-parts/content', 'read-progress'); ?>
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

			<?php get_template_part('template-parts/content', 'review-score'); ?> 
			
		</div>

		<aside class="article-sidebar flow-large">
			<section class="author-info flow-small">
			<?php
				$author_id = get_the_author_meta( 'ID' );
				$bio_short = get_field( 'bio_short', 'user_' . $author_id );
				
				// Output the author's avatar.
				echo get_avatar( $author_id, 72, '', get_the_author() );

				// Output the custom bio_short if available; otherwise, fall back to the default description.
				if ( $bio_short ) {
					echo '<p class="small">' . wp_kses_post( $bio_short ) . '</p>';
				} else {
					echo '<p class="small">' . esc_html( get_the_author_meta( 'description' ) ) . '</p>';
				}

				// Retrieve the author's first name.
				$first_name = get_the_author_meta( 'first_name', $author_id );
				// Set the link text, falling back to a generic label if the first name is not set.
				$link_text = ! empty( $first_name )
					? sprintf( esc_html__( 'More about %s', 'your-text-domain' ), esc_html( $first_name ) )
					: esc_html__( 'More about the author', 'your-text-domain' );

				// Output the link to the author's archive.
				echo '<a class="link-alt" href="' . esc_url( get_author_posts_url( $author_id ) ) . '">';
					echo '<p class="small">' . $link_text . '</p>';
				echo '</a>';
			?>
			</section>

			<?php get_template_part('template-parts/content', 'social-share-links'); ?> 
    	</aside>
	</div>
</article>