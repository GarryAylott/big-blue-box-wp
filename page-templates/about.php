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

        <section class="about-page-hosts flow-large">
            <div class="text-block">
                <h2>Podcast Hosts</h2>
                <p>Garry launched The Big Blue Box in March 2014. In October that year Adam joined, and for more than a decade they’ve been creating Doctor Who content together, sharing opinions, theories and <a href="<?php echo esc_url( home_url( '/reviews-compendium' ) ); ?>" class="link-alt">putting a score on just about every Doctor Who story and it's spin-offs.</a></p>
            </div>

            <div class="about-page-hosts__hosts">
                <div class="host-card">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-garry.webp' ) ); ?>"
                        alt="<?php echo esc_attr__( 'Portrait of Garry Aylott, host of The Big Blue Box Podcast', 'bigbluebox' ); ?>" width="588" height="508" loading="lazy" decoding="async">
                    <h3>Garry Aylott</h3>
                    <p>Creator, Producer and Co-Host</p>
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

        <section class="about-page-writers flow-large">
             <div class="text-block">
                <h2>Your Writers</h2>
                <p>Behind The Big Blue Box’s engaging blog content lies a hugely talented team of writers, passionate about all things Doctor Who. With their collective expertise and love for the show, our writers publish fresh and exciting content regularly, ensuring there's always something new for fans to sink their teeth into. From Big Finish audio reviews, in-depth event recaps to thought-provoking editorials and everything in between, our team covers a wide range of Doctor Who content.</p>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?> 
