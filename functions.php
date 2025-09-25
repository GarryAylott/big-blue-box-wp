<?php
/**
 * @package Big_Blue_Box
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Theme defaults and registers support for various WordPress features.
 */
function bigbluebox_setup() {
	/*
	* Make theme available for translation.
	*/
	load_theme_textdomain( 'bigbluebox', get_template_directory() . '/languages' );

	/*
	* Add default posts and comments RSS feed links to head.
	*/
	add_theme_support( 'automatic-feed-links' );

	/*
	* Let WordPress manage the document title.
	* By adding theme support, we declare that this theme does not use a
	* hard-coded <title> tag in the document head, and expect WordPress to
	* provide it for us.
	*/
	add_theme_support( 'title-tag' );

	/*
	* Enable support for Post Thumbnails on posts and pages.
	*
	* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	*/
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'homepage-thumb', 690, 999 );
	add_image_size( 'singlepost-feat', 1200, 600 );
	add_image_size( 'singlepost-wide', 1200, 675, true );
	add_image_size( 'singlepost-square', 1200, 9999, true );
	add_image_size( 'post-list-thumb', 400, 225, true );

	/*
	* Switch default core markup for search form, comment form, and comments
	* to output valid HTML5.
	*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'bigbluebox_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'bigbluebox_setup' );


/**
 * Add custom search icon to dynamic WP top nav
 */
function add_search_icon_to_menu($items, $args) {
	if ($args->theme_location === 'main-nav') {
		$search_icon = '<li class="menu-item search-menu-item">
			<a href="#" class="nav-search-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
					<path d="M13 6.5a6.499 6.499 0 0 1-1.25 3.844l3.938 3.969a.964.964 0 0 1 0 1.406.964.964 0 0 1-1.407 0l-3.969-3.969C9.25 12.563 7.906 13 6.5 13A6.495 6.495 0 0 1 0 6.5C0 2.937 2.906 0 6.5 0 10.063 0 13 2.938 13 6.5ZM6.5 11a4.463 4.463 0 0 0 3.875-2.25 4.458 4.458 0 0 0 0-4.5C9.562 2.875 8.094 2 6.5 2a4.54 4.54 0 0 0-3.906 2.25 4.458 4.458 0 0 0 0 4.5A4.475 4.475 0 0 0 6.5 11Z"/>
				</svg>
			</a>
		</li>';
		$items .= $search_icon;
	}
	return $items;
}
add_filter('wp_nav_menu_items', 'add_search_icon_to_menu', 10, 2);


/**
 * Custom admin footer
 */
function modify_footer() {
	echo 'Created by Garry Aylott for The Big Blue Box Podcast. ';
}
add_filter( 'admin_footer_text', 'modify_footer' );


/**
 * Always show second edit bar in TinyMCE
 */
function show_tinymce_toolbar( $in ) {
	$in['wordpress_adv_hidden'] = false;
	return $in;
}
add_filter( 'tiny_mce_before_init', 'show_tinymce_toolbar' );

/**
 * Add lead class to first paragraph
 */
function first_paragraph( $content ) {
	return preg_replace( '/<p([^>]+)?>/', '<p$1 class="lead">', $content, 1 );
}
add_filter( 'the_content', 'first_paragraph' );

function register_my_menus() {
	register_nav_menus(
		array(
			'main-nav' => __( 'Main Nav' ),
			'footer-menu-legals' => __( 'Footer Menu Legals' ),
			'footer-menu-bbb' => __( 'Footer Menu BBB' ),
		)
	);
}
add_action( 'init', 'register_my_menus' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bigbluebox_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'bigbluebox' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'bigbluebox' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'bigbluebox_widgets_init' );

/**
 * Modify excerpt length
 */
function custom_excerpt_length( $length ) {
	return 80;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
 * Change More excerpt
 */
function custom_more_excerpt( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'custom_more_excerpt' );

/**
 * Strip WP inline image and figure hard widths and heights
 */
add_filter('the_content', 'bbbx_clean_gutenberg_images', 20);

function bbbx_clean_gutenberg_images($content) {
	libxml_use_internal_errors(true);
	$doc = new DOMDocument();
	$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);

	// Loop through all <img> and remove width/height
	foreach ($doc->getElementsByTagName('img') as $img) {
		$img->removeAttribute('width');
		$img->removeAttribute('height');
	}

	// Loop through all <figure> and remove style="width: ..."
	foreach ($doc->getElementsByTagName('figure') as $figure) {
		if ($figure->hasAttribute('style')) {
			$figure->removeAttribute('style');
		}
	}

	$body = $doc->getElementsByTagName('body')->item(0);
	$new_content = '';
	foreach ($body->childNodes as $child) {
		$new_content .= $doc->saveHTML($child);
	}

	return $new_content;
}

