<?php
/**
 * The template for displaying search results pages.
 *
 * @package Big_Blue_Box
 */

get_header();

$bg_image = bbb_get_random_hero_bg();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image' => $bg_image
]); ?>

<main id="primary" class="site-main">
    <div class="wrapper flow-large" data-search-term="<?php echo esc_attr(get_search_query()); ?>">

        <?php if (have_posts()) : ?>
            <section class="archive-hero search-hero">
                <div class="archive-hero__group">
                    <h6 class="section-title">
                        <?php echo wp_kses_post( __( 'TARDIS Data-Core Query Complete&hellip; Data retrieved.', 'bigbluebox' ) ); ?>
                    </h6>
                    <h4 class="archive-hero__heading">
                        <?php echo wp_kses_post( __( 'Articles and podcast episodes about:<br>', 'bigbluebox' ) ); ?>
                    </h4>
                    <h2><?php echo esc_html(get_search_query()); ?></h2>
                </div>
                <form role="search" method="get" class="search-form search-hero-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <label>
                        <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bigbluebox' ); ?></span>
                        <input type="search" class="search-field" placeholder="<?php echo esc_attr__( 'Enter your search...', 'bigbluebox' ); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
                    </label>
                    <button type="submit" class="button search-submit"><?php esc_html_e( 'Search', 'bigbluebox' ); ?></button>
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
                        <?php echo wp_kses_post( __( 'TARDIS Data-Core Query Complete&hellip; Data not found.', 'bigbluebox' ) ); ?>
                    </h6>
                    <h4 class="archive-hero__heading">
                        <?php esc_html_e( 'The TARDIS could not locate anything', 'bigbluebox' ); ?>
                    </h4>
                    <p><?php echo wp_kses_post( __( 'Try another search&hellip;', 'bigbluebox' ) ); ?></p>
                    <form role="search" method="get" class="search-form search-hero-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <label>
                            <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bigbluebox' ); ?></span>
                            <input type="search" class="search-field" placeholder="<?php echo esc_attr__( 'Enter your search...', 'bigbluebox' ); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
                        </label>
                        <button type="submit" class="button search-submit"><?php esc_html_e( 'Search', 'bigbluebox' ); ?></button>
                    </form>
                    <p>
                        <?php
                        printf(
                            wp_kses_post( __( 'Still no results? Catch up with the latest posts below or <a class="link-alt" href="%s">return to the homepage</a>.', 'bigbluebox' ) ),
                            esc_url( home_url( '/' ) )
                        );
                        ?>
                    </p>
                </div>
                <?php get_template_part('template-parts/content', 'suggested-posts', array('header_type' => 'latest')); ?>
            </section>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
