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
    return 30;
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
 * Enqueue scripts and styles.
 */
function bigbluebox_scripts() {
	wp_enqueue_style( 'bigbluebox-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_enqueue_script( 'bigbluebox-scripts', get_template_directory_uri() . '/scripts/bbb-scripts.min.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bigbluebox_scripts' );


/* Includes from starter theme */

// /**
//  * Custom template tags for this theme.
//  */
// require get_template_directory() . '/inc/template-tags.php';

// /**
//  * Functions which enhance the theme by hooking into WordPress.
//  */
// require get_template_directory() . '/inc/template-functions.php';

// /**
//  * Customizer additions.
//  */
// require get_template_directory() . '/inc/customizer.php';

// /**
//  * Load Jetpack compatibility file.
//  */
// if ( defined( 'JETPACK__VERSION' ) ) {
// 	require get_template_directory() . '/inc/jetpack.php';
// }
