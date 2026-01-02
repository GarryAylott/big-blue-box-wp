<?php
/**
 * Archive pages for tags, author pages, etc.
 *
 * @package Big_Blue_Box
 */

get_header();

$term = get_queried_object();
$slug = $term->slug;

$article_tabs = [
    [
        'label' => __( 'All', 'bigbluebox' ),
        'slug'  => 'non-podcasts',
    ],
    [
        'label' => __( 'Reviews', 'bigbluebox' ),
        'slug'  => 'reviews',
    ],
    [
        'label' => __( 'News', 'bigbluebox' ),
        'slug'  => 'news',
    ],
    [
        'label' => __( 'Opinions', 'bigbluebox' ),
        'slug'  => 'opinions',
    ],
    [
        'label' => __( 'Features', 'bigbluebox' ),
        'slug'  => 'features',
    ],
];

$hero_sub_html = '';

switch ($slug) {
    case 'podcasts':
    $hero_heading = esc_html__('Podcasts', 'bigbluebox');
    $hero_sub = sprintf(
        __('Listen to our entire podcast library plus our bonus Round Table episodes with the writing team. Looking for all of our review scores? Check out our %s.', 'bigbluebox'),
        '<a class="link-alt" href="' . esc_url(get_permalink(get_page_by_path('reviews-compendium'))) . '">' . esc_html__('Review Compendium', 'bigbluebox') . '</a>'
    );
    break;
    default:
        $hero_heading = esc_html__( 'Articles', 'bigbluebox' );
        $hero_sub = '';
        $active_slug = in_array( $slug, array_column( $article_tabs, 'slug' ), true )
            ? $slug
            : $article_tabs[0]['slug'];

        ob_start();
        ?>
        <div class="view-switcher" role="group" aria-label="<?php echo esc_attr__( 'Filter posts by category', 'bigbluebox' ); ?>" data-context="category">
            <?php foreach ( $article_tabs as $tab ) : ?>
                <?php $is_active = $active_slug === $tab['slug']; ?>
                <button type="button" class="switch-btn<?php echo $is_active ? ' is-active' : ''; ?>" data-category="<?php echo esc_attr( $tab['slug'] ); ?>" aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php
        $hero_sub_html = ob_get_clean();
        break;
}

$bg_image = bbb_get_random_hero_bg();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image' => $bg_image
]); ?>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

        <?php get_template_part('template-parts/content', 'archive-hero', [
            'hero_heading' => $hero_heading,
            'hero_sub'     => $hero_sub,
            'hero_sub_html' => $hero_sub_html
        ]);?>

        <p id="ajax-posts-status" class="screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></p>
        <div id="ajax-posts-container" class="post-cards-grid browse-all__posts" aria-describedby="ajax-posts-status">
            <?php
            if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'post-cards', ['card_type' => 'browse']);
            endwhile;
            else :
                echo '<p>' . esc_html__( 'No posts found.', 'bigbluebox' ) . '</p>';
            endif;
            ?>
        </div>

        <div id="ajax-posts-pagination">
            <?php bbb_custom_pagination(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
