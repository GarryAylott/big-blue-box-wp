<?php
/**
 * Template part for displaying suggested articles.
 *
 * @package Big_Blue_Box
 */
?>

<div class="suggested-articles">
    <h4>
        <?php
        if (has_category('Podcasts', get_the_ID())) {
            echo 'Continue listening';
        } else {
            echo 'Continue reading';
        }
        ?>
    </h4>
    <div class="articles-hori-scroll">
        <?php
            $current_post_id = get_the_ID();
            $current_post_categories = wp_get_post_categories($current_post_id);
            $current_post_tags = wp_get_post_tags($current_post_id);

            if (has_category('Podcasts', $current_post_id)) {
                $current_post_date = get_the_date('Y-m-d H:i:s', $current_post_id);

                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 10,
                    'post__not_in'   => array($current_post_id),
                    'category_name'  => 'Podcasts',
                    'orderby'        => 'date',
                    'order'          => 'DESC', // Fetch previous posts in reverse order
                    'date_query'     => array(
                        array(
                            'before'    => $current_post_date,
                            'inclusive' => false,
                        ),
                    ),
                );
            } else {
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 10,
                    'post__not_in'   => array($current_post_id),
                    'orderby'        => 'rand',
                    'category__in'   => $current_post_categories,
                    'tag__in'        => wp_list_pluck($current_post_tags, 'term_id'),
                    'ignore_sticky_posts' => 1,
                );
            }
            $query = new WP_Query($args);
        ?>
        <?php 
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <img class="post-thumb-img img-hover rounded-small" src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'homepage-thumb')); ?>" width="387" height="217" alt="<?php echo esc_attr(get_the_title()); ?>">
                        </a>
                    <?php endif; ?>

                    <div class="post-card-content">
                        <header class="entry-header">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <h5 class="balance">
                                    <?php
                                        $thetitle = esc_html(get_the_title());
                                        echo (strlen($thetitle) > 80) ? substr($thetitle, 0, 80) . "..." : $thetitle;
                                    ?>
                                </h5>
                            </a>
                        </header>

                        <div class="entry-content">
                            <p class="small">
                                <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                            </p>
                        </div>
                    </div>
                </article>
        <?php
        endwhile;
            wp_reset_postdata();
        endif; 
        ?>
    </div>
</div>