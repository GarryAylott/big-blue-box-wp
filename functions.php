<?php
/**
 * @package Big_Blue_Box
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Internal debug logger — no-op unless WP_DEBUG is enabled.
 *
 * @param string $message Log message.
 */
function bbb_log( $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $message );
	}
}

/**
 * Theme defaults and registers support for various WordPress features.
 */
function bigbluebox_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'bigbluebox', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Custom image sizes
	add_image_size( 'post-featured-card', 1200, 675, true );
	add_image_size( 'post-featured-large', 2400, 1350, true );
	add_image_size( 'latest-podcast-ep-thumb', 640, 360 );
	add_image_size( 'singlepost-wide', 1200, 675, true );
	add_image_size( 'singlepost-square', 1200, 9999, true );
	add_image_size( 'post-list-thumb', 400, 225, true );

	// Remove un-needed WP generated image sizes
	add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
		unset( $sizes['thumbnail'] );     // 150 × 150
		unset( $sizes['medium'] );        // 300 × 300
		unset( $sizes['medium_large'] );  // 768 px wide:contentReference[oaicite:1]{index=1}
		unset( $sizes['large'] );         // 1024 × 1024
		return $sizes;
	});

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
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

	// Register menu locations
	function register_my_menus() {
		register_nav_menus(
			array(
				'main-nav'            => esc_html__( 'Main Nav', 'bigbluebox' ),
				'footer-menu-col-1' => esc_html__( 'Footer Menu Col 1', 'bigbluebox' ),
				'footer-menu-col-2'    => esc_html__( 'Footer Menu Col 2', 'bigbluebox' ),
			)
		);
	}
	add_action( 'init', 'register_my_menus' );

	// Register widget areas
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

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'bigbluebox_setup' );

// Trim unneeded core image sizes
add_filter(
	'intermediate_image_sizes_advanced',
	function ( $sizes ) {
		unset( $sizes['medium_large'] );
		// Leave 1536/2048 enabled so hero srcsets have larger steps.
		return $sizes;
	}
);

// Expose custom sizes in the media modal with friendly labels.
add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		$sizes['singlepost-wide']   = esc_html__( 'Post Image Wide', 'bigbluebox' );
		$sizes['singlepost-square'] = esc_html__( 'Post Image Square', 'bigbluebox' );
		return $sizes;
	}
);

/**
 * Add custom search icon to dynamic WP top nav
 */
function add_search_icon_to_menu($items, $args) {
	if ($args->theme_location === 'main-nav') {
		$search_label = esc_html__( 'Open search', 'bigbluebox' );
		$search_icon = '<li class="menu-item search-menu-item">
			<a href="#" class="nav-search-icon">
				<i data-lucide="search" class="icon-bold"></i>
				<span class="search-shortcut" aria-hidden="true"></span>
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
        echo esc_html__( 'Created by Garry Aylott for The Big Blue Box Podcast.', 'bigbluebox' ) . ' ';
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
	if ( is_admin() || is_feed() || ! is_singular() || ! in_the_loop() ) {
		return $content;
	}

	return preg_replace( '/<p([^>]+)?>/', '<p$1 class="lead">', $content, 1 );
}
add_filter( 'the_content', 'first_paragraph' );

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
	if ( is_admin() || is_feed() || ! in_the_loop() || ! is_string( $content ) || '' === $content ) {
		return $content;
	}

	$internal_errors = libxml_use_internal_errors( true );
	$doc             = new DOMDocument();

	try {
		$loaded = $doc->loadHTML( '<?xml encoding="utf-8" ?>' . $content );
		if ( ! $loaded ) {
			return $content;
		}

		// Loop through all <img> and remove width/height
		foreach ( $doc->getElementsByTagName( 'img' ) as $img ) {
			$img->removeAttribute( 'width' );
			$img->removeAttribute( 'height' );
		}

		// Loop through all <figure> and remove style="width: ..." (skip embeds)
		foreach ( $doc->getElementsByTagName( 'figure' ) as $figure ) {
			// Skip embed blocks - they contain iframes that wp_kses_post would strip
			$class = $figure->getAttribute( 'class' );
			if ( str_contains( $class, 'wp-block-embed' ) ) {
				continue;
			}
			if ( $figure->hasAttribute( 'style' ) ) {
				$figure->removeAttribute( 'style' );
			}
		}

		$body = $doc->getElementsByTagName( 'body' )->item( 0 );
		if ( ! $body ) {
			return $content;
		}

		$new_content = '';
		foreach ( $body->childNodes as $child ) {
			$new_content .= $doc->saveHTML( $child );
		}

		return $new_content;
	} catch ( Throwable $e ) {
		bbb_log( '❌ Failed to clean Gutenberg images: ' . $e->getMessage() );
		return $content;
	} finally {
		libxml_clear_errors();
		libxml_use_internal_errors( $internal_errors );
	}
}

