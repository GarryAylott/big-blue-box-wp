<?php
/**
 * Template part for displaying author meta on post thumbnails and single post headers.
 *
 * @package Big_Blue_Box
 */
?>

<?php
// Get the author ID, name, and post date
$author_id = get_the_author_meta('ID');
$author_name = get_the_author_meta('display_name'); // Using display name to match WordPress display settings

// Define the author images array with display names that match WordPress settings
$author_images = array(
    'Garry' => 'author-avatar-small-garry.webp',
    'Maria Kalotichou' => 'author-avatar-small-maria.webp',
    'Jordan Shortman' => 'author-avatar-small-jordan.webp',
    'Harry Walker' => 'author-avatar-small-harry.webp',
    'Matt Steele' => 'author-avatar-small-matt.webp'
);

// Display the author name, image, and post date
if ( array_key_exists($author_name, $author_images) ) {
    $author_image_url = get_template_directory_uri() . '/images/authors/' . $author_images[$author_name];
} else {
    // Default author image if the author is not found in the array
    $author_image_url = get_template_directory_uri() . '/images/authors/author-avatar-small-default.webp';
}

// Display the author name, image, and post date
if ( array_key_exists($author_name, $author_images) ) {
    $author_image_url = get_template_directory_uri() . '/images/authors/' . $author_images[$author_name];
} else {
    // Default author image if the author is not found in the array
    $author_image_url = get_template_directory_uri() . '/images/authors/author-avatar-small-default.webp';
}
?>

<?php
// Use a generic argument for future-proofing: link_author_name (default: is_single())
$link_author_name = $args['link_author_name'] ?? is_single();
?>
<div class="author-meta">
    <img class="author-image" src="<?php echo esc_url($author_image_url); ?>" width="48" height="70" alt="<?php echo esc_attr($author_name); ?>">
    <div class="author-meta__details">
        <p class="author-meta__author-name small">
            <?php if ( $link_author_name ) : ?>
                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
                    <?php echo esc_html( $author_name ); ?>
                </a>
            <?php else : ?>
                <?php echo esc_html( $author_name ); ?>
            <?php endif; ?>
        </p>
        <p class="small">
            <?php echo get_the_date('j F, Y'); ?>
        </p>
    </div>
</div>
