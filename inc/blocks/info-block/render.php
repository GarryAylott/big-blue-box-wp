<?php
/**
 * Info Block - Server-side rendering.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 * @return string  Rendered block HTML.
 */

if ( ! isset( $attributes['content'] ) || empty( trim( $attributes['content'] ) ) ) {
    return '';
}

$content = wp_kses_post( $attributes['content'] );
?>

<div class="info-block">
    <i data-lucide="info" class="info-block__icon icon-step-2 icon-brand-primary"></i>
    <div class="info-block__content">
        <?php echo $content; ?>
    </div>
</div>
