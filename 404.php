<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Big_Blue_Box
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="wrapper flow-large">
		<section class="error-404">
			<div class="error-404-main">
				<header class="error-404__header">
					<h6 class="section-title">
						Error 404 - Page Not Found
					</h6>
					<h1><?php esc_html_e( 'What?! The TARDIS has exploded!', 'bigbluebox' ); ?></h1>
				</header>

				<section class="error-404__content flow-tiny">
					<h5>It looks like the TARDIS has burst into flames and taken this page with it.</h5>
					<p>While we repair the time circuits&hellip;</p>
					<ul class="flow-tiny">
						<li><strong>Use the navigation:</strong> Try the menu at the top to find your way.</li>
						<li>
							<strong>Search The Big Blue Box:</strong> Give the search bar below a whirl&hellip;
							<?php
							global $bbb_hide_search_form_title;
							$bbb_hide_search_form_title = true;
							get_search_form();
							unset( $bbb_hide_search_form_title );
							?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Return to the home page:', 'bigbluebox' ); ?></strong>
							<a class="link-alt" href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<?php esc_html_e( 'Click here', 'bigbluebox' ); ?>
							</a>
							to head back home.
						</li>
						<li><strong>Check out the recent posts:</strong> Why not have a gander at our latest posts below.</li>
						</ul>
				</section>
			</div>
			<picture>
				<source srcset="<?php echo get_bloginfo('template_url') ?>/images/TARDIS-busted.avif" type="image/avif">
				<source srcset="<?php echo get_bloginfo('template_url') ?>/images/TARDIS-busted.webp" type="image/webp">
				<img src="<?php echo get_bloginfo('template_url') ?>/images/TARDIS-busted.webp" alt="">
			</picture>
		</section>
		<?php get_template_part('template-parts/content', 'suggested-posts',
			array(
				'header_type'  => 'latest',
				'force_latest' => true,
				'latest_limit' => 10,
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
