<?php
/**
 * The template for displaying search results pages
 *
 * @package Big_Blue_Box
 */

get_header();

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
    <div class="wrapper flow-large" data-search-term="<?php echo esc_attr(get_search_query()); ?>">

        <?php if (have_posts()) : ?>
            <section class="archive-hero search-hero">
                <div class="archive-hero__group">
                    <h6 class="section-title">
                        TARDIS Data-Core Query Complete&hellip; Data retrieved.
                    </h6>
                    <h4 class="archive-hero__heading">
                        Articles and podcast episodes about:<br>
                    </h4>
                    <h2><?php echo esc_html(get_search_query()); ?></h2>
                </div>
                <form role="search" method="get" class="search-form search-hero-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <label>
                        <span class="screen-reader-text">Search for:</span>
                        <input type="search" class="search-field" placeholder="Enter your search..." value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
                    </label>
                    <button type="submit" class="button search-submit">Search</button>
                </form>
            </section>

            <div class="post-cards-grid">
                <div class="browse-all__posts">
                    <?php while (have_posts()) : the_post();
                        get_template_part('template-parts/content', 'post-cards', ['card_type' => 'browse']);
                    endwhile; ?>
                </div>
            </div>

            <?php 
            // Pass the main query to pagination
            global $wp_query;
            bbb_custom_pagination($wp_query); 
            ?>

        <?php else : ?>
            <section class="archive-hero search-hero">
                <div class="no-search-results flow-small">
                    <h6 class="section-title">
                        TARDIS Data-Core Query Complete&hellip; Data not found.
                    </h6>
                    <h4 class="archive-hero__heading">
                        The TARDIS could not locate anything
                    </h4>
                    <p>Try another search&hellip;</p>
                    <form role="search" method="get" class="search-form search-hero-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <label>
                            <span class="screen-reader-text">Search for:</span>
                            <input type="search" class="search-field" placeholder="Enter your search..." value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
                        </label>
                        <button type="submit" class="button search-submit">Search</button>
                    </form>
                    <p>Still no results? Catch up with the latest posts below or <a class="link-alt" href="<?php echo esc_url(home_url('/')); ?>">return to the homepage</a>.</p>
                </div>
                <?php get_template_part('template-parts/content', 'suggested-posts', array('header_type' => 'latest')); ?>
            </section>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>