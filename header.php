<?php
/**
 * @package Big_Blue_Box
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php if ( is_front_page() || is_home() ) : ?>
	<link rel="preload" as="image" type="image/avif" href="<?php echo esc_url( get_template_directory_uri() . '/images/pagebg_home.avif' ); ?>" fetchpriority="high">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'bigbluebox' ); ?></a>

	<header role="banner" class="site-head">
		<div class="header-backdrop"></div>
		<div class="wrapper">
			<div class="site-head__inner">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-head__home">
					<span class="visually-hidden"><?php esc_html_e( 'The Big Blue Box Podcast', 'bigbluebox' ); ?></span>
					<div class="site-head__logo">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/images/logos/logo-horizontal.svg' ); ?>" width="251" height="53" alt="<?php echo esc_attr__( 'The Big Blue Box Podcast', 'bigbluebox' ); ?>">
					</div>
				</a>

				<div class="site-head__navigation">
					<button class="menu-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
						<p class="visually-hidden"><?php esc_html_e( 'Menu', 'bigbluebox' ); ?></p>
						<span></span>
						<span></span>
						<span></span>
					</button>

					<nav class="nav-container" aria-label="<?php esc_attr_e( 'primary', 'bigbluebox' ); ?>">
						<div id="primary-navigation" class="nav-drawer" data-visible="false">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'main-nav',
									'container' => false,
									'items_wrap' => '<ul class="nav" role="list">%3$s</ul>',
								)
							);
							?>
							<div class="nav-logo">
								<img src="<?php echo esc_url( get_template_directory_uri() . '/images/logos/logo-horizontal.svg' ); ?>" width="251" height="53" alt="<?php echo esc_attr__( 'The Big Blue Box Podcast', 'bigbluebox' ); ?>">
							</div>
						</div>
					</nav>
				</div>
			</div>
		</div>
	</header>

	<div class="search-overlay" id="searchOverlay" aria-hidden="true">
		<div class="search-overlay__content">
			<?php get_search_form(); ?>
		</div>
	</div>
