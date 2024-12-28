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

<div class="page-bg-inline bg-image-fade">
	<img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg-home.jpg" decoding="async" alt="" fetchpriority="high">
</div>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'bigbluebox' ); ?></a>

	<header role="banner" class="site-head">
		<div class="header-backdrop"></div>
		<div class="wrapper">
			<div class="site-head__inner">
				<div class="site-head__logo">
					<img src="<?php echo get_bloginfo('template_url') ?>/images/logos/BBB-logo.svg" width="300" height="77" alt="The Big Blue Box Podcast logo">
				</div>

				<div class="site-head__navigation">
					<button class="menu-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
						<p class="visually-hidden">Menu</p>
						<span></span>
						<!-- <span></span> -->
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
