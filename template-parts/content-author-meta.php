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
$author_name = get_the_author_meta('display_name');

// Get avatar with custom default fallback
$avatar_args = array(
    'size' => 48,
    'default' => get_template_directory_uri() . '/images/authors/author-avatar-small-default.webp'
);
?>

<div class="author-meta">
    <?php echo get_avatar($author_id, 48, $avatar_args['default'], esc_attr($author_name), array('class' => 'author-image')); ?>
    <div class="author-meta__details">
        <p class="author-meta__author-name small">
            <?php if (is_single()) { ?>
            <a href="<?php echo get_author_posts_url( $author_id ); ?>">
                <?php echo esc_html($author_name); ?>
            </a>
            <?php } else { ?>
            <?php echo esc_html($author_name); ?>
            <?php } ?>
        </p>
        <p class="small">
            <?php echo $publish_date = get_the_date('j F, Y'); ?>
        </p>
    </div>
</div>
