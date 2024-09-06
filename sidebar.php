<?php
/**
 * The sidebar containing custom sections and widgets.
 *
 * @package bigbluebox
 */
?>

<aside id="secondary" class="main-sidebar flow-large">
	<section class="flow-small">
		<p class="semi-bold section-title-small">
			Search the TARDIS
		</p>
		<?php get_search_form(); ?>
	</section>

	<section class="flow-small">
		<p class="semi-bold section-title-small">
			Connect and chat
		</p>
		<ul class="social-links" role="list">
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="X Twitter link">
					<a class="link-alt" href="https://twitter.com/bigblueboxpcast" target="_blank" rel="noreferrer noopener">X (Twitter)</a>
				</div>
			</li>
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-insta.svg" width="21" height="21" alt="Instagram link">
					<a class="link-alt" href="https://instagram.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">Instagram</a>
				</div>
			</li>
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="Facebook link">
					<a class="link-alt" href="https://facebook.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">Facebook</a>
				</div>
			</li>
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="Threads link">
					<a class="link-alt" href="https://www.threads.net/@bigblueboxpodcast" target="_blank" rel="noreferrer noopener">Threads</a>
				</div>
			</li>
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="RSS podcasts link">
					<a class="link-alt" href="https://feeds.captivate.fm/doctor-who-big-blue-box-podcast/" target="_blank" rel="noreferrer noopener">RSS feed - podcasts</a>
				</div>
			</li>
			<li>
				<div class="social-links__item">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="RSS articles link">
					<a class="link-alt" href="https://www.bigblueboxpodcast.co.uk/category/articles/feed/" target="_blank" rel="noreferrer noopener">RSS feed - articles</a>
				</div>
			</li>
		</ul>
	</section>

	<section class="call-out-panel discord-link">
		<div class="intro">
			<img src="<?php echo get_bloginfo('template_url') ?>/images/logos/logo-discord.svg" width="121" height="23" alt="Our free Discord server">
			<p class="small">
				Join the Big Blue Box Discord server - it’s free! Hang out with Doctor Who fans in a safe space to discuss episodes, theories, events, Big Finish, collecting and more!
			</p>
		</div>
		<a class="button-ghost" href="https://discord.gg/QfHAmyVdaJ" target="_blank" rel="noreferrer noopener">
			Join us on Discord now
		</a>
	</section>

	<section class="flow-small">
		<p class="semi-bold section-title-small">
			Meet our writers
		</p>
		<div class="author-section-small">
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-jordan.webp" width="56" height="56" alt="Jordan Shortman author image">
				<div class="author-block-content">
						<p class="semi-bold">
							<?php the_author_posts_link(); ?>
						</p>
					<p class="small">
						Big Finish and book reviews are Jordans cup of tea.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-maria.webp" width="56" height="56" alt="Maria Kalotichou author image">
				<div class="author-block-content">
					<p class="semi-bold">
						Maria Kalotichou
					</p>
					<p class="small">
						Our roving on-location reviewer for UK cons and meet ups plus Big Finish reviews.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-matt.webp" width="56" height="56" alt="Matt Steele author image">
				<div class="author-block-content">
					<p class="semi-bold">
						Matt Steele
					</p>
					<p class="small">
						Matt runs the merch updates area on our Discord server plus reviews TV episodes.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-harry.webp" width="56" height="56" alt="Harry Walker author image">
				<div class="author-block-content">
					<p class="semi-bold">
						Harry Walker
					</p>
					<p class="small">
						Harry puts his writing skills to work with creative editorials and reviews.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<div class="new-author-img rounded-small">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-newauthor.svg" width="40" height="40" alt="Maybe you could join the team?">
				</div>
				<div class="author-block-content">
					<p class="semi-bold">
						Maybe you?
					</p>
					<p class="small">
						We’re always looking for writers to volunteer for the blog. Do you know your Doctor Who and have decent writing skills? <a class="link-alt" href="#">Get in touch!</a>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section>
		<a href="https://www.youtube.com/user/thegeekshandbag" target="_blank" rel="noreferrer noopener">
			<img class="sidebar-img" src="<?php echo get_bloginfo('template_url') ?>/images/sidebar-panel-geekshandbag.webp" width="329" height="205" alt="Maybe you could join the team?">
		</a>
	</section>

	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->