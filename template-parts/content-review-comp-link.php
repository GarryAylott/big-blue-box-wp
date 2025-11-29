<?php
/**
 * Template part for displaying the full-width review compendium seciton.
 *
 * @package Big_Blue_Box
 */
?>

<?php
$compendium_images = array(
    'CompSection-BG-1',
    'CompSection-BG-2',
    'CompSection-BG-3',
    'CompSection-BG-4',
    'CompSection-BG-5',
    'CompSection-BG-6',
    'CompSection-BG-7',
    'CompSection-BG-8',
    'CompSection-BG-9',
    'CompSection-BG-10',
    'CompSection-BG-11',
    'CompSection-BG-12',
    'CompSection-BG-13',
    'CompSection-BG-14',
    'CompSection-BG-15',
    'CompSection-BG-Fugitive',
    'CompSection-BG-War',
);

$initial_index      = array_rand($compendium_images);
$initial_image_name = $compendium_images[$initial_index];
$images_base_url    = trailingslashit(get_template_directory_uri()) . 'images/compendium-link-section';
?>

<section class="review-compendium-link">
    <div
        class="review-compendium-link__visual"
        data-compendium-rotation="<?php echo esc_attr(wp_json_encode($compendium_images)); ?>"
        data-image-dir="<?php echo esc_url($images_base_url); ?>"
        data-initial-index="<?php echo esc_attr($initial_index); ?>"
        data-image-alt="<?php echo esc_attr__('Doctor Who compendium collage', 'bigbluebox'); ?>"
    >
        <picture class="review-compendium-link__image is-active">
            <source type="image/avif" srcset="<?php echo esc_url("{$images_base_url}/{$initial_image_name}.avif"); ?>">
            <source type="image/webp" srcset="<?php echo esc_url("{$images_base_url}/{$initial_image_name}.webp"); ?>">
            <img
                src="<?php echo esc_url("{$images_base_url}/{$initial_image_name}.webp"); ?>"
                width="1000"
                height="1100"
                alt="<?php echo esc_attr__('Doctor Who compendium collage', 'bigbluebox'); ?>"
                loading="lazy"
                decoding="async"
            >
        </picture>
    </div>

    <div class="wrapper">
        <div class="review-compendium-text flow-small">
            <h3>Our Reviews for Every Doctor Who Story</h3>
            <p>
                We’ve reviewed A LOT of Doctor Who, almost all of it in fact (including the spin-offs), so it can be difficult, especially in some of the podcast apps, to find what podcast episode we reviewed a particular story.
            </p>
            <p>
                Here’s our Reviews Compendium, a handy reference of all of our episodes and review scores. Happy listening.
            </p>
                <?php
                $compendium_page = get_page_by_path('reviews-compendium');
                $compendium_url  = $compendium_page ? get_permalink($compendium_page) : '#';
                ?>
                <a class="button" href="<?php echo esc_url($compendium_url); ?>">
                    View Reviews Compendium
                </a>
        </div>
    </div>
</section>
