<?php
/**
 * @package Big_Blue_Box
 */

get_header();
?>

<div class="hero-bg-image">
    <img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg_home.webp" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main flow-page-regions">
    <div class="wrapper">
            <?php
            $displayed_posts = array();

            $args1 = array(
                'category_name' => 'podcasts',
                'posts_per_page' => 1
            );
            $query1 = new WP_Query($args1);

            if ($query1->have_posts()) :
                while ($query1->have_posts()) : $query1->the_post(); ?>

                    <?php $bg_img_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'singlepost-feat') : ''; ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('latest-podcast-ep rounded'); ?> style="background-image: url('<?php echo esc_url($bg_img_url); ?>');">
                        <div class="latest-podcast-ep__content">
                            <div class="latest-podcast-ep__details">
                                <h6 class="section-title">
                                    <i data-lucide="mic" class="icon-bold"></i>
                                    Latest podcast episode
                                </h6>
                                <div class="latest-podcast-ep__copy">
                                    <a href="<?php the_permalink(); ?>">
                                        <h1>
                                            <?php
                                            $thetitle   = $post->post_title;
                                            $getlength  = strlen( $thetitle );
                                            $thelength  = 80;
                                            echo substr( $thetitle, 0, $thelength );
                                            if ( $getlength > $thelength ) echo "...";
                                            ?>
                                        </h1>
                                    </a>
                                    <p class="icon-text-group small">
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

                            </div>

                            <p class="latest-podcast-ep__excerpt">
                                <?php echo wp_trim_words( get_the_excerpt(), 22 ); ?>
                            </p>

                            <a href="<?php the_permalink(); ?>" class="button flex">
                                <i data-lucide="headphones" class="icon-bold"></i>
                                Listen Now
                            </a>
                        </div>
                    </article>
                <?php
                $displayed_posts[] = get_the_ID();
                endwhile;
                wp_reset_postdata();
            endif; ?>
    </div>

    <?php
        set_query_var('displayed_posts', $displayed_posts);
        get_template_part('template-parts/content', 'latest-articles');
    ?>
    
    <div class="wrapper">
        <?php get_template_part('template-parts/content', 'testimonial'); ?>
    </div>

    <div class="browse-all">
        <div class="wrapper">
            <div class="browse-all__header">
                <h3 class="section-title-heading">Latest Podcast Episodes & Articles</h3>
                <div class="view-switcher" role="group" aria-label="Filter posts by type">
                    <button class="switch-btn is-active" data-category="all" aria-pressed="true">
                        All
                    </button>
                    <button class="switch-btn" data-category="articles" aria-pressed="true">
                        <i data-lucide="newspaper"></i>
                        Articles
                    </button>
                    <button class="switch-btn" data-category="podcasts" aria-pressed="false">
                        <i data-lucide="mic"></i>
                        Podcasts
                    </button>
                </div>
            </div>
            <div class="browse-all__container">
                <div id="ajax-posts-container" class="browse-all__posts">
                    <?php
                    $displayed_posts = get_query_var('displayed_posts');

                    $args3 = array(
                        'posts_per_page' => 12,
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
                </div>

                <?php get_sidebar(); ?>

            </div>
        </div>
    </div>

    <div class="wrapper">
        <?php get_template_part('template-parts/content', 'testimonial'); ?>
    </div>

    <?php get_template_part('template-parts/content', 'review-comp-link'); ?>

    <section>
        <div class="wrapper">
            <h3 class="section-title-heading">Explore more articles</h3>
            <div class="more-articles">
                <?php
                $tags = ['big-finish', 'events', 'reading'];

                foreach ($tags as $tag) {
                    $args = array(
                        'tag' => $tag,
                        'posts_per_page' => 3,
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) : ?>
                        <div class="more-articles__column">
                            <div class="more-articles__header">
                                <h4><?php echo ucwords(str_replace('-', ' ', $tag)); ?></h4>
                                <a class="button-ghost" href="<?php echo get_tag_link(get_term_by('slug', $tag, 'post_tag')->term_id); ?>" class="tag-archive-link">View all <?php echo ucwords(str_replace('-', ' ', $tag)); ?></a>
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
                ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/content', 'rt-link-panel'); ?>

    <div class="wrapper">
        <div class="testimonials-multi-col">
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
            <?php get_template_part('template-parts/content', 'testimonial'); ?>
        </div>
    </div>

    <div class="wrapper doctors-bbb">
        <picture>
            <source srcset="<?php echo get_bloginfo('template_url') ?>/images/hero-doctors.avif" type="image/avif">
            <source srcset="<?php echo get_bloginfo('template_url') ?>/images/hero-doctors.webp" type="image/webp">
            <img src="<?php echo get_bloginfo('template_url') ?>/images/hero-doctors.png" alt="Your space-time coordinates for everything Doctor Who...">
        </picture>
        <div class="podcast-app-links">
            <h6>
                Want to listen on your favourite podcast app?
            </h6>
            <?php get_template_part('template-parts/content', 'podcast-apps-links'); ?>
        </div>
    </div>
</main>
<?php

get_footer();