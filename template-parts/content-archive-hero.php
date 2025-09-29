<?php
/**
 * Template part for displaying the hero section on archive pages.
 *
 * @package Big_Blue_Box
 */
?>

<?php
$hero_heading = $args['hero_heading'] ?? 'Archive';
$hero_sub     = $args['hero_sub'] ?? '';
?>

<section class="archive-hero">
    <h6 class="section-title">
        <?php if (is_category()): ?>
            <i data-lucide="layout-list"></i>
            Category
        <?php elseif (is_tag()): ?>
            <i data-lucide="tag"></i>
            Tag
        <?php else: ?>
            Archive
        <?php endif; ?>
    </h6>
    <div class="archive-hero__group">
        <h1 class="archive-hero__heading"><?php echo wp_kses_post($hero_heading); ?></h1>
        <?php if ($hero_sub): ?>
            <p><?php echo wp_kses_post($hero_sub); ?></p>
        <?php endif; ?>
    </div>
</section>
