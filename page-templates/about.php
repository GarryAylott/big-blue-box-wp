<?php
/**
 * Template Name: About Page
 * Description: The template for displaying the About page.
 *
 * @package BigBlueBox
 */

get_header();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'   => get_template_directory_uri() . '/images/pagebg_about.jpg',
    'sources' => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_about.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_about.webp',
            'type'   => 'image/webp'
        ]
    ]
]); ?>

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
            <h2><?php esc_html_e( 'Podcast Hosts', 'bigbluebox' ); ?></h2>

            <div class="about-page-hosts__hosts">
                <div class="host-card">
                    <picture>
                        <source srcset="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-garry.avif' ) ); ?>" type="image/avif">
                        <source srcset="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-garry.webp' ) ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-garry.png' ) ); ?>"
                            alt="<?php echo esc_attr__( 'Portrait of Garry Aylott, co-host of The Big Blue Box Podcast', 'bigbluebox' ); ?>" width="588" height="508" loading="lazy" decoding="async">
                    </picture>
                    <div class="host-card__content">
                        <h3><?php esc_html_e( 'Garry Aylott', 'bigbluebox' ); ?></h3>
                        <h5><?php esc_html_e( 'Creator, Producer and Co-Host', 'bigbluebox' ); ?></h5>
                        <p>
                            <?php esc_html_e( 'Garry founded The Big Blue Box back in March of 2014. (In)famous for not liking McCoy at first but over time was completely won over with Remembrance of the Daleks now in his top 3. When he’s not recording, you’ll find him reading comic books, travelling, and enjoying a bloody good cup of tea.', 'bigbluebox' ); ?>
                        </p>
                        <ul class="social-icons" role="list">
                            <li>
                                <a href="https://twitter.com/bigblueboxpod" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'X (Twitter)', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-x.svg' ) ); ?>" alt="<?php echo esc_attr__( 'X (Twitter)', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                            <li>
                                <a href="https://instagram.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Instagram', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-insta.svg' ) ); ?>" alt="<?php echo esc_attr__( 'Instagram', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                            <li>
                                <a href="https://facebook.com/bigblueboxpodcast" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Facebook', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-fb.svg' ) ); ?>" alt="<?php echo esc_attr__( 'Facebook', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="host-card">
                    <picture>
                        <source srcset="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-adam.avif' ) ); ?>" type="image/avif">
                        <source srcset="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-adam.webp' ) ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( get_theme_file_uri( 'images/authors/host-img-adam.png' ) ); ?>"
                            alt="<?php echo esc_attr__( 'Portrait of Adam Charlton, co-host of The Big Blue Box Podcast', 'bigbluebox' ); ?>" width="588" height="508" loading="lazy" decoding="async">
                    </picture>
                    <div class="host-card__content">
                        <h3><?php esc_html_e( 'Adam Charlton', 'bigbluebox' ); ?></h3>
                        <h5><?php esc_html_e( 'Co-Host', 'bigbluebox' ); ?></h5>
                        <p>
                            <?php
                            $geeks_link = sprintf(
                                '<a href="%s" class="link-alt" target="_blank" rel="noopener" aria-label="%s">%s</a>',
                                esc_url( 'https://www.youtube.com/@TheGeeksHandbag' ),
                                esc_attr__( 'The Geeks Handbag on YouTube', 'bigbluebox' ),
                                esc_html__( 'The Geeks Handbag', 'bigbluebox' )
                            );
                            printf(
                                wp_kses_post( __( 'Adam is a life-long Doctor Who fan. Tom Baker is "his" Doctor and Adam loves classic Who along with <span>most</span> of modern Who, too. Check out Adam\'s YouTube channel %s for reviews, unboxings, convention videos and general geekery.', 'bigbluebox' ) ),
                                $geeks_link
                            );
                            ?>
                        </p>
                        <ul class="social-icons" role="list">
                            <li>
                                <a href="https://www.youtube.com/@thegeekshandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'YouTube', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-yt.svg' ) ); ?>" alt="<?php echo esc_attr__( 'YouTube', 'bigbluebox' ); ?>" width="29" height="28">
                                </a>
                            </li>
                            <li>
                                <a href="https://x.com/TheGeeksHandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'X (Twitter)', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-x.svg' ) ); ?>" alt="<?php echo esc_attr__( 'X (Twitter)', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/the_geeks_handbag/" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Instagram', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-insta.svg' ) ); ?>" alt="<?php echo esc_attr__( 'Instagram', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/thegeekshandbag" class="has-external-icon" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Facebook', 'bigbluebox' ); ?>">
                                    <img src="<?php echo esc_url( get_theme_file_uri( 'images/icons/social-icon-fb.svg' ) ); ?>" alt="<?php echo esc_attr__( 'Facebook', 'bigbluebox' ); ?>" width="24" height="24">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-page-writers flow-large">
             <div class="text-block">
                <h2><?php esc_html_e( 'Writing Team', 'bigbluebox' ); ?></h2>
                <p><?php esc_html_e( 'The blog is driven by writers who know the show inside out. Their articles range across Big Finish reviews, convention round-ups, editorials and more. Also active in our Discord, they\'re always on hand to chat anything Doctor Who.', 'bigbluebox' ); ?></p>
            </div>

            <?php
            $team_writers = [
                [
                    'user_id'   => 4,
                    'name'      => 'Maria Kalatichou',
                    'role'      => __( 'Writer', 'bigbluebox' ),
                    'image'     => 'images/authors/about-writers-panel-maria.webp',
                    'image_alt' => __( 'Maria Kalatichou - Staff writer', 'bigbluebox' ),
                ],
                [
                    'user_id'   => 8,
                    'name'      => 'Matt Steele',
                    'role'      => __( 'Writer & Merch Reporter', 'bigbluebox' ),
                    'image'     => 'images/authors/about-writers-panel-matt.webp',
                    'image_alt' => __( 'Matt Steele - Staff writer and Merch Reporter', 'bigbluebox' ),
                ],
                [
                    'user_id'   => 3,
                    'name'      => 'Jordan Shortman',
                    'role'      => __( 'Writer', 'bigbluebox' ),
                    'image'     => 'images/authors/about-writers-panel-jordan.webp',
                    'image_alt' => __( 'Jordan Shortman - Staff writer', 'bigbluebox' ),
                ],
                [
                    'user_id'   => 7,
                    'name'      => 'Harry Walker',
                    'role'      => __( 'Writer', 'bigbluebox' ),
                    'image'     => 'images/authors/about-writers-panel-harry.webp',
                    'image_alt' => __( 'Harry Walker - Staff writer', 'bigbluebox' ),
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
                            <header>
                                <h4><?php echo esc_html($writer['name']); ?></h4>
                                <p><?php echo esc_html($writer['role']); ?></p>
                            </header>
                            <?php if (!empty($social_links_markup)) : ?>
                                <?php echo $social_links_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup escaped within helper. ?>
                            <?php endif; ?>
                            <a class="team-writers__link" href="<?php echo esc_url(get_author_posts_url($writer['user_id'])); ?>">
                                <button class="button"><?php printf(esc_html__('More about %s', 'bigbluebox'), esc_html($first_name)); ?></button>
                            </a>
                        </div>
                        <img class="team-writers__image" src="<?php echo esc_url(get_theme_file_uri($writer['image'])); ?>" alt="<?php echo esc_attr($writer['image_alt']); ?>" loading="lazy" />
                    </div>
                <?php endforeach; ?>

                <div class="team-writers__panel">
                    <div class="team-writers__content">
                        <h5>
                            <?php esc_html_e( 'Maybe You?', 'bigbluebox' ); ?>
                        </h5>
                        <p class="small" id="panel5-title">
                            <?php esc_html_e( 'Writers with a love for Doctor Who — we want you! We’re always on the lookout for writers who can craft great articles. If that\'s you and want to be part of a welcoming, creative crew, see what openings we have.', 'bigbluebox' ); ?>
                        </p>
                        <a class="button" href="<?php echo esc_url( home_url( '/team-openings' ) ); ?>">
                            <?php esc_html_e( 'Team Openings', 'bigbluebox' ); ?>
                        </a>
                    </div>
                    <img class="team-writers__image" src="<?php echo esc_url(get_theme_file_uri('images/authors/about-writers-panel-maybeyou.webp')); ?>" alt="<?php echo esc_attr__( 'Future Big Blue Box writer', 'bigbluebox' ); ?>" loading="lazy" />
                </div>
            </div>

            <div class="dalektat">
                <?php $theme_dir = esc_url( get_template_directory_uri() ); ?>
                <picture>
                    <source srcset="<?php echo $theme_dir; ?>/images/DalekTat.avif" type="image/avif">
                    <source srcset="<?php echo $theme_dir; ?>/images/DalekTat.webp" type="image/webp">
                    <img src="<?php echo $theme_dir; ?>/images/DalekTat.webp" alt="<?php echo esc_attr__( 'Dalek Tat illustration', 'bigbluebox' ); ?>" loading="lazy">
                </picture>
                <div class="dalektat__content flow-small">
                    <h3><?php esc_html_e( 'Dalek Tat', 'bigbluebox' ); ?></h3>
                    <p><?php esc_html_e( 'One of our oldest team members, and certainly the one with the most attitude to boot! Dalek Tat served as our merchandise researcher and assistant when we ran a dedicated “Merch Corner” section on the podcast.', 'bigbluebox' ); ?></p>
                    <p><?php echo wp_kses_post( __( 'No one knows where Dalek Tat is these days. Wandering the cosmos? Still on the hunt for Doctor Who merchandise? Searching for a decent cup of tea? Who knows. One day, we shall meet again. Yes, one day&hellip;', 'bigbluebox' ) ); ?></p>
                </div>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?> 