/**
 * Stop WP interferring with front-end image manipulation when images are set to "auto" (this will likely be fixed in an upcoming WP update).
 */
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );

/**
 * Single post reading time estimation.
 */
function bbb_estimated_reading_time( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content = get_post_field( 'post_content', $post_id );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$reading_time = max( 1, ceil( $word_count / 200 ) );

	$reading_time_text = sprintf(
		_n( '%d min to read', '%d mins to read', $reading_time, 'bigbluebox' ),
		$reading_time
	);

	return apply_filters( 'bbb_estimated_reading_time', $reading_time_text, $reading_time, $post_id );
}

function bbb_get_icon( $name ) {
	$path = get_template_directory() . '/images/icons/' . sanitize_file_name( $name ) . '.svg';

	if ( file_exists( $path ) ) {
		return file_get_contents( $path );
	}
	return '';
}

/**
 * Add category slugs to body class on single posts
 */
add_filter('body_class', function($classes) {
	if (is_single()) {
		$categories = get_the_category();
		if ($categories) {
			foreach ($categories as $category) {
				$classes[] = 'category-' . sanitize_html_class($category->slug);
			}
		}
	}
	return $classes;
});

/**
 * Remove unwanted theme class from body_class
 */
add_filter('body_class', function($classes) {
	foreach ($classes as $key => $class) {
		if (strpos($class, 'wp-theme-') === 0) {
			unset($classes[$key]);
		}
	}
	return $classes;
});

/**
 * AJAX handler for category-based post filtering
 */
function filter_posts_by_category() {
	$category = sanitize_text_field($_POST['category'] ?? '');
	$search_term = sanitize_text_field($_POST['s'] ?? '');
	$context = sanitize_text_field($_POST['context'] ?? '');

	$args = [
		'post_status'    => 'publish',
		'posts_per_page' => get_option('posts_per_page'),
		'post_type'      => 'post',
		'paged'          => isset($_POST['paged']) ? (int) $_POST['paged'] : 1,
	];

	if ($context === 'search') {
		if (!empty($search_term)) {
			$args['s'] = $search_term;
		}
		if ($category !== 'all') {
			$args['category_name'] = $category;
		}
	} else {
		if ($category !== 'all') {
			$args['category_name'] = $category;
		}
		unset($args['s']); // ensure no accidental search
	}

	// Make category/search term available to pagination.php
	if ($context === 'search') {
		$_GET['category'] = $category;
		$_GET['s'] = $search_term;
	}

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		ob_start();

		while ($query->have_posts()) {
			$query->the_post();
			get_template_part('template-parts/content', 'post-cards', ['card_type' => 'browse']);
		}

		// Only show pagination if not on homepage (context !== 'home')
		if ($context !== 'home') {
			bbb_custom_pagination($query);
		}

		wp_reset_postdata();
		echo ob_get_clean();
	} else {
		echo '<p>No posts found.</p>';
	}

	wp_die();
}
add_action('wp_ajax_filter_posts_by_category', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts_by_category', 'filter_posts_by_category');

add_filter('query_vars', function($vars) {
	$vars[] = 'replytocom';
	return $vars;
});

/**
 * Customize the number of posts per page for archives.
 */
add_action('pre_get_posts', function($query) {
	// Only target main queries on frontend, and for archives
	if (!is_admin() && $query->is_main_query() && (is_category() || is_tag() || is_post_type_archive())) {
		$query->set('posts_per_page', 15);
	}
});

// No need to load jQuery
function bbb_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'bbb_remove_jquery_migrate' );

// Pagination
require get_template_directory() . '/inc/pagination.php';

// ACF Fields
require get_template_directory() . '/inc/acf-fields.php';

// Captivate API Tools
require get_template_directory() . '/inc/api-shutdown.php';

// Custom comments section.
require get_template_directory() . '/inc/custom-comments.php';

// Captivate external audio
require_once get_template_directory() . '/inc/captivate-external-audio.php';

// Helper for suggested/related posts
require get_template_directory() . '/inc/related-articles.php';

// Logic for post promo banner insertion
require get_template_directory() . '/inc/article-promo-banners.php';

// Compendium data helper
require_once get_stylesheet_directory() . '/inc/reviews-compendium.php';

// Gutenberg blocks initialisation
require get_template_directory() . '/inc/blocks/init.php';

// Enqueue scripts and styles.
require get_template_directory() . '/inc/enqueue.php';

// Includes from starter theme */
// Functions which enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/template-functions.php';


if (isset($_GET['auto_assign_captivate_episodes'])) {
	include __DIR__ . '/auto-assign-captivate-episodes.php';
	exit;
}
