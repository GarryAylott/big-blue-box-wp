<?php
/**
 * Archive pages for tags, author pages, etc.
 *
 * @package Big_Blue_Box
 */

get_header();

$term = get_queried_object();
$slug = $term->slug;

switch ($slug) {
    case 'podcasts':
        $hero_heading = 'Podcasts';
        $hero_sub = 'Listen to our entire podcast library plus our bonus Round Table episodes with the writing team. Looking for all of our review scores? Check out our Review Compendium.';
        break;
    case 'articles':
        $hero_heading = 'Articles';
        $hero_sub = 'All our Doctor Who writing in one place: Big Finish, book, and merch reviews, editorials, and event coverage—updated regularly by the Big Blue Box team.';
        break;
    default:
        $hero_heading = 'Category: ' . single_cat_title('', false);
        $hero_sub = category_description(); // outputs the category description if set
        break;
}

$bg_pool = [
    'pagebg_tardis-int-1.webp',
    'pagebg_tardis-int-2.webp',
    'pagebg_tardis-int-3.webp',
    'pagebg_tardis-int-4.webp',
    'pagebg_tardis-int-5.webp',
    'pagebg_tardis-int-6.webp',
    'pagebg_tardis-int-7.webp',
    'pagebg_tardis-int-8.webp'
];
$bg_image = get_template_directory_uri() . '/images/' . $bg_pool[array_rand($bg_pool)];
?>

<div class="hero-bg-image">
    <img src="<?php echo esc_url($bg_image); ?>" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

        <?php get_template_part('template-parts/content', 'archive-hero', [
            'hero_heading' => $hero_heading,
            'hero_sub'     => $hero_sub
        ]);?>

        <div class="post-cards-grid">
            <?php
            if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'post-cards', ['card_type' => 'browse']);
            endwhile;
            else :
                echo '<p>No posts found.</p>';
            endif;
            ?>
        </div>

        <?php bbb_custom_pagination(); ?>
    </div>
</main>

<?php get_footer(); ?>