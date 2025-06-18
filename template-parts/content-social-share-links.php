<?php
/**
 * Template part for displaying a list of social media links that share articles.
 *
 * @package Big_Blue_Box
 */
?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Only output on singular posts.
if ( ! is_singular() ) {
    return;
}

global $post;
if ( ! $post ) {
    return;
}

$post_id   = $post->ID;
$title     = get_the_title( $post_id );
$url       = get_permalink( $post_id );
$thumb_url = '';

if ( has_post_thumbnail( $post_id ) ) {
    $thumb_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
}

// Build share text in order: title, URL, thumbnail (if available).
$share_text = $title . "\n" . $url . "\n" . ( $thumb_url ? $thumb_url : '' );

// X/Twitter share URL using web intents.
$twitter_url = 'https://twitter.com/intent/tweet?text=' . urlencode( $share_text );

// Threads share URL.
$threads_url = 'https://www.threads.net/create?text=' . urlencode( $share_text );

// Bluesky share URL.
$bluesky_url = 'https://bsky.app/feed/compose?text=' . urlencode( $share_text );

// Facebook share URL; Facebook scrapes meta data from the URL.
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $url ) . '&quote=' . urlencode( $share_text );
?>

<section class="social-channels flow">
    <h5>Share</h5>
    <ul role="list">
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $bluesky_url ); ?>" target="_blank" rel="noopener noreferrer">
                <div class="social-channels__item">
                    <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="X Bluesky link">
                    <p class="small">Bluesky</p>
                </div>
                <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer">
                <div class="social-channels__item">
                    <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="X Twitter link">
                    <p class="small">X (Twitter)</p>
                </div>
                <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">
                <div class="social-channels__item">
                    <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="Facebook link">
                    <p class="small">Facebook</p>
                </div>
                <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $threads_url ); ?>" target="_blank" rel="noopener noreferrer">
                <div class="social-channels__item">
                    <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="Threads link">
                    <p class="small">Threads</p>
                </div>
                <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
    </ul>
</section>