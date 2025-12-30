<?php
/**
 * Template part for rendering hero background images.
 *
 * @package Big_Blue_Box
 */
?>

<?php
$image       = $args['image'] ?? '';
$sources     = $args['sources'] ?? [];
$extra_image = $args['extra_image'] ?? '';
$alt         = $args['alt'] ?? '';
$fetchpriority = $args['fetchpriority'] ?? 'high';
?>

<?php if ($image || $sources) : ?>
    <div class="hero-bg-image">
        <?php if (!empty($sources)) : ?>
            <picture>
                <?php foreach ($sources as $source) : ?>
                    <?php
                    $srcset = $source['srcset'] ?? '';
                    $type   = $source['type'] ?? '';
                    if (!$srcset) {
                        continue;
                    }
                    ?>
                    <source srcset="<?php echo esc_url($srcset); ?>"<?php echo $type ? ' type="' . esc_attr($type) . '"' : ''; ?>>
                <?php endforeach; ?>
                <?php if ($image) : ?>
                    <img src="<?php echo esc_url($image); ?>" decoding="async" alt="<?php echo esc_attr($alt); ?>" fetchpriority="<?php echo esc_attr($fetchpriority); ?>" loading="eager">
                <?php endif; ?>
            </picture>
        <?php elseif ($image) : ?>
            <img src="<?php echo esc_url($image); ?>" decoding="async" alt="<?php echo esc_attr($alt); ?>" fetchpriority="<?php echo esc_attr($fetchpriority); ?>" loading="eager">
        <?php endif; ?>

        <?php if ($extra_image) : ?>
            <img src="<?php echo esc_url($extra_image); ?>" decoding="async" alt="" fetchpriority="<?php echo esc_attr($fetchpriority); ?>" loading="eager">
        <?php endif; ?>
    </div>
<?php endif; ?>
