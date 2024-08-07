<?php
/**
 * Template part for displaying author meta on post thumbnails and single post headers.
 *
 * @package Big_Blue_Box
 */
?>

<?php
// Get the author ID, name, and post date
$author_id = get_the_author_meta('nickname');
$author_name = get_the_author();
$post_date = get_the_date();

$author_images = array(
    'Garry' => 'author-meta-garry.webp',
    'Maria' => 'author-meta-maria.webp',
    'Jordan' => 'author-meta-jordan.webp',
    'Harry' => 'author-meta-harry.webp',
    'Matt' => 'author-meta-matt.webp',
    'Mark' => 'author-meta-mark.webp',
);

// Display the author name, image, and post date
if ( array_key_exists($author_name, $author_images) ) {
    $author_image_url = get_template_directory_uri() . '/images/authors/' . $author_images[$author_name];
} else {
    // Default author image if the author is not found in the array
    $author_image_url = get_template_directory_uri() . '/images/authors/author-meta-garry.webp';
    // ^^^^^^^ Change the above image to serve as a default image of something like the logo or the Tardis etc ^^^^^^
}
?>

<div class="author-meta">
    <img class="author-image" src="<?php echo esc_url($author_image_url); ?>" alt="<?php echo esc_attr($author_name); ?>">
    <div class="author-meta__details">
        <p class="small bold">
            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                <?php echo esc_html($author_name); ?>
            </a>
        </p>
        <p class="small">
            <?php $publish_date = get_the_date('nS F, Y'); ?>
            <?php echo $publish_date; ?>
        </p>
    </div>
</div>