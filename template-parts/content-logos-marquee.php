<?php
/**
 * Template part for displaying a side-scrolling marquee of logos.
 *
 * @package Big_Blue_Box
 */
?>

<?php
function bbb_theme_image_url( string $path ): string {
    return esc_url(
        get_template_directory_uri() . '/images/' . ltrim( $path, '/' )
    );
}
?>
<section class="brand-logos-marquee" aria-labelledby="<?php echo esc_attr__( 'Featured brands from the world of Doctor Who', 'bigbluebox' ); ?>">
    <div class="brand-logos-marquee__group">
        <picture class="brand-logo-1">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-dw.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-dw.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-dw.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'Doctor Who', 'bigbluebox' ); ?>">
        </picture>
        <picture class="brand-logo-3">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-bf.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-bf.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-bf.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'Big Finish', 'bigbluebox' ); ?>">
        </picture>
        <picture class="brand-logo-4">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-sja.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-sja.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-sja.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'The Sarah Jane Adventures', 'bigbluebox' ); ?>">
        </picture>
        <picture class="brand-logo-5">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-twb.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-twb.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-twb.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'The War Between the Land and the Sea', 'bigbluebox' ); ?>">
        </picture>
        <picture class="brand-logo-2">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-tw.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-tw.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-tw.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'Torchwood', 'bigbluebox' ); ?>">
        </picture>
        <picture class="brand-logo-6">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-cl.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-cl.webp' ); ?>" type="image/webp">
            <img src="<?php echo bbb_theme_image_url( 'brand-logos/brand-logo-cl.webp' ); ?>" loading="eager" alt="<?php echo esc_attr__( 'Class', 'bigbluebox' ); ?>">
        </picture>
    </div>
</section>