/**
 * Stop WP interfering with front-end image manipulation when images are set to "auto" (this will likely be fixed in an upcoming WP update).
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
		_n( '%d min', '%d mins', $reading_time, 'bigbluebox' ),
		$reading_time
	);

	return apply_filters( 'bbb_estimated_reading_time', $reading_time_text, $reading_time, $post_id );
}

function bbb_get_icon( $name ) {
	if ( ! is_string( $name ) ) {
		return '';
	}

	if ( str_contains( $name, '<' ) ) {
		$allowed_tags = array(
			'i' => array(
				'aria-hidden' => true,
				'class' => true,
				'data-lucide' => true,
			),
		);
		return wp_kses( $name, $allowed_tags );
	}

	$path = get_template_directory() . '/images/icons/' . sanitize_file_name( $name ) . '.svg';
	if ( file_exists( $path ) ) {
		return file_get_contents( $path );
	}
	return '';
}

/**
 * Add category slugs to body class on single posts plus tidy up page body classes
 */
function bigbluebox_customize_body_classes( $classes ) {
	if ( is_single() ) {
		$categories = get_the_category();

		if ( $categories ) {
			foreach ( $categories as $category ) {
				$classes[] = 'category-' . sanitize_html_class( $category->slug );
			}
		}
	}

	if ( is_page_template() ) {
		$template = get_page_template_slug( get_queried_object_id() );

		if ( $template ) {
			$basename = sanitize_title( basename( $template, '.php' ) );

			$classes = array_values(
				array_filter(
					$classes,
					static fn ( $class ) => 'page-template' === $class || 0 !== strpos( $class, 'page-template-' )
				)
			);

			$classes[] = 'page-template-' . $basename;
		}
	}

	$classes = array_values(
		array_filter(
			$classes,
			static fn ( $class ) => 0 !== strpos( $class, 'wp-theme-' )
		)
	);

	return $classes;
}
add_filter( 'body_class', 'bigbluebox_customize_body_classes' );

/**
 * AJAX handler for category-based post filtering
 */
