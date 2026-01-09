<?php
/**
 * @package Big_Blue_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'       => get_template_directory_uri() . '/images/pagebg_legal.webp',
    'sources'     => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.webp',
            'type'   => 'image/webp'
        ]
    ],
    'extra_image' => get_template_directory_uri() . '/images/pagebg_home.webp'
]); ?>

<main id="primary" class="site-main flow-page-regions">
    <section class="wrapper homepage-top">
        <?php
        $displayed_posts = array();

        $args1 = array(
            'category_name' => 'podcasts',
            'posts_per_page' => 1
        );
        $query1 = new WP_Query($args1);

        if ($query1->have_posts()) :
            while ($query1->have_posts()) : $query1->the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card-hero' ); ?>>
                <div class="post-card-hero__content">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <div class="img-container">
                                <?php
                                echo wp_get_attachment_image(
                                    get_post_thumbnail_id(),
                                    'latest-podcast-ep-thumb',
                                    false,
                                    [
                                        'class'  => 'post-thumb-img img-hover',
                                    ]
                                );
                                ?>
                            </div>
                        </a>
                    <?php endif; ?>

                    <div class="post-card-hero-details flow-tiny">
                        <header class="entry-header">
                            <h6 class="section-title">
                                <i data-lucide="mic" class="icon-bold icon-step--1"></i>
                                <?php esc_html_e( 'Latest podcast episode', 'bigbluebox' ); ?>
                            </h6>
                            <div class="post-card-hero__copy flow-tiny">
                                <a href="<?php the_permalink(); ?>">
                                    <h2>
                                        <?php
                                        $thetitle   = $post->post_title;
                                        $getlength  = strlen( $thetitle );
                                        $thelength  = 80;
                                        echo substr( $thetitle, 0, $thelength );
                                        if ( $getlength > $thelength ) echo "...";
                                        ?>
                                    </h2>
                                </a>
                                <p class="icon-text-group">
                                    <?php
                                    $ep_label = get_field( 'podcast_episode_number' );
                                    $ep_type = get_field( 'podcast_episode_type' );
                                    if ( is_numeric( $ep_label ) ) {
                                        echo 'Episode ' . esc_html( $ep_label );
                                    } elseif ( $ep_label === 'N/A' && ! empty( $ep_type ) ) {
                                        echo esc_html( $ep_type );
                                    } elseif ( $ep_label ) {
                                        echo esc_html( $ep_label );
                                    }
                                    ?>
                                    <span>•</span> <?php echo get_the_date( 'j M, Y' ); ?>
                                </p>
                            </div>
                        </header>
                        <p class="post-card-hero-details__excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), 22 ); ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" class="button">
                            <i data-lucide="headphones" class="icon-bold"></i>
                            <?php esc_html_e( 'Listen Now', 'bigbluebox' ); ?>
                        </a>
                    </div>
                </div>
            </article>
            <?php
            $displayed_posts[] = get_the_ID();
            endwhile;
            wp_reset_postdata();
        endif; ?>

            <?php get_template_part('template-parts/content', 'logos-marquee'); ?>

            <h6 class="section-title">
            <i data-lucide="newspaper" class="icon-bold icon-step--1"></i>
            <?php esc_html_e( 'Latest articles', 'bigbluebox' ); ?>
        </h6>

        <?php
            set_query_var('displayed_posts', $displayed_posts);
            get_template_part('template-parts/content', 'latest-articles');
        ?>
    </section>

    <section class="wrapper">
        <?php get_template_part('template-parts/content', 'testimonial'); ?>
    </section>

    <section class="browse-all">
        <div class="wrapper">
            <div class="browse-all__header">
                <h2 class="section-title-heading"><?php esc_html_e( 'Latest from The Big Blue Box', 'bigbluebox' ); ?></h2>
                <div class="view-switcher" role="group" aria-label="Filter posts by type">
                    <button class="switch-btn is-active" data-category="all" aria-pressed="true">
                        <?php esc_html_e( 'All', 'bigbluebox' ); ?>
                    </button>
                    <button class="switch-btn" data-category="non-podcasts" aria-pressed="false">
                        <i data-lucide="newspaper"></i>
                        <?php esc_html_e( 'Articles', 'bigbluebox' ); ?>
                    </button>
                    <button class="switch-btn" data-category="podcasts" aria-pressed="false">
                        <i data-lucide="mic"></i>
                        <?php esc_html_e( 'Podcasts', 'bigbluebox' ); ?>
                    </button>
                </div>
            </div>
            <div class="browse-all__container">
                <p id="ajax-posts-status" class="screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></p>
                <div id="ajax-posts-container" class="browse-all__posts" aria-describedby="ajax-posts-status">
                    <?php
                    $displayed_posts = get_query_var('displayed_posts');

                    $args3 = array(
                        'posts_per_page' => 14,
                        'post_status' => 'publish',
                        'post__not_in' => $displayed_posts,
                        'post_type' => 'post',
                        'no_found_rows' => true
                    );
                    $query3 = new WP_Query($args3);

                    if ($query3->have_posts()) :
                        while ($query3->have_posts()) : $query3->the_post(); ?>
                            <?php get_template_part('template-parts/content', 'post-cards', array('card_type' => 'browse')); ?>
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>

                    <div class="more-buttons flex-splitter">
                        <a class="button-ghost" href="<?php echo esc_url( home_url( '/category/articles/' ) ); ?>">
                            <i data-lucide="newspaper"></i>
                            <?php esc_html_e( 'All Articles', 'bigbluebox' ); ?>
                        </a>
                        <a class="button-ghost" href="<?php echo esc_url( home_url( '/category/podcasts/' ) ); ?>">
                            <i data-lucide="mic"></i>
                            <?php esc_html_e( 'All Podcasts', 'bigbluebox' ); ?>
                        </a>
                    </div>
                </div>

                <?php get_sidebar(); ?>

            </div>
        </div>
    </section>

    <section class="wrapper">
        <?php get_template_part('template-parts/content', 'testimonial'); ?>
    </section>

    <?php get_template_part('template-parts/content', 'review-comp-link'); ?>

    <section>
        <div class="wrapper">
            <h2 class="section-title-heading"><?php esc_html_e( 'More articles by subject', 'bigbluebox' ); ?></h2>
            <div class="more-articles">
                <?php
                $tags         = array( 'big-finish', 'events', 'reading' );
                $skipped_tags = array();

                foreach ( $tags as $tag ) {
                    $term = get_term_by( 'slug', $tag, 'post_tag' );

                    if ( ! $term ) {
                        $skipped_tags[] = $tag;
                        continue;
                    }
                    $args = array(
                        'tag' => $tag,
                        'posts_per_page' => 3,
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) : ?>
                        <div class="more-articles__column">
                            <div class="more-articles__header">
                                <?php $tag_label = ucwords(str_replace('-', ' ', $tag)); ?>
                                <h3><?php echo esc_html( $tag_label ); ?></h3>
                                <a class="button-ghost" href="<?php echo esc_url( get_tag_link( $term->term_id ) ); ?>" class="tag-archive-link">
                                    <?php
                                    printf(
                                        esc_html__( 'View all %s', 'bigbluebox' ),
                                        esc_html( $tag_label )
                                    );
                                    ?>
                                </a>
                            </div>
                            <ul role="list">
                            <?php while ($query->have_posts()) : $query->the_post(); ?>
                                <?php get_template_part('template-parts/content', 'post-cards', array('card_type' => 'browse')); ?>
                            <?php endwhile; ?>
                            </ul>
                        </div>
                    <?php endif;
                    // Reset Post Data
                    wp_reset_postdata();
                }

                if ( ! empty( $skipped_tags ) ) {
                    bbb_log( 'Missing post_tag terms for slugs: ' . implode( ', ', $skipped_tags ) );
                }
                ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/content', 'rt-link-panel'); ?>

    <section class="wrapper">
        <div class="testimonials-multi-col">
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
        </div>
    </section>

    <section class="wrapper doctors-bbb">
        <?php $theme_dir = esc_url( get_template_directory_uri() ); ?>
        <picture>
            <source srcset="<?php echo $theme_dir; ?>/images/hero-doctors.avif" type="image/avif">
            <source srcset="<?php echo $theme_dir; ?>/images/hero-doctors.webp" type="image/webp">
            <img src="<?php echo $theme_dir; ?>/images/hero-doctors.png" alt="<?php echo esc_attr__( 'Your space-time coordinates for everything Doctor Who...', 'bigbluebox' ); ?>">
        </picture>
        <div class="podcast-app-links">
            <h5>
                <?php esc_html_e( 'Listen now on your favourite podcast app.', 'bigbluebox' ); ?>
            </h5>
            <?php get_template_part('template-parts/content', 'podcast-apps-links'); ?>
        </div>
    </section>
</main>
<?php

get_footer();
