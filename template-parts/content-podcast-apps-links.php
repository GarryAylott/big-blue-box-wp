<?php
/**
 * Template part for displaying links to podcast apps.
 *
 * @package Big_Blue_Box
 */
?>

<div class="podcast-app-links flow-small">
    <div class="podcast-app-links__buttons">
        <div class="link-column">
            <a class="button-ghost" href="https://podcasts.apple.com/gb/podcast/the-doctor-who-big-blue-box-podcast/id852653886?ls=1&mt=2" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-apple.svg" width="160" height="28" alt="Listen on Apple Podcasts">
            </a>
        </div>
        <div class="link-column">
            <a class="button-ghost" href="https://open.spotify.com/show/2vRtn5865vpoNNpD5wUtZS" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-spotify.svg" width="89" height="28" alt="Listen on Spotify">
            </a>
        </div>
        <div class="link-column">
            <a class="button-ghost" href="https://music.youtube.com/playlist?list=PLWuvhqwnIO8ibiK2LaMPN_LGa1Fcxk8Jq" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-youtubemusic.svg" width="138" height="26" alt="Listen on YouTube Music">
            </a>
        </div>
        <div class="link-column pod-links-menu">
            <details>
                <summary>
                    <a class="button-ghost pod-links-menu-link">
                        <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-more.svg" width="26" height="6" alt="More podcast listening options">
                    </a>
                </summary>
                <div class="other-pod-links-popup rounded">
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-amazonmusic.svg" width="48" height="48" alt="Listen on Amazon Music">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-goodpods.svg" width="48" height="48" alt="Listen on Goodpods">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-tunein.svg" width="48" height="48" alt="Listen on TuneIn Radio">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-pocketcasts.svg" width="48" height="48" alt="Listen on Pocket Casts">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-iheart.svg" width="48" height="48" alt="Listen on iHeart Radio">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-castro.svg" width="48" height="48" alt="Listen on Castro">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-castbox.svg" width="48" height="48" alt="Listen on Castbox">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-overcast.svg" width="48" height="48" alt="Listen on Overcast">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-podchaser.svg" width="48" height="48" alt="Listen on Podchaser">
                        </a>
                    </div>
                    <div class="other-pod-links-popup__item">
                        <a href="#" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_bloginfo('template_url') ?>/images/podcast-apps/pod-app-rss.svg" width="48" height="48" alt="Copy RSS feed">
                        </a>
                    </div>
                </div>  
            </details>
        </div>
    </div>
</div>