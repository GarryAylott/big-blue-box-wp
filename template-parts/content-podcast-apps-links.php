<?php
/**
 * Template part for displaying links to podcast apps.
 *
 * @package Big_Blue_Box
 */
?>

<ul class="podcast-app-links__links" role="list">
    <li class="app">
        <a class="button-ghost has-external-icon" href="https://podcasts.apple.com/gb/podcast/the-doctor-who-big-blue-box-podcast/id852653886?ls=1&mt=2" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-apple.svg" width="160" height="28" alt="Listen on Apple Podcasts">
        </a>
    </li>
    <li class="app">
        <a class="button-ghost has-external-icon" href="https://open.spotify.com/show/2vRtn5865vpoNNpD5wUtZS" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-spotify.svg" width="89" height="28" alt="Listen on Spotify">
        </a>
    </li>
    <li class="app">
        <a class="button-ghost has-external-icon" href="https://music.youtube.com/playlist?list=PLWuvhqwnIO8ibiK2LaMPN_LGa1Fcxk8Jq" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo get_bloginfo('template_url') ?>/images/podlinkbtn-youtubemusic.svg" width="138" height="26" alt="Listen on YouTube Music">
        </a>
    </li>
    <li class="app">
        <a class="button-ghost has-external-icon text-button" href="https://lnkfi.re/bigblueboxpodcast" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo get_bloginfo('template_url') ?>/images/icons/icon-headphones.svg" width="16" height="16" alt="More Podcast Apps">
        <span>More Podcast Apps</span>
        </a>
    </li>
</ul>