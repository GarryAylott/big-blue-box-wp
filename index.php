<?php
/**
 * @package Big_Blue_Box
 */

get_header();
?>

<div class="page-bg-inline bg-image-fade">
	<img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg_home.webp" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main flow-page-regions">
    <div class="wrapper">
        <article id="post-<?php the_ID(); ?>" <?php post_class('latest-podcast-ep flex-splitter'); ?>>
            <?php
            $displayed_posts = array();

            $args1 = array(
                'category_name' => 'podcasts',
                'posts_per_page' => 1
            );
            $query1 = new WP_Query($args1);

            if ($query1->have_posts()) :
                while ($query1->have_posts()) : $query1->the_post(); ?>

                    <div class="latest-podcast-ep__content">
                        <div class="latest-podcast-ep__details">
                            <h6 class="icon-text-group clr-900">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="none">
                                <path d="M7.5 3c0-.813-.688-1.5-1.5-1.5A1.5 1.5 0 0 0 4.5 3v5c0 .844.656 1.5 1.5 1.5A1.5 1.5 0 0 0 7.5 8V3ZM3 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3ZM2 6.75V8c0 2.219 1.781 4 4 4 2.188 0 4-1.781 4-4V6.75a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75V8c0 2.813-2.094 5.094-4.75 5.469V14.5h1.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-4.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75h1.5v-1.031A5.502 5.502 0 0 1 .5 8V6.75A.74.74 0 0 1 1.25 6a.76.76 0 0 1 .75.75Z"/>
                                </svg>
                                Latest episode <span>•</span> Episode <?php the_field('episode_number'); ?> <span>•</span> <?php echo $publish_date = get_the_date('j M, Y'); ?>
                            </h6>

                            <div class="latest-podcast-ep__copy">
                                <a href="<?php the_permalink(); ?>">
                                    <h3>
                                        <?php
                                        $thetitle = $post->post_title;
                                        $getlength = strlen($thetitle);
                                        $thelength = 80;
                                        echo substr($thetitle, 0, $thelength);
                                        if ($getlength > $thelength) echo "...";
                                        ?>
                                    </h3>
                                </a>
                            </div>
                        </div>

                        <p>
                            <?php echo wp_trim_words(get_the_excerpt()); ?>
                        </p>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img class="post-thumb-img latest-podcast-ep__thumb img-hover rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>" width="595" height="335" alt="<?php echo the_title() ?>">
                        </a>
                    <?php endif; ?>

                <?php
                $displayed_posts[] = get_the_ID();
                endwhile;
                wp_reset_postdata();
            endif; ?>
        </article>
        <div class="app-links">
            <?php get_template_part('template-parts/content', 'podcast-apps-links'); ?> 
        </div>
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
            <div class="browse-all__container">
                <div class="browse-all__posts">
                    <?php
                    $displayed_posts = get_query_var('displayed_posts');

                    $args3 = array(
                        'posts_per_page' => 10,
                        'post_status' => 'publish',
                        'post__not_in' => $displayed_posts
                    );
                    $query3 = new WP_Query($args3);

                    if ($query3->have_posts()) :
                        while ($query3->have_posts()) : $query3->the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card-alt'); ?>>

                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img class="post-thumb-img img-hover rounded-small" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>" width="391" height="220" alt="<?php echo the_title() ?>">
                                    </a>
                                <?php endif; ?>

								<div class="post-card-content">
									<header class="entry-header">
                                        <?php get_template_part( 'template-parts/content', 'category-tag' ); ?>
										<a href="<?php the_permalink(); ?>">
											<h5 class="balance">
												<?php
												$thetitle = $post->post_title;
												$getlength = strlen($thetitle);
												$thelength = 55;
												echo substr($thetitle, 0, $thelength);
												if ($getlength > $thelength) echo "...";
												?>
											</h5>
										</a>
									</header>

									<footer class="entry-footer">
										<?php get_template_part( 'template-parts/content', 'author-meta' ); ?>
									</footer>
								</div>

                            </article>
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>

                    <div class="browse-all-btns flex">
                        <a class="button" href="">
                            View all podcasts
                        </a>
                        <a class="button" href="">
                            View all articles
                        </a>
                    </div>
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
            <h4 class="section-title">More articles</h4>
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
                        <h5 class="section-title-small"><?php echo ucwords(str_replace('-', ' ', $tag)); ?></h5>
                        <ul role="list">
                        <?php while ($query->have_posts()) : $query->the_post(); ?>

                            <li>
                                <div class="more-articles-content">
                                    <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img class="post-thumb-img-small img-hover rounded-xs" src="<?php echo the_post_thumbnail_url('homepage-thumb'); ?>" width="156" height="88" alt="<?php echo the_title() ?>">
                                    </a>
                                    <?php endif; ?>

                                    <div class="post-meta">
                                        <p class="small">by <?php echo get_the_author_meta('first_name'); ?></p>
                                        <p class="small"><?php the_date('j M, Y'); ?></p>
                                    </div>
                                </div>

                                <a class="more-articles-title" href="<?php the_permalink(); ?>">
                                    <h6>
                                        <?php
                                        $thetitle = $post->post_title;
                                        $getlength = strlen($thetitle);
                                        $thelength = 55;
                                        echo substr($thetitle, 0, $thelength);
                                        if ($getlength > $thelength) echo "...";
                                        ?>
                                    </h6>
                                </a>
                            </li>
                            
                        <?php endwhile; ?>
                        </ul>
                        <a class="button-ghost" href="<?php echo get_tag_link(get_term_by('slug', $tag, 'post_tag')->term_id); ?>" class="tag-archive-link">View all <?php echo ucwords(str_replace('-', ' ', $tag)); ?></a>
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
</main>
<?php

get_footer();