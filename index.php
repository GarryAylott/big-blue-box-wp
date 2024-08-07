<?php
/**
 * @package Big_Blue_Box
 */

get_header();
?>

	<main id="primary" class="site-main flow-page-regions">
		<div class="wrapper">
			<div class="flow-large">
				<article id="post-<?php the_ID(); ?>" <?php post_class('latest-podcast-ep'); ?>>
					<?php
						$homepage_latest_podcast = new WP_Query(
							array(
								'category_name' => 'podcasts',
								'posts_per_page' => 1
							)
						);
						while ( $homepage_latest_podcast->have_posts() ) : $homepage_latest_podcast->the_post();
					?>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>">
							<img class="post-thumb-img rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>">
						</a>
					<?php endif; ?>

					<div class="latest-podcast-ep__content">
						<div class="flow-small">
							<p class="bold icon-text-group clr-900">
								<svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="none">
									<path d="M7.5 3c0-.813-.688-1.5-1.5-1.5A1.5 1.5 0 0 0 4.5 3v5c0 .844.656 1.5 1.5 1.5A1.5 1.5 0 0 0 7.5 8V3ZM3 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3ZM2 6.75V8c0 2.219 1.781 4 4 4 2.188 0 4-1.781 4-4V6.75a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75V8c0 2.813-2.094 5.094-4.75 5.469V14.5h1.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-4.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75h1.5v-1.031A5.502 5.502 0 0 1 .5 8V6.75A.74.74 0 0 1 1.25 6a.76.76 0 0 1 .75.75Z"/>
								</svg>
								Latest Podcast <span>•</span> <?php the_field('episode_number'); ?>
							</p>
							<div class="latest-podcast-ep__details">
								<a href="<?php the_permalink(); ?>">
									<h4>
										<?php
											$thetitle = $post->post_title;
											$getlength = strlen($thetitle);
											$thelength = 80;
											echo substr($thetitle, 0, $thelength);
											if ($getlength > $thelength) echo "...";
										?>
									</h4>
								</a>
								<?php echo the_excerpt(); ?>
							</div>
						</div>
						
						<?php get_template_part( 'template-parts/content', 'author-meta' ); ?>
					</div>
					
					<?php endwhile;
						wp_reset_postdata();
					?>
				</article>

				<?php get_template_part( 'template-parts/content', 'latest-articles' ); ?>
			</div>
		</div>
	</main>
<?php
get_sidebar();
get_footer();
