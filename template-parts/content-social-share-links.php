<?php
/**
 * Template part for displaying a list of social media links that share articles.
 *
 * @package Big_Blue_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! is_singular() ) {
    return;
}

global $post;
if ( ! $post ) {
    return;
}

$post_id = $post->ID;
$title   = get_the_title( $post_id );
$url     = get_permalink( $post_id );

// Core share text (note the full stop already ends with punctuation).
$share_text = $title . ' by The Big Blue Box Podcast.';

// Bluesky — ensure space + double newline + URL
$bluesky_text = $share_text . " \n\n" . $url;
$bluesky_url  = 'https://bsky.app/intent/compose?text=' . rawurlencode( $bluesky_text );

// X (Twitter) — text only, URL separate
$twitter_url = 'https://x.com/intent/tweet?text=' . rawurlencode( $share_text )
             . '&url=' . rawurlencode( $url );

// Facebook
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url );

// Threads
$threads_url = 'https://www.threads.net/intent/post?text=' . rawurlencode( $share_text )
             . '&url=' . rawurlencode( $url );
?>

<section class="social-channels flow">
    <h5>Share</h5>
    <ul role="list">
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $bluesky_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share this post on Bluesky">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="">
                    <p class="small">Bluesky</p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share this post on X (Twitter)">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="">
                    <p class="small">X (Twitter)</p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share this post on Facebook">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="">
                    <p class="small">Facebook</p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $threads_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share this post on Threads">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="">
                    <p class="small">Threads</p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
    </ul>
</section>