<?php
/**
 * @package Big_Blue_Box
 */

?>
<article class="post-content region-small flow" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-hero entry-header">
		<?php
		if ( has_post_thumbnail() ) :
			echo wp_get_attachment_image(
				get_post_thumbnail_id(),
				'post-featured-large',
				false,
				[
					'class' => 'post-thumb-img rounded',
					'sizes' => '(min-width: 1400px) 1600px, 100vw',
				]
			);
		endif;
		?>
		<div class="post-hero__details">
			<div class="post-content-title">
				<?php the_title( '<h1 class="entry-title">', '</h1>' );?>
			</div>

			<div class="post-meta">
				<div class="post-content-meta">
					<p class="post-meta-title"><?php esc_html_e( 'Publish Info', 'bigbluebox' ); ?></p>
					<div class="post-meta-content">
						<?php if ( has_category( 'podcasts' ) ) : ?>
							<?php
							$ep_label     = get_field( 'podcast_episode_number' );
							$episode_type = get_field( 'podcast_episode_type' );

							// Build the label with fallback
							if ( is_numeric( $ep_label ) ) {
								$episode_text = sprintf( esc_html__( 'Episode %s', 'bigbluebox' ), esc_html( $ep_label ) );
							} elseif ( $ep_label === 'N/A' && ! empty( $episode_type ) ) {
								$episode_text = esc_html( $episode_type );
							} elseif ( ! empty( $ep_label ) ) {
								$episode_text = esc_html( $ep_label );
							} else {
								$episode_text = null;
							}

							if ( $episode_text ) :
								?>
								<p><?php echo esc_html( $episode_text ); ?>,</p>
							<?php endif; ?>

							<p><?php echo esc_html( get_the_date() ); ?></p>

					<?php else : ?>
						<p>
							<?php
							printf(
								esc_html__( 'Words by %s on', 'bigbluebox' ),
								esc_html( get_the_author() )
							);
							?>
						</p>
						<p><?php echo esc_html( get_the_date() ); ?></p>
					<?php endif; ?>
					</div>
				</div>

				<?php if ( ! has_category( 'podcasts' ) ) : ?>
					<div class="post-content-meta">
						<p class="post-meta-title"><?php esc_html_e( 'Reading Time', 'bigbluebox' ); ?></p>
						<div class="post-meta-content">
							<p class="reading-time">
								<?php echo bbb_get_icon( '<i data-lucide="clock"></i>' ); ?>
								<?php echo esc_html( bbb_estimated_reading_time() ); ?>
							</p>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( has_tag() ) : ?>
					<div class="post-content-meta post-tags">
						<p class="post-meta-title"><?php esc_html_e( 'Tags', 'bigbluebox' ); ?></p>
						<?php the_tags( '<ul role="list"><li>', '</li><li>', '</li></ul>' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="post-content__container">
		<?php if ( is_single() && ! has_category( 'podcasts' ) ) : ?>
			<?php get_template_part('template-parts/content', 'read-progress'); ?>
		<?php endif; ?>
		<div class="article-body flow-large">
			<?php
			if ( function_exists( 'get_field' ) ) {
				$summary_point_one = get_field( 'summary_point_1' );
				$summary_point_two = get_field( 'summary_point_2' );
				$summary_point_three = get_field( 'summary_point_3' );

				if ( $summary_point_one || $summary_point_two || $summary_point_three ) {
					?>

					<section class="post-summary call-out-panel-large rounded">
					<h4><?php esc_html_e( 'Summary', 'bigbluebox' ); ?></h4>
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
						$guid           = get_field( 'captivate_episode_guid' );
						$episode_number = get_field( 'podcast_episode_number' );
						$episode_type   = get_field( 'podcast_episode_type' );
						$episode_title  = get_field( 'episode' );

						$episode_number_display = $episode_number;
						$normalized_episode_type = $episode_type ? strtolower( trim( $episode_type ) ) : '';
						if ( in_array( $normalized_episode_type, array( 'bonus', 'bonus episode' ), true ) ) {
							$episode_number_display = __( 'Bonus', 'bigbluebox' );
						}

						if ( ! $episode_title ) {
							$episode_title = get_the_title();
						}

						bbb_log( '🎯 GUID from ACF: ' . print_r( $guid, true ) );

						// Prefer the audio URL stored during acf/save_post; avoid front-end API calls.
						$audio_url = get_field( 'captivate_audio_url' );
						if ( ! $audio_url ) {
							$audio_url = get_post_meta( get_the_ID(), 'captivate_audio_url', true );
						}

						if ( ! $audio_url && $guid ) {
							$audio_url = bbb_get_captivate_audio_url( $guid );
						}

						// Fallback for local dev
						if ( ! $audio_url && wp_get_environment_type() === 'local' ) {
							$audio_url = 'https://traffic.libsyn.com/secure/examplepodcast/example-episode.mp3';
							bbb_log( '🔊 Using fallback audio URL for local dev.' );
						}

						if ( $audio_url ) :
							?>
							<div class="podcast-player flow">
								<audio
									id="player"
									class="vlite-js"
									preload="none"
									data-episode-number="<?php echo esc_attr( (string) $episode_number_display ); ?>"
									data-episode-title="<?php echo esc_attr( (string) $episode_title ); ?>"
									aria-label="<?php echo esc_attr( sprintf( esc_html__( 'Podcast audio player for %s', 'bigbluebox' ), $episode_title ) ); ?>"
								>
									<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg" />
									<?php esc_html_e( 'Your browser does not support the audio element.', 'bigbluebox' ); ?>
								</audio>
							</div>
							<?php
						endif;
					endif;
				?>

			<section class="entry-content flow">
				<?php the_content(); ?>
			</section>

			<?php get_template_part('template-parts/content', 'review-score'); ?> 

			<?php if ( in_category( 'podcasts' ) ) : ?>
				<?php get_template_part( 'template-parts/content', 'podcast-transcript' ); ?>

				<div class="podcast-app-links">
					<h6>
						<?php esc_html_e( 'Want to listen on your favourite podcast app?', 'bigbluebox' ); ?>
					</h6>
					<?php get_template_part('template-parts/content', 'podcast-apps-links'); ?>
				</div>
			<?php endif; ?>
			
		</div>

		<aside class="article-sidebar flow-large">
			<section class="author-info flow-small">
			<?php
				$author_id = get_the_author_meta( 'ID' );
				$bio_short = get_field( 'bio_short', 'user_' . $author_id );
				
				// Output the author's avatar using custom image array and fallback
				$author_name = get_the_author_meta('display_name', $author_id);
				$author_images = array(
					'Garry' => 'author-avatar-small-garry.webp',
					'Maria Kalotichou' => 'author-avatar-small-maria.webp',
					'Jordan Shortman' => 'author-avatar-small-jordan.webp',
					'Harry Walker' => 'author-avatar-small-harry.webp',
					'Matt Steele' => 'author-avatar-small-matt.webp'
				);
				if ( array_key_exists($author_name, $author_images) ) {
					$author_image_url = get_template_directory_uri() . '/images/authors/' . $author_images[$author_name];
				} else {
					$author_image_url = get_template_directory_uri() . '/images/authors/author-avatar-small-default.webp';
				}
				echo '<img class="author-image" src="' . esc_url($author_image_url) . '" width="80" height="80" alt="' . esc_attr($author_name) . '" />';
				// Author name
				echo '<h5 class="author-name">' . esc_html( $author_name ) . '</h5>';

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
						? sprintf( esc_html__( 'More about %s', 'bigbluebox' ), esc_html( $first_name ) )
						: esc_html__( 'More about the author', 'bigbluebox' );

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
