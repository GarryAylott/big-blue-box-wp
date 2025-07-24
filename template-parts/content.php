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
				<?php if ( has_category( 'podcasts' ) ) : ?>
					<?php
					$ep_label     = get_field( 'podcast_episode_number' );
					$episode_type = get_field( 'podcast_episode_type' ); // e.g., 'bonus', 'trailer'
					$is_bonus = is_string( $episode_type ) && strtolower( $episode_type ) === 'bonus';

					// Build the label with fallback
					if ( is_numeric( $ep_label ) ) {
						$episode_text = 'Episode ' . esc_html( $ep_label );
					} elseif ( ! empty( $ep_label ) ) {
						$episode_text = esc_html( $ep_label );
					} elseif ( $is_bonus ) {
						$episode_text = 'Bonus Episode';
					} else {
						$episode_text = null;
					}

					if ( $episode_text ) :
						?>
						<p class="small"><?php echo $episode_text; ?></p>
						<span class="separator">|</span>
					<?php endif; ?>

					<p class="small"><?php echo esc_html( get_the_date() ); ?></p>

				<?php else : ?>
					<p class="small">By <?php echo esc_html( get_the_author() ); ?></p>
					<span class="separator">|</span>
					<p class="small"><?php echo esc_html( get_the_date() ); ?></p>
					<span class="separator">|</span>
					<p class="reading-time small">
						<?php echo bbb_get_icon( 'icon-clock' ); ?>
						<?php echo esc_html( bbb_estimated_reading_time() ); ?>
					</p>
				<?php endif; ?>
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

			<!-- Podcast player -->
			<?php
				if ( in_category( 'podcasts' ) ) :
					$guid = get_field( 'captivate_episode_guid' );
					error_log( '🎯 GUID from ACF: ' . print_r( $guid, true ) );

					$audio_url = bbb_get_captivate_audio_url( $guid );

					// Fallback for local dev
					if ( ! $audio_url && wp_get_environment_type() === 'local' ) {
						$audio_url = 'https://traffic.libsyn.com/secure/examplepodcast/example-episode.mp3';
						error_log( '🔊 Using fallback audio URL for local dev.' );
					}

					echo '<!-- GUID: ' . esc_html( $guid ) . ' -->';
					echo '<!-- Audio URL: ' . esc_url( $audio_url ) . ' -->';

					if ( $audio_url ) :
						?>
						<div class="podcast-player flow">
							<audio id="player" class="vlite-js" preload="none">
								<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg" />
								Your browser does not support the audio element.
							</audio>
						</div>
						<?php
					endif;
				endif;
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