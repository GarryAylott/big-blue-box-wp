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
                        Adam has been into Doctor Who for as long as he can remember. He’s the walking guidebook when it comes to facts and trivia and never shy of calling out a dodgy story. Don't forget to remember to check out <a href="https://www.youtube.com/@TheGeeksHandbag" class="link-alt" target="_blank" rel="noopener" aria-label="The Geeks Handbag on YouTube">The Geeks Handbag on YouTube</a>, packed with reviews, unboxings, convention videos and more.
                    </p>
                    <ul class="social-icons" role="list">
                        <li>
                            <a href="https://www.youtube.com/@thegeekshandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="YouTube">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-yt.svg' ) ); ?>" alt="YouTube" width="29" height="28">
                            </a>
                        </li>
                        <li>
                            <a href="https://x.com/TheGeeksHandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-x.svg' ) ); ?>" alt="X (Twitter)" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/the_geeks_handbag/" class="has-external-icon" target="_blank" rel="noopener" aria-label="Instagram">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-insta.svg' ) ); ?>" alt="Instagram" width="24" height="24">
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/thegeekshandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="Facebook">
                                <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-fb.svg' ) ); ?>" alt="Facebook" width="24" height="24">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="about-page-writers flow-large">
             <div class="text-block">
                <h2>Writing Team</h2>
                <p>The blog is driven by writers who know the show inside out. Their articles range across Big Finish reviews, convention round-ups, editorials and more. Also active in our Discord, they're always on hand to chat anything Doctor Who.</p>
            </div>

            <?php
            $team_writers = [
                [
                    'user_id'   => 4,
                    'name'      => 'Maria Kalatichou',
                    'role'      => 'Writer',
                    'image'     => 'images/authors/about-writers-panel-maria.webp',
                    'image_alt' => 'Maria Kalatichou - Staff writer',
                ],
                [
                    'user_id'   => 8,
                    'name'      => 'Matt Steele',
                    'role'      => 'Writer & Merch Reporter',
                    'image'     => 'images/authors/about-writers-panel-matt.webp',
                    'image_alt' => 'Matt Steele - Staff writer and Merch Reporter',
                ],
                [
                    'user_id'   => 3,
                    'name'      => 'Jordan Shortman',
                    'role'      => 'Writer',
                    'image'     => 'images/authors/about-writers-panel-jordan.webp',
                    'image_alt' => 'Jordan Shortman - Staff writer',
                ],
                [
                    'user_id'   => 7,
                    'name'      => 'Harry Walker',
                    'role'      => 'Writer',
                    'image'     => 'images/authors/about-writers-panel-harry.webp',
                    'image_alt' => 'Harry Walker - Staff writer',
                ],
            ];

            ?>
            <div class="team-writers">
                <?php foreach ($team_writers as $writer) : ?>
                    <?php
                    $first_name = get_user_meta($writer['user_id'], 'first_name', true);
                    if (empty($first_name)) {
                        $first_name = $writer['name'];
                    }
                    $first_name = sanitize_text_field($first_name);

                    $social_links_markup = bbb_render_author_social_links(
                        $writer['user_id'],
                        [
                            'container'          => 'div',
                            'container_class'    => 'author-hero__socials',
                            'link_class'         => 'has-external-icon',
                            'link_rel'           => 'noopener',
                            'link_target'        => '_blank',
                            'icon_class'         => 'img-hover',
                            'lucide_class'       => 'icon-bold',
                            'aria_label_pattern' => esc_html__('%1$s on %2$s', 'bigbluebox'),
                            'author_name'        => sanitize_text_field($writer['name']),
                        ]
                    );
                    ?>
                    <div class="team-writers__panel">
                        <div class="team-writers__content">
                            <h5><?php echo esc_html($writer['name']); ?></h5>
                            <p class="small"><?php echo esc_html($writer['role']); ?></p>
                            <?php if (!empty($social_links_markup)) : ?>
                                <?php echo $social_links_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup escaped within helper. ?>
                            <?php endif; ?>
                            <a class="team-writers__link" href="<?php echo esc_url(get_author_posts_url($writer['user_id'])); ?>">
                                <button class="button"><?php printf(esc_html__('More about %s', 'bigbluebox'), esc_html($first_name)); ?></button>
                            </a>
                        </div>
                        <img class="team-writers__image" src="<?php echo esc_url(get_theme_file_uri($writer['image'])); ?>" alt="<?php echo esc_attr($writer['image_alt']); ?>" />
                    </div>
                <?php endforeach; ?>

                <div class="team-writers__panel">
                    <div class="team-writers__content">
                        <h5>
                            Maybe You?
                        </h5>
                        <p class="small" id="panel5-title">
                            Writers with a love for Doctor Who — we want you! We’re always on the lookout for writers who can craft great articles. If that's you and want to be part of a welcoming, creative crew, drop us a message.
                        </p>
                        <a class="button" href="mailto:hello@bigblueboxpodcast.co.uk?subject=I%27m%20interested%20in%20writing%20for%20The%20Big%20Blue%20Box!">
                            Get in Touch
                        </a>
                    </div>
                    <img class="team-writers__image" src="<?php echo esc_url(get_theme_file_uri('images/authors/about-writers-panel-maybeyou.webp')); ?>" alt="Future Big Blue Box writer"/>
                </div>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?> 
