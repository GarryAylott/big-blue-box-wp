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
						<?php esc_html_e( 'Error 404 - Page Not Found', 'bigbluebox' ); ?>
					</h6>
					<h1><?php esc_html_e( 'What?! The TARDIS has exploded!', 'bigbluebox' ); ?></h1>
				</header>

				<section class="error-404__content flow-tiny">
					<h5><?php esc_html_e( 'It looks like the TARDIS has burst into flames and taken this page with it.', 'bigbluebox' ); ?></h5>
					<p><?php echo wp_kses_post( __( 'While we repair the time circuits&hellip;', 'bigbluebox' ) ); ?></p>
					<ul class="flow-tiny">
						<li>
							<strong><?php esc_html_e( 'Use the navigation:', 'bigbluebox' ); ?></strong>
							<?php esc_html_e( 'Try the menu at the top to find your way.', 'bigbluebox' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Search The Big Blue Box:', 'bigbluebox' ); ?></strong>
							<?php echo wp_kses_post( __( 'Give the search bar below a whirl&hellip;', 'bigbluebox' ) ); ?>
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
							<?php esc_html_e( 'to head back home.', 'bigbluebox' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Check out the recent posts:', 'bigbluebox' ); ?></strong>
							<?php esc_html_e( 'Why not have a gander at our latest posts below.', 'bigbluebox' ); ?>
						</li>
						</ul>
				</section>
			</div>
			<picture>
				<?php $theme_dir = esc_url( get_template_directory_uri() ); ?>
				<source srcset="<?php echo $theme_dir; ?>/images/TARDIS-busted.avif" type="image/avif">
				<source srcset="<?php echo $theme_dir; ?>/images/TARDIS-busted.webp" type="image/webp">
				<img src="<?php echo $theme_dir; ?>/images/TARDIS-busted.webp" alt="<?php echo esc_attr__( 'TARDIS illustration', 'bigbluebox' ); ?>">
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
