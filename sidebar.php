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
			Social and chat
		</h5>
		<ul role="list">
			<li>
				<a class="has-external-icon" href="https://bsky.app/profile/bigblueboxpodcast.bsky.social" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="X Bluesky link">
						<p class="small">Bluesky</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://twitter.com/bigblueboxpcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="X Twitter link">
						<p class="small">X (Twitter)</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://instagram.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-insta.svg" width="21" height="21" alt="Instagram link">
						<p class="small">Instagram</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://facebook.com/bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="Facebook link">
						<p class="small">Facebook</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://www.threads.net/@bigblueboxpodcast" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="Threads link">
						<p class="small">Threads</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://feeds.captivate.fm/doctor-who-big-blue-box-podcast/" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="RSS podcasts link">
						<p class="small">RSS feed - Podcasts</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
			</li>
			<li>
				<a class="has-external-icon" href="https://www.bigblueboxpodcast.co.uk/category/articles/feed/" target="_blank" rel="noreferrer noopener">
					<div class="social-channels__item">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-rss.svg" width="21" height="21" alt="RSS articles link">
						<p class="small">RSS feed - Articles</p>
					</div>
					<img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
				 </a>
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

	<section class="flow">
		<h5>
			Meet your writers
		</h5>
		<div class="author-section-small">
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-jordan.webp" width="56" height="56" alt="Jordan Shortman author image">
				<div class="author-block-content">
					<h6>
						<?php the_author_posts_link(); ?>
					</h6>
					<p class="small">
						Big Finish and book reviews are Jordans cup of tea.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-maria.webp" width="56" height="56" alt="Maria Kalotichou author image">
				<div class="author-block-content">
					<h6>
						Maria Kalotichou
					</h6>
					<p class="small">
						Our roving on-location reviewer for UK cons and meet ups plus Big Finish reviews.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-matt.webp" width="56" height="56" alt="Matt Steele author image">
				<div class="author-block-content">
					<h6>
						Matt Steele
					</h6>
					<p class="small">
						Matt runs the merch updates area on our Discord server plus reviews TV episodes.
					</p>
				</div>
			</div>
			<div class="author-section-small__author-block">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/authors/author-avatar-harry.webp" width="56" height="56" alt="Harry Walker author image">
				<div class="author-block-content">
					<h6>
						Harry Walker
					</h6>
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
					<h6>
						Maybe you?
					</h6>
					<p class="small">
						We’re always looking for writers to volunteer for the blog. Do you know your Doctor Who and have decent writing skills? <a class="link-alt" href="#">Get in touch!</a>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section>
		<a href="https://www.youtube.com/user/thegeekshandbag" target="_blank" rel="noreferrer noopener">
			<img class="sidebar-img img-hover" src="<?php echo get_bloginfo('template_url') ?>/images/sidebar-panel-geekshandbag.webp" width="329" height="205" alt="Adam's YouTube channel - The Geeks Handbag.">
		</a>
	</section>
</aside><!-- #secondary -->