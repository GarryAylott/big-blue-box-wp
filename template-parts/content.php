<?php
/**
 * @package Big_Blue_Box
 */

?>
<article class="single-post-article region-small flow" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header flow-small">

		<?php get_template_part( 'template-parts/content', 'category-tag' );

        the_title( '<h1 class="entry-title">', '</h1>' );?>

		<div class="single-post-article__header-meta">
			<?php printf(
				'<div class="post-meta flex">%s <span>•</span> %s</div>',
					'<p> By ' . esc_html( get_the_author() ) . '</p>',
					'<p>' . esc_html( get_the_date() ) . '</p>'
				);
			?>
			<?php if ( has_tag() ) : ?>
				<div class="post-tags">
					<p class="small">Tags:</p>
					<?php the_tags( '<ul role="list"><li>', '</li><li>', '</li></ul>' ); ?>
				</div>
			<?php endif; ?>
		</div>
    </header>

	<?php
	if (has_post_thumbnail()) :
		?>
			<img class="singlepost-feat rounded" src="<?php echo the_post_thumbnail_url(); ?>" width="595" height="335" alt="<?php echo the_title() ?>">
		<?php
	endif;
	?>

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

			<?php
			// Fetch the ACF fields (ensure you have ACF installed/active).
			$closing_text  = get_field( 'closing_thoughts_text' );
			$closing_score = get_field( 'closing_thoughts_score' );

			// Only display if fields are not empty.
			if ( $closing_text || $closing_score ) :
				// Convert score to a float (handles half-points).
				$score = (float) $closing_score;
				
				// Calculate circle stroke offset based on the score (out of 10).
				// For an SVG circle of radius 50, circumference ~ 314.		
				$radius = 50;
				$circumference = 2 * M_PI * $radius;
				// E.g. if score=5, offset= half the circumference → 314 - (5/10)*314 = 157.
				$offset = $circumference - ( ( $score / 10 ) * $circumference );
				?>
				
				<div class="closing-thoughts rounded">
					<div class="closing-thoughts__text">
						<h3 class="closing-thoughts-title"><?php esc_html_e( 'Closing Thoughts', 'bigbluebox' ); ?></h3>
						
						<?php if ( $closing_text ) : ?>
							<p><?php echo esc_html( $closing_text ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( $closing_score ) : ?>
						<div class="score-wrapper" aria-label="<?php printf( esc_attr__( 'Score: %s out of 10', 'bigbluebox' ), esc_attr( $score ) ); ?>">
							<!-- SVG Container -->
							<svg 
								class="score-circle" 
								width="120" 
								height="120" 
								viewBox="0 0 120 120" 
								role="img"
							>
								<!-- Background circle (full grey ring) -->
								<circle
									class="score-circle-bg"
									cx="60"
									cy="60"
									r="50"
								/>
								<!-- Stroke circle showing portion of the circumference -->
								<circle
									class="score-circle-stroke"
									cx="60"
									cy="60"
									r="50"
									style="stroke-dasharray: <?php echo esc_attr( $circumference ); ?>; stroke-dashoffset: <?php echo esc_attr( $offset ); ?>;"
								/>
							</svg>
							<div class="score-value"><?php echo esc_html( $score ); ?></div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<aside class="article-sidebar flow-large">
			<section class="author-info flow-tiny">
			<?php
				$author_id = get_the_author_meta( 'ID' );
				$short_bio = get_field( 'short_bio', 'user_' . $author_id );
				
				// Output the author's avatar.
				echo get_avatar( $author_id, 72, '', get_the_author() );
				
				// Output the full author name.
				echo '<p class="author-info__name">By ' . esc_html( get_the_author() ) . '</p>';

				// Output the short bio if available; otherwise, fall back to the default description.
				if ( $short_bio ) {
					echo '<p class="small">' . wp_kses_post( $short_bio ) . '</p>';
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