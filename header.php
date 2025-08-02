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
					<span class="visually-hidden">The Big Blue Box Podcast</span>
					<div class="site-head__logo">
						<img src="<?php echo get_bloginfo('template_url') ?>/images/logos/logo-horizontal.svg" width="251" height="53" alt="The Big Blue Box Podcast">
					</div>
				</a>

				<div class="site-head__navigation">
					<button class="menu-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
						<p class="visually-hidden">Menu</p>
						<span></span>
						<span></span>
					</button>

					<?php wp_nav_menu(
						array(
							'theme_location' => 'main-nav',
							'container' => 'nav',
							'container_class' => 'nav-container',	
							'items_wrap' => '<ul id="primary-navigation" data-visible="false" class="nav" role="list" aria-label="primary" >%3$s</ul>'
						)
					); ?>
				</div>
			</div>
		</div>
	</header>

	<div class="search-overlay" id="searchOverlay" aria-hidden="true">
		<div class="search-overlay__content">
			<?php get_search_form(); ?>
		</div>
	</div>
