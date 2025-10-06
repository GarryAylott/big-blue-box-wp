<?php
/**
 * Template Name: About Page
 * Description: The template for displaying the About page.
 *
 * @package BigBlueBox
 */

get_header();
?>

<div class="hero-bg-image">
    <img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg_about.webp" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main">
    <div class="wrapper flow-large">

        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <header class="page-title text-block">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </header>
            <?php endwhile;
        endif; ?>

        <section class="about-page-hosts flow">
            <h2>Podcast Hosts</h2>

            <div class="about-page-hosts__hosts">
                <div class="host-card">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-garry.webp' ) ); ?>"
                        alt="<?php echo esc_attr__( 'Portrait of Garry Aylott, host of The Big Blue Box Podcast', 'bigbluebox' ); ?>" width="588" height="508" loading="lazy" decoding="async">
                    <h3>Garry Aylott</h3>
                    <p>Creator, Producer and Co-Host</p>
                    <p class="small">
                        Garry founded The Big Blue Box back in March of 2014. (In)famous for not liking McCoy at first but over time was completely won over with Remembrance of the Daleks now in his top 3. When he’s not recording, you’ll find him reading comic books, travelling, and enjoying a bloody good cup of tea.
                    </p>
                    <ul class="social-icons" role="list">
                        <li>
                            <a href="https://twitter.com/bigblueboxpod" class="has-external-icon" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-x.svg' ) ); ?>" alt="X (Twitter)" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="Instagram">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-insta.svg' ) ); ?>" alt="Instagram" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://facebook.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="Facebook">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-fb.svg' ) ); ?>" alt="Facebook" width="24" height="24">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="host-card">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-adam.webp' ) ); ?>"
                        alt="<?php echo esc_attr__( 'Portrait of Garry Aylott, host of The Big Blue Box Podcast', 'bigbluebox' ); ?>" width="588" height="508" loading="lazy" decoding="async">
                    <h3>Adam Charlton</h3>
                    <p>Co-Host</p>
                    <p class="small">
                        Adam has been into Doctor Who for as long as he can remember. He’s the walking guidebook when it comes to facts and trivia and never shy of calling out a dodgy story. Don't forget to remember to check out <a href="https://www.youtube.com/@TheGeeksHandbag" class="link-alt" target="_blank" rel="noopener" aria-label="The Geeks Handbag on YouTube">The Geeks Handbag on YouTube</a>, packed with reviews, unboxings and convention videos.
                    </p>
                    <ul class="social-icons" role="list">
                        <li>
                            <a href="https://twitter.com/bigblueboxpod" class="has-external-icon" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-yt.svg' ) ); ?>" alt="YouTube" width="29" height="28">
                            </a>
                        </li>
                        <li>
                            <a href="https://twitter.com/bigblueboxpod" class="has-external-icon" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-x.svg' ) ); ?>" alt="X (Twitter)" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="Instagram">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-insta.svg' ) ); ?>" alt="Instagram" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://facebook.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="Facebook">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-fb.svg' ) ); ?>" alt="Facebook" width="24" height="24">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="about-page-writers">
             <div class="text-block">
                <h2>Writing Team</h2>
                <p>The blog is driven by writers who know the show inside out. Their articles range across Big Finish reviews, convention round-ups, editorials and more. Also active in our Discord, they're always on hand to chat anything Doctor Who.</p>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?> 
