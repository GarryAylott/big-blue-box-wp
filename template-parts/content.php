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

	<section class="article-featured-image">
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

			<section class="article-closeout">
				<h4>Final Thoughts</h4>
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

		<aside class="article-sidebar flow-large">
			<section class="author-info flow-small">
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

			<section class="social-channels flow">
				<h5>Share</h5>
				<ul role="list">
					<li>
						<a href="https://bsky.app/compose?text=<?php echo urlencode( get_the_title() . ' ' . get_permalink() ); ?>" target="_blank" rel="noopener">
							<div class="social-channels__item">
								<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="X Bluesky link">
								<p>Bluesky</p>
							</div>
							<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
						</a>
					</li>
					<li>
						<a href="https://x.com/share?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank">
							<div class="social-channels__item">
								<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="X Twitter link">
								<p>X (Twitter)</p>
							</div>
							<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
						</a>
					</li>
					<li>
						<a href="https://instagram.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
							<div class="social-channels__item">
								<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-insta.svg" width="21" height="21" alt="Instagram link">
								<p>Instagram</p>
							</div>
							<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
						</a>
					</li>
					<li>
						<a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank">
							<div class="social-channels__item">
								<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="Facebook link">
								<p>Facebook</p>
							</div>
							<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
						</a>
					</li>
					<li>
						<a href="https://www.threads.net/intent/post?text=Check+out+this+article+from+The+Big+Blue+Box+&url=<?php echo urlencode( get_permalink() ); ?>" target="_blank">
							<div class="social-channels__item">
								<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="Threads link">
								<p>Threads</p>
							</div>
							<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
						</a>
					</li>
				</ul>
			</section>
    	</aside>
	</div>
</article>