function filter_posts_by_category() {
	check_ajax_referer( 'bbb_filter_posts', 'nonce' );

	$category    = isset( $_POST['category'] ) ? sanitize_key( wp_unslash( $_POST['category'] ) ) : '';
	$search_term = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
	$context     = isset( $_POST['context'] ) ? sanitize_key( wp_unslash( $_POST['context'] ) ) : '';
	$paged       = isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1;

	$posts_per_page = get_option( 'posts_per_page' );
	if ( 'home' === $context ) {
		$posts_per_page = 14;
	}
	if ( 'category' === $context ) {
		$posts_per_page = 15;
	}

	$args = array(
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'post_type'      => 'post',
		'paged'          => $paged,
		'ignore_sticky_posts' => true,
	);

	$podcasts_cat = get_category_by_slug( 'podcasts' );
	$podcasts_cat_id = $podcasts_cat ? (int) $podcasts_cat->term_id : 0;

	if ( 'search' === $context ) {
		if ( $search_term ) {
			$args['s'] = $search_term;
		}
		if ( $category && 'all' !== $category ) {
			if ( 'non-podcasts' === $category && $podcasts_cat_id ) {
				$args['category__not_in'] = array( $podcasts_cat_id );
			} else {
				$args['category_name'] = $category;
			}
		}
	} else {
		if ( $category && 'all' !== $category ) {
			if ( 'non-podcasts' === $category && $podcasts_cat_id ) {
				$args['category__not_in'] = array( $podcasts_cat_id );
			} else {
				$args['category_name'] = $category;
			}
		}
	}

	if ( 'home' === $context ) {
		$args['no_found_rows'] = true;
	}

	$query = new WP_Query( $args );
	$html       = '';
	$pagination = '';

	if ( $query->have_posts() ) {
		ob_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/content', 'post-cards', array( 'card_type' => 'browse' ) );
		}

		$html = ob_get_clean();

		if ( 'home' !== $context ) {
			$pagination_base = '';
			if ( 'category' === $context ) {
				if ( 'non-podcasts' === $category ) {
					$articles_cat = get_category_by_slug( 'articles' );
					$pagination_base = $articles_cat ? get_category_link( $articles_cat->term_id ) : '';
				} elseif ( $category ) {
					$active_cat = get_category_by_slug( $category );
					$pagination_base = $active_cat ? get_category_link( $active_cat->term_id ) : '';
				}
			}
			ob_start();
			bbb_custom_pagination( $query, $pagination_base );
			$pagination = ob_get_clean();
		}
		wp_reset_postdata();
	} else {
		$html = '<p>' . esc_html__( 'No posts found.', 'bigbluebox' ) . '</p>';
	}

	wp_send_json_success(
		array(
			'content'    => $html,
			'pagination' => $pagination,
		)
	);
}
add_action('wp_ajax_filter_posts_by_category', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts_by_category', 'filter_posts_by_category');

add_filter('query_vars', function($vars) {
	$vars[] = 'replytocom';
	return $vars;
});

/**
 * Custom REST endpoint to list team members for the Thoughts from Team block.
 *
 * The default /wp/v2/users endpoint requires 'list_users' capability which
 * Authors don't have. This custom endpoint allows anyone who can edit posts
 * to fetch a list of authors and editors for use in the block.
 */
function bbb_register_team_members_endpoint() {
	register_rest_route( 'bbb/v1', '/team-members', array(
		'methods'             => 'GET',
		'callback'            => 'bbb_get_team_members',
		'permission_callback' => function () {
			return current_user_can( 'edit_posts' );
		},
	) );
}
add_action( 'rest_api_init', 'bbb_register_team_members_endpoint' );

/**
 * Return list of authors and editors for the Thoughts from Team block.
 */
function bbb_get_team_members() {
	$users = get_users( array(
		'role__in' => array( 'author', 'editor', 'administrator' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC',
	) );

	$team_members = array();
	foreach ( $users as $user ) {
		$team_members[] = array(
			'id'          => $user->ID,
			'name'        => $user->display_name,
			'avatar_urls' => array(
				'48' => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
				'96' => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
			),
		);
	}

	return rest_ensure_response( $team_members );
}

/**
 * Customize the number of posts per page for archives.
 */
add_action('pre_get_posts', function($query) {
	// Only target main queries on frontend, and for archives
	if ( $query->is_feed() ) {
		return;
	}

	if (!is_admin() && $query->is_main_query() && (is_category() || is_tag() || is_post_type_archive())) {
		if ( $query->is_category( 'articles' ) ) {
			$podcasts_cat = get_category_by_slug( 'podcasts' );
			if ( $podcasts_cat ) {
				// Override the category archive constraint and exclude podcasts instead.
				$query->set( 'cat', '' );
				$query->set( 'category_name', '' );
				$tax_query = (array) $query->get( 'tax_query' );
				$tax_query[] = array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array( (int) $podcasts_cat->term_id ),
					'operator' => 'NOT IN',
				);
				$query->set( 'tax_query', $tax_query );
			}
		}
		$query->set('posts_per_page', 15);
	}
});

/**
 * No need to load jQuery
 */
function bbb_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'bbb_remove_jquery_migrate' );

/**
 * Load custom log in page assets
 */
add_action( 'login_enqueue_scripts', 'bbb_login_scripts' );
function bbb_login_scripts() {

    wp_enqueue_script(
        'bbb-scripts',
        get_stylesheet_directory_uri() . '/scripts/bbb-scripts.min.js',
        [],
        wp_get_theme()->get( 'Version' ),
        true
    );

    wp_localize_script(
        'bbb-scripts',
        'themeSettings',
        ['themeUrl' => get_stylesheet_directory_uri()]
    );
}

/**
 * Automatically load function partials
 */
$inc_path = get_template_directory() . '/inc/';
foreach ( glob( $inc_path . '*.php' ) as $file ) {
    require $file;
}

if (isset($_GET['auto_assign_captivate_episodes'])) {
	include __DIR__ . '/auto-assign-captivate-episodes.php';
	exit;
}
