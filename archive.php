<?php
/**
 * Archive pages for tags, author pages, etc.
 *
 * @package Big_Blue_Box
 */

get_header();

// 1. Image pools
$author_bg_map = [
    '7' => get_template_directory_uri() . '/images/authors/pagebg_author-harry.webp',
    '3' => get_template_directory_uri() . '/images/authors/pagebg_author-jordan.webp',
    '4' => get_template_directory_uri() . '/images/authors/pagebg_author-maria.webp',
    '8' => get_template_directory_uri() . '/images/authors/pagebg_author-matt.webp',
    '1' => get_template_directory_uri() . '/images/authors/pagebg_author-garry.webp'
];

// 2. Content logic
if (is_tag()) {
    $hero_heading = sprintf(
        wp_kses_post( __( 'Articles and podcasts tagged:<br /><span>%s</span>', 'bigbluebox' ) ),
        esc_html( single_tag_title( '', false ) )
    );
    $hero_sub = tag_description();
    $bg_image = bbb_get_random_hero_bg();
} elseif (is_author()) {
    $author_id = get_query_var('author');
    $author = get_userdata($author_id);
    $bg_image = isset($author_bg_map[$author_id])
        ? $author_bg_map[$author_id]
        : get_template_directory_uri() . '/images/pagebg_tardis-int-2.webp';
} else {
    $hero_heading = get_the_archive_title();
    $hero_sub = term_description() ?: '';
    $bg_image = get_template_directory_uri() . '/images/pagebg_default.webp';
}
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image' => $bg_image
]); ?>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

        <?php if (is_author()) : ?>
            <?php
            // ACF user fields
            $profile_img_id = get_field('profile_image', 'user_' . $author_id);
            $fav_doctor = get_field('fav_doctor', 'user_' . $author_id);
            $fav_story  = get_field('fav_story', 'user_' . $author_id);
            $fav_doctor_image = get_field('fav_doctor_image', 'user_' . $author_id);
            $fav_story_image = get_field('fav_story_image', 'user_' . $author_id);

            $article_count = count_user_posts($author_id, 'post');
            $bio = get_the_author_meta('description', $author_id);
            $acf_bio = get_field('bio', 'user_' . $author_id);
            $doctor_width = 0;
            $doctor_height = 0;
            $story_width = 0;
            $story_height = 0;

            $social_links_markup = bbb_render_author_social_links(
                $author_id,
                [
                    'container'          => 'div',
                    'container_class'    => 'author-hero__socials',
                    'link_class'         => 'has-external-icon',
                    'link_rel'           => 'noopener',
                    'link_target'        => '_blank',
                    'icon_class'         => 'img-hover',
                    'lucide_class'       => 'icon-bold',
                    'aria_label_pattern' => esc_html__('%1$s on %2$s', 'bigbluebox'),
                    'author_name'        => sanitize_text_field($author->display_name),
                ]
            );
            ?>
            <section class="author-hero">
                <div class="author-hero__container<?php echo bbb_is_legacy_author( $author_id ) ? ' author-hero__container--legacy' : ''; ?>">
                    <?php if ( ! bbb_is_legacy_author( $author_id ) ) : ?>
                    <div class="author-hero__image">
                        <?php if ($profile_img_id): ?>
                            <?php
                            echo wp_get_attachment_image($profile_img_id, 'large', false, [
                                'alt' => esc_attr($author->display_name),
                                'class' => 'author-img rounded-small',
                                'loading' => 'lazy'
                            ]);
                            ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="author-hero__content flow-small">
                        <h1 class="author-hero__name"><?php echo esc_html($author->display_name); ?></h1>
                        <?php if ( ! bbb_is_legacy_author( $author_id ) && ! empty( $social_links_markup ) ) : ?>
                            <?php echo $social_links_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup escaped within helper. ?>
                        <?php endif; ?>
                        <span class="author-hero__badge">
                            <span>
                                <?php
                                printf(
                                    esc_html__( '%s Articles', 'bigbluebox' ),
                                    esc_html( $article_count )
                                );
                                ?>
                            </span>
                        </span>
                        <div class="author-hero__bio"><?php echo wpautop( wp_kses_post( $acf_bio ) ); ?></div>
                        <?php if ( ! bbb_is_legacy_author( $author_id ) ) : ?>
                        <div class="author-hero__favs">
                            <?php
                            if ($fav_doctor_image) {
                                $doctor_width = round($fav_doctor_image['width'] / 2);
                                $doctor_height = round($fav_doctor_image['height'] / 2);
                            }

                            if ($fav_story_image) {
                                $story_width = round($fav_story_image['width'] / 2);
                                $story_height = round($fav_story_image['height'] / 2);
                            }
                            ?>

                            <?php if ($fav_doctor): ?>
                                <div class="fav-doctor" style="--image-width: <?php echo esc_attr( absint( $doctor_width ) ); ?>px;">
                                    <div class="fav-image">
                                        <?php if (!empty($fav_doctor_image['url'])): ?>
                                            <img
                                                src="<?php echo esc_url($fav_doctor_image['url']); ?>"
                                                alt="<?php echo esc_attr($fav_doctor_image['alt']); ?>"
                                                class="fav-doctor-image"
                                                width="<?php echo esc_attr( absint( $doctor_width ) ); ?>"
                                                height="<?php echo esc_attr( absint( $doctor_height ) ); ?>"
                                                loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                    <h6>
                                        <?php
                                        printf(
                                            wp_kses_post( __( 'Fav Doctor: <strong>%s</strong>', 'bigbluebox' ) ),
                                            esc_html( $fav_doctor )
                                        );
                                        ?>
                                    </h6>
                                </div>
                            <?php endif; ?>

                            <?php if ($fav_story): ?>
                                <div class="fav-story" style="--image-width: <?php echo esc_attr( absint( $story_width ) ); ?>px;">
                                    <div class="fav-image">
                                        <?php if (!empty($fav_story_image['url'])): ?>
                                            <img
                                                src="<?php echo esc_url($fav_story_image['url']); ?>"
                                                alt="<?php echo esc_attr($fav_story_image['alt']); ?>"
                                                class="fav-story-image"
                                                width="<?php echo esc_attr( absint( $story_width ) ); ?>"
                                                height="<?php echo esc_attr( absint( $story_height ) ); ?>"
                                                loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                    <h6>
                                        <?php
                                        printf(
                                            wp_kses_post( __( 'Fav Story: <strong>%s</strong>', 'bigbluebox' ) ),
                                            esc_html( $fav_story )
                                        );
                                        ?>
                                    </h6>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <?php get_template_part('template-parts/content', 'archive-hero', [
                'hero_heading' => $hero_heading,
                'hero_sub'     => $hero_sub
            ]); ?>
        <?php endif; ?>

        <?php
        if (is_author()) {
            $author_id = get_query_var('author');
            $author = get_userdata($author_id);
            $author_first_name = $author ? $author->first_name : '';
            if (!$author_first_name) {
                // Fallback to display name if first name is not set
                $author_first_name = $author ? $author->display_name : '';
            }
            $is_garry = strtolower($author_first_name) === 'garry' || strtolower($author->display_name) === 'garry';
            if ($is_garry) {
                $articles_heading = sprintf(
                    esc_html__( "%s's Podcast Episodes and Articles", 'bigbluebox' ),
                    esc_html( $author_first_name )
                );
            } else {
                $articles_heading = sprintf(
                    esc_html__( "%s's Articles", 'bigbluebox' ),
                    esc_html( $author_first_name )
                );
            }
        } else {
            $articles_heading = "";
        }
        ?>
        <h4><?php echo $articles_heading; ?></h4>

        <div class="post-cards-grid">
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

        <?php bbb_custom_pagination(); ?>
    </div>
</main>

<?php get_footer(); ?>
