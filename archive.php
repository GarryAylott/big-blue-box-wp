<?php
/**
 * Archive pages for tags, author pages, etc.
 *
 * @package Big_Blue_Box
 */

get_header();

// 1. Image pools
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
$author_bg_map = [
    '1' => get_template_directory_uri() . '/images/author1.webp',
    '2' => get_template_directory_uri() . '/images/author2.webp',
    // Add additional author ID => bg image mappings as needed
];

// 2. Content logic
if (is_tag()) {
    $hero_heading = 'Articles and podcasts tagged ' . single_tag_title('', false);
    $hero_sub = tag_description();
    $bg_image = get_template_directory_uri() . '/images/' . $bg_pool[array_rand($bg_pool)];
} elseif (is_author()) {
    $author_id = get_query_var('author');
    $author = get_userdata($author_id);
    $bg_image = isset($author_bg_map[$author_id])
        ? $author_bg_map[$author_id]
        : get_template_directory_uri() . '/images/pagebg_author_default.webp';
} else {
    $hero_heading = get_the_archive_title();
    $hero_sub = term_description() ?: '';
    $bg_image = get_template_directory_uri() . '/images/pagebg_default.webp';
}
?>

<div class="page-bg-inline bg-image-fade">
    <img src="<?php echo esc_url($bg_image); ?>" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

        <?php if (is_author()) : ?>
            <?php
            // ACF user fields
            $profile_img_id = get_field('profile_image', 'user_' . $author_id);
            $twitter   = get_field('twitter', 'user_' . $author_id);
            $instagram = get_field('instagram', 'user_' . $author_id);
            $facebook  = get_field('facebook', 'user_' . $author_id);
            $fav_doctor = get_field('fav_doctor', 'user_' . $author_id);
            $fav_story  = get_field('fav_story', 'user_' . $author_id);

            $article_count = count_user_posts($author_id, 'post');
            $bio = get_the_author_meta('description', $author_id);
            ?>
            <section class="author-hero">
                <div class="author-hero__layout">
                    <div class="author-hero__image">
                        <?php
                        // Only show if profile image exists
                        if ($profile_img_id) {
                            echo wp_get_attachment_image($profile_img_id, 'large', false, [
                                'alt' => esc_attr($author->display_name),
                                'class' => 'author-hero__img',
                                'loading' => 'lazy'
                            ]);
                        }
                        ?>
                    </div>
                    <div class="author-hero__content">
                        <h1 class="author-hero__name"><?php echo esc_html($author->display_name); ?></h1>
                        <div class="author-hero__meta-row">
                            <div class="author-hero__socials">
                                <?php if ($twitter): ?>
                                    <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                                        <!-- Twitter SVG -->
                                        <svg viewBox="0 0 24 24" width="1.4em" height="1.4em" fill="currentColor" aria-hidden="true"><path d="M22.46 6c-.77.35-1.6.59-2.47.7a4.19 4.19 0 0 0 1.84-2.32c-.82.49-1.73.85-2.7 1.04A4.14 4.14 0 0 0 16.07 4c-2.27 0-4.1 1.83-4.1 4.08 0 .32.04.64.1.94C8 8.8 5.08 7.32 3 5.06c-.35.6-.56 1.29-.56 2.04 0 1.41.72 2.65 1.83 3.38-.67-.02-1.29-.21-1.83-.5v.05c0 1.97 1.41 3.62 3.28 4a4.19 4.19 0 0 1-1.82.07c.51 1.6 2 2.77 3.75 2.8A8.3 8.3 0 0 1 2 19.14 11.76 11.76 0 0 0 8.29 21c7.54 0 11.67-6.24 11.67-11.65 0-.18 0-.36-.01-.54A8.17 8.17 0 0 0 24 4.56c-.77.35-1.6.59-2.47.7Z"/></svg>
                                <?php endif; ?>
                                <?php if ($instagram): ?>
                                    <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                        <!-- Instagram SVG -->
                                        <svg viewBox="0 0 24 24" width="1.4em" height="1.4em" fill="currentColor" aria-hidden="true"><path d="M12 7.2A4.8 4.8 0 1 0 12 16.8 4.8 4.8 0 1 0 12 7.2zm0 7.92A3.12 3.12 0 1 1 12 8.88a3.12 3.12 0 0 1 0 6.24zm5.52-8.28a1.12 1.12 0 1 0 0 2.24 1.12 1.12 0 0 0 0-2.24zm3.16 1.14c-.07-1.46-.42-2.75-1.53-3.86C17.83 2.7 16.54 2.35 15.08 2.28 13.13 2.17 10.88 2.17 8.92 2.28c-1.46.07-2.75.42-3.86 1.53C2.7 6.17 2.35 7.46 2.28 8.92 2.17 10.87 2.17 13.13 2.28 15.08c.07 1.46.42 2.75 1.53 3.86 1.11 1.11 2.4 1.46 3.86 1.53 1.95.11 4.21.11 6.16 0 1.46-.07 2.75-.42 3.86-1.53 1.11-1.11 1.46-2.4 1.53-3.86.11-1.95.11-4.21 0-6.16zM21.6 17.57a5.31 5.31 0 0 1-1.18 1.88c-.82.82-1.8 1.21-2.87 1.28-1.93.11-4 .11-5.92 0-1.07-.07-2.05-.46-2.87-1.28a5.31 5.31 0 0 1-1.18-1.88c-.27-.64-.41-1.34-.47-2.06C2.89 13.13 2.89 10.87 2.99 8.92c.06-.72.2-1.42.47-2.06a5.31 5.31 0 0 1 1.18-1.88c.82-.82 1.8-1.21 2.87-1.28 1.93-.11 4-.11 5.92 0 1.07.07 2.05.46 2.87 1.28.51.51.92 1.15 1.18 1.88.27.64.41 1.34.47 2.06.11 1.95.11 4.21 0 6.16-.06.72-.2 1.42-.47 2.06z"/></svg>
                                <?php endif; ?>
                                <?php if ($facebook): ?>
                                    <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                        <!-- Facebook SVG -->
                                        <svg viewBox="0 0 24 24" width="1.4em" height="1.4em" fill="currentColor" aria-hidden="true"><path d="M22 12.07C22 6.63 17.51 2.07 12 2.07S2 6.63 2 12.07c0 5 3.66 9.12 8.44 9.87v-6.98h-2.54v-2.89h2.54V9.86c0-2.5 1.5-3.89 3.79-3.89 1.1 0 2.25.2 2.25.2v2.48h-1.27c-1.25 0-1.64.77-1.64 1.56v1.88h2.8l-.45 2.89h-2.35v6.98C18.34 21.19 22 17.07 22 12.07z"/></svg>
                                <?php endif; ?>
                            </div>
                            <span class="author-hero__badge">
                                <span><?php echo esc_html($article_count); ?> Articles</span>
                            </span>
                        </div>
                        <div class="author-hero__bio"><?php echo wpautop($bio); ?></div>
                        <div class="author-hero__favs">
                            <?php if ($fav_doctor): ?>
                                <div>Favourite Doctor: <strong><?php echo esc_html($fav_doctor); ?></strong></div>
                            <?php endif; ?>
                            <?php if ($fav_story): ?>
                                <div>Favourite Story: <strong><?php echo esc_html($fav_story); ?></strong></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <?php get_template_part('template-parts/content', 'archive-hero', [
                'hero_heading' => $hero_heading,
                'hero_sub'     => $hero_sub
            ]); ?>
        <?php endif; ?>

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
