<?php
/**
 * The sidebar containing custom sections and widgets.
 *
 * @package bigbluebox
 */
?>

<aside id="secondary" class="main-sidebar flow-large">
	<section class="social-channels flow">
		<h5>
			<?php esc_html_e( 'Connect and Follow', 'bigbluebox' ); ?>
		</h5>
		<ul role="list">
			<li>
				<a class="has-external-icon" href="https://bsky.app/profile/bigblueboxpodcast.bsky.social" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="<?php echo esc_attr__( 'Bluesky link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'Bluesky', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://twitter.com/bigblueboxpcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="<?php echo esc_attr__( 'X (Twitter) link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'X (Twitter)', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://instagram.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-insta.svg" width="21" height="21" alt="<?php echo esc_attr__( 'Instagram link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'Instagram', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://facebook.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="<?php echo esc_attr__( 'Facebook link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'Facebook', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://www.threads.net/@bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="<?php echo esc_attr__( 'Threads link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'Threads', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://feeds.captivate.fm/doctor-who-big-blue-box-podcast/" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="<?php echo esc_attr__( 'RSS podcasts link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'RSS: Podcasts', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://www.bigblueboxpodcast.co.uk/category/articles/feed/" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="<?php echo esc_attr__( 'RSS articles link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'RSS: Articles', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://www.bigblueboxpodcast.co.uk/feed/" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="<?php echo esc_attr__( 'RSS everything link', 'bigbluebox' ); ?>">
						<h6><?php esc_html_e( 'RSS: Everything', 'bigbluebox' ); ?></h6>
					</div>
					<i data-lucide="arrow-up-right"></i>
				 </a>
			</li>
		</ul>
	</section>

	<section class="call-out-panel discord-link">
		<div class="intro">
			<img src="<?php echo get_bloginfo('template_url') ?>/images/logos/logo-discord.svg" width="121" height="23" alt="<?php echo esc_attr__( 'Our free Discord server', 'bigbluebox' ); ?>">
			<p class="small">
				<?php esc_html_e( 'Join the Big Blue Box Discord server - it\'s free! Hang out with Doctor Who fans in a safe space to discuss episodes, theories, events, Big Finish, collecting and more!', 'bigbluebox' ); ?>
			</p>
		</div>
		<a class="button-ghost has-external-icon" href="https://discord.gg/skZXdxUNkB" target="_blank" rel="noreferrer noopener">
			<?php esc_html_e( 'Join us on Discord now', 'bigbluebox' ); ?>
		</a>
	</section>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<section class="widget-area">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</section>
	<?php endif; ?>

	<section class="flow">
		<h5>
			<?php esc_html_e( 'Meet your writers', 'bigbluebox' ); ?>
		</h5>
		<div class="author-section-small">
			<div class="author-section-small__author-block">
				<a class="author-avatar" href="<?php echo esc_url( get_author_posts_url(3) ); ?>">
					<?php echo get_avatar( 3, 56, '', esc_attr__( 'Jordan Shortman author image', 'bigbluebox' ) ); ?>
				</a>
				<div class="author-block-content">
					<h6>
						<a href="<?php echo esc_url( get_author_posts_url(3) ); ?>">Jordan Shortman</a>
					</h6>
					<p class="small">
						<?php esc_html_e( 'Loves getting stuck into Big Finish stories and Doctor Who books.', 'bigbluebox' ); ?>
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<a class="author-avatar" href="<?php echo esc_url( get_author_posts_url(4) ); ?>">
					<?php echo get_avatar( 4, 56, '', esc_attr__( 'Maria Kalotichou author image', 'bigbluebox' ) ); ?>
				</a>
				<div class="author-block-content">
					<h6>
						<a href="<?php echo esc_url( get_author_posts_url(4) ); ?>">Maria Kalotichou</a>
					</h6>
					<p class="small">
						<?php esc_html_e( 'Our roving reporter for events plus regular reviews of TV episodes, Big Finish and more.', 'bigbluebox' ); ?>
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<a class="author-avatar" href="<?php echo esc_url( get_author_posts_url(8) ); ?>">
					<?php echo get_avatar( 8, 56, '', esc_attr__( 'Matt Steele author image', 'bigbluebox' ) ); ?>
				</a>
				<div class="author-block-content">
					<h6>
						<a href="<?php echo esc_url( get_author_posts_url(8) ); ?>">Matt Steele</a>
					</h6>
					<p class="small">
						<?php esc_html_e( 'Covers the latest merch and reviews Doctor Who TV episodes and Big Finish.', 'bigbluebox' ); ?>
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<a class="author-avatar" href="<?php echo esc_url( get_author_posts_url(7) ); ?>">
					<?php echo get_avatar( 7, 56, '', esc_attr__( 'Harry Walker author image', 'bigbluebox' ) ); ?>
				</a>
				<div class="author-block-content">
					<h6>
						<a href="<?php echo esc_url( get_author_posts_url(7) ); ?>">Harry Walker</a>
					</h6>
					<p class="small">
						<?php esc_html_e( 'Creating editorials and opinion pieces plus reviews of Doctor Who TV episodes.', 'bigbluebox' ); ?>
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<span class="author-avatar no-link">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-small-default.webp" width="56" height="81" alt="<?php echo esc_attr__( 'Maybe you could join the team?', 'bigbluebox' ); ?>">
				</span>
				<div class="author-block-content">
					<h6>
						<?php esc_html_e( 'Maybe you?', 'bigbluebox' ); ?>
					</h6>
					<p class="small">
						<?php
						printf(
							wp_kses_post( __( 'We\'re always looking for writers to volunteer for the blog. Do you know your Doctor Who and have decent writing skills? <a class="link-alt" href="%s">See team openings.</a>', 'bigbluebox' ) ),
							esc_url( home_url( '/team-openings/' ) )
						);
						?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section>
		<a class="has-external-icon" href="https://www.bigfinish.com/" target="_blank" rel="noreferrer noopener">
			<picture class="img-container-square">
				<source srcset="<?php echo get_bloginfo('template_url') ?>/images/ads/big-finish-banner-square.avif" type="image/avif">
				<source srcset="<?php echo get_bloginfo('template_url') ?>/images/ads/big-finish-banner-square.webp" type="image/webp">
				<img class="sidebar-img img-hover" src="<?php echo get_bloginfo('template_url') ?>/images/ads/big-finish-banner-square.webp" width="342" height="342" alt="<?php echo esc_attr__( 'Check out the full range of Doctor Who audio dramas from Big Finish, now.', 'bigbluebox' ); ?>">
			</picture>
		</a>
	</section>
</aside><!-- #secondary -->
