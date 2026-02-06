<?php
/**
 * Template Name: Team Openings
 * Description: The template for displaying team openings.
 *
 * @package BigBlueBox
 */

get_header();
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'   => get_template_directory_uri() . '/images/pagebg_legal.webp',
    'sources' => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_legal.webp',
            'type'   => 'image/webp'
        ]
    ]
]); ?>

<main id="primary" class="site-main team-openings-page">
    <div class="wrapper">
        <?php
        $team_openings = [
            [
                'title'       => __( 'General Staff Writer', 'bigbluebox' ),
                'description' => __( 'We’re after a general writer to join the team who is interested in writing a general mix of Doctor Who content. Episode reviews, opinion pieces, book reviews, merch reviews, Top 10s, etc. Basically, anything you’re passionate about within Doctor Who that you feel our audience would enjoy reading.', 'bigbluebox' ),
                'mailto'      => 'mailto:hello@bigblueboxpodcast.co.uk?subject=Staff%20Writer%20Application',
            ],
            [
                'title'       => __( 'Big Finish/Audio Drama Specialist', 'bigbluebox' ),
                'description' => __( 'Do you love the Doctor Who range of audio dramas from Big Finish? Do you listen to an unhealthy amount of audio dramas, soundtracks and more from the world of Doctor Who? Have a knack for writing easily readable, concise, insightful reviews? We’re looking to expand in this area so we’d love to hear from you. ', 'bigbluebox' ),
                'mailto'      => 'mailto:hello@bigblueboxpodcast.co.uk?subject=Audio%20Drama%20Specialist%20Application',
            ],
        ];
        ?>

        <section class="team-openings flow-small">
            <header class="page-title">
                <h1><?php esc_html_e( 'Join the Team', 'bigbluebox' ); ?></h1>
                <p>
                    <?php esc_html_e( 'Love Doctor Who and enjoy writing? Have a solid grasp of grammar, structure and writing for SEO? Can you commit to at least one article per month? Want to be part of a relaxed, fan-run hobby site? We’d love to hear from you.', 'bigbluebox' ); ?>
                </p>
                <p>
                    <?php esc_html_e( 'When getting in touch, please provide links to articles you\'ve published in the last year.', 'bigbluebox' ); ?>
                </p>
            </header>

            <p>
                <?php esc_html_e( 'Current openings:', 'bigbluebox' ); ?>
            </p>

            <div class="team-openings__list">
                <?php foreach ( $team_openings as $opening ) : ?>
                    <article class="team-openings__panel call-out-panel flow-tiny">
                        <h2 class="team-openings__title"><?php echo esc_html( $opening['title'] ); ?></h2>
                        <p class="small"><?php echo esc_html( $opening['description'] ); ?></p>
                        <a class="link-alt team-openings__link" href="<?php echo esc_url( $opening['mailto'] ); ?>">
                            <?php esc_html_e( 'Get in touch', 'bigbluebox' ); ?>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <p class="small">
                <?php esc_html_e( 'A heads up, The Big Blue Box is a hobby project and everyone on the team is a volunteer.', 'bigbluebox' ); ?>
            </p>
        </section>
    </div>
</main>

<?php get_footer(); ?>
