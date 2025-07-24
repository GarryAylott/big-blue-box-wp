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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16">
                                        <path d="M7.5 3c0-.813-.688-1.5-1.5-1.5A1.5 1.5 0 0 0 4.5 3v5c0 .844.656 1.5 1.5 1.5A1.5 1.5 0 0 0 7.5 8V3ZM3 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3ZM2 6.75V8c0 2.219 1.781 4 4 4 2.188 0 4-1.781 4-4V6.75a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75V8c0 2.813-2.094 5.094-4.75 5.469V14.5h1.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-4.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75h1.5v-1.031A5.502 5.502 0 0 1 .5 8V6.75A.74.74 0 0 1 1.25 6a.76.76 0 0 1 .75.75Z"/>
                                    </svg>
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
                                        if ( is_numeric( $ep_label ) ) {
                                            echo 'Episode ' . esc_html( $ep_label );
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16">
                                    <path fill="#fff" fill-rule="evenodd" d="M8 1.5A5.5 5.5 0 0 0 2.5 7v1h4v7H4a3 3 0 0 1-3-3V7a7 7 0 0 1 14 0v5a3 3 0 0 1-3 3H9.5V8h4V7A5.5 5.5 0 0 0 8 1.5Zm-3 8H2.5V12A1.5 1.5 0 0 0 4 13.5h1v-4Zm6 0h2.5V12a1.5 1.5 0 0 1-1.5 1.5h-1v-4Z" clip-rule="evenodd"/>
                                </svg>
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
            <div class="view-switcher" role="group" aria-label="Filter posts by type">
                <button class="switch-btn is-active" data-category="all" aria-pressed="true">
                    All
                </button>
                <button class="switch-btn" data-category="articles" aria-pressed="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                        <path fill="currentColor" d="M5.25 2.5a.74.74 0 0 0-.75.75v9.5c0 .281-.063.531-.156.75h9.406a.74.74 0 0 0 .75-.75v-9.5a.76.76 0 0 0-.75-.75h-8.5Zm-3 12.5C1 15 0 14 0 12.75V3.5a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75v9.25c0 .438.313.75.75.75a.74.74 0 0 0 .75-.75v-9.5C3 2.031 4 1 5.25 1h8.5C14.969 1 16 2.031 16 3.25v9.5A2.26 2.26 0 0 1 13.75 15H2.25ZM5.5 4.25a.74.74 0 0 1 .75-.75h3a.76.76 0 0 1 .75.75v2.5a.74.74 0 0 1-.75.75h-3a.722.722 0 0 1-.75-.75v-2.5Zm6.25-.75h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm-5.5 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Z"/>
                    </svg>
                    Articles
                </button>
                <button class="switch-btn" data-category="podcasts" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="none">
                        <path fill="currentColor" d="M7.5 3c0-.813-.688-1.5-1.5-1.5A1.5 1.5 0 0 0 4.5 3v5c0 .844.656 1.5 1.5 1.5A1.5 1.5 0 0 0 7.5 8V3ZM3 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3ZM2 6.75V8c0 2.219 1.781 4 4 4 2.188 0 4-1.781 4-4V6.75a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75V8c0 2.813-2.094 5.094-4.75 5.469V14.5h1.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-4.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75h1.5v-1.031A5.502 5.502 0 0 1 .5 8V6.75A.74.74 0 0 1 1.25 6a.76.76 0 0 1 .75.75Z"/>
                    </svg>
                    Podcasts
                </button>
            </div>
            <div class="browse-all__container">
                <div id="ajax-posts-container" class="browse-all__posts">
                    <?php
                    $displayed_posts = get_query_var('displayed_posts');

                    $args3 = array(
                        'posts_per_page' => 10,
                        'post_status' => 'publish',
                        'post__not_in' => $displayed_posts,
                        'post-type' => 'post'
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
            <h6 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                    <path d="M5.25 2.5a.74.74 0 0 0-.75.75v9.5c0 .281-.063.531-.156.75h9.406a.74.74 0 0 0 .75-.75v-9.5a.76.76 0 0 0-.75-.75h-8.5Zm-3 12.5C1 15 0 14 0 12.75V3.5a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75v9.25c0 .438.313.75.75.75a.74.74 0 0 0 .75-.75v-9.5C3 2.031 4 1 5.25 1h8.5C14.969 1 16 2.031 16 3.25v9.5A2.26 2.26 0 0 1 13.75 15H2.25ZM5.5 4.25a.74.74 0 0 1 .75-.75h3a.76.76 0 0 1 .75.75v2.5a.74.74 0 0 1-.75.75h-3a.722.722 0 0 1-.75-.75v-2.5Zm6.25-.75h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm-5.5 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Z"/>
                </svg>
                Explore more articles
            </h6>
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
            <h6 class="centered">
                Listen to The Big Blue Box Podcast on your favourite podcast app
            </h6>
            <?php get_template_part('template-parts/content', 'podcast-apps-links'); ?>
        </div>
    </div>
</main>
<?php

get_footer();