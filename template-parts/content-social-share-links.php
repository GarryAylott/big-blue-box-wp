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
$title   = wp_strip_all_tags( get_the_title( $post_id ) );
$url     = get_permalink( $post_id );

// Core share text (note the full stop already ends with punctuation).
$share_text = sprintf(
    /* translators: %s: Post title */
    __( '%s by The Big Blue Box Podcast.', 'bigbluebox' ),
    $title
);

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
    <h5><?php esc_html_e( 'Share', 'bigbluebox' ); ?></h5>
    <ul role="list">
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $bluesky_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'Share this post on Bluesky', 'bigbluebox' ); ?>">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-bluesky.svg" width="21" height="21" alt="">
                    <p class="small"><?php esc_html_e( 'Bluesky', 'bigbluebox' ); ?></p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'Share this post on X (Twitter)', 'bigbluebox' ); ?>">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-x.svg" width="21" height="21" alt="">
                    <p class="small"><?php esc_html_e( 'X (Twitter)', 'bigbluebox' ); ?></p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'Share this post on Facebook', 'bigbluebox' ); ?>">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-fb.svg" width="21" height="21" alt="">
                    <p class="small"><?php esc_html_e( 'Facebook', 'bigbluebox' ); ?></p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
        <li>
            <a class="has-external-icon" href="<?php echo esc_url( $threads_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'Share this post on Threads', 'bigbluebox' ); ?>">
                <div class="social-channels__item">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icons/social-icon-threads.svg" width="21" height="21" alt="">
                    <p class="small"><?php esc_html_e( 'Threads', 'bigbluebox' ); ?></p>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/images/icons/icon-arrow-up-right.svg" width="16" height="16" alt="">
            </a>
        </li>
    </ul>
</section>
