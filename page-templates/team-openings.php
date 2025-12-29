<?php
/**
 * Template Name: Team Openings
 * Description: The template for displaying team openings.
 *
 * @package BigBlueBox
 */

get_header();
?>

<div class="hero-bg-image">
    <picture>
        <source srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.avif" type="image/avif">
        <source srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.webp" type="image/webp">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagebg_legal.webp" decoding="async" alt="" fetchpriority="high">
    </picture>
</div>

<main id="primary" class="site-main team-openings-page">
    <div class="wrapper">
        <?php
        $team_openings = [
            [
                'title'       => __( 'General Staff Writer', 'bigbluebox' ),
                'description' => __( 'We’re after a general writer to join the team who is interested in writing a general mix of Doctor Who content. Episode reviews, opinion pieces, book reviews, merch reviews, Top 10s, etc. Basically, anything you’re passionate about within Doctor Who that you feel our audience would enjoy reading.', 'bigbluebox' ),
                'mailto'      => 'mailto:hello@bigblueboxpodcast.co.uk?subject=Product%20Designer%20Application',
            ],
            [
                'title'       => __( 'Big Finish/Audio Drama Specialist', 'bigbluebox' ),
                'description' => __( 'Do you love the Doctor Who range of audio dramas from Big Finish? Do you listen to an unhealthy amount of audio dramas, soundtracks and more from the world of Doctor Who? Have a knack for writing easily readable, concise, insightful reviews? We’re looking to expand in this area so we’d love to hear from you. ', 'bigbluebox' ),
                'mailto'      => 'mailto:hello@bigblueboxpodcast.co.uk?subject=Podcast%20Producer%20Application',
            ],
        ];
        ?>

        <section class="team-openings flow-small">
            <header class="page-title">
                <h1><?php esc_html_e( 'Join the Team', 'bigbluebox' ); ?></h1>
                <p>
                    Love Doctor Who and enjoy writing? Have a solid grasp of grammar, structure and writing for SEO? Can you commit to at least one article per month? Want to be part of a relaxed, fan-run hobby site? We’d love to hear from you.
                </p>
            </header>

            <p>
                Current openings:
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
                An important note - The Big Blue Box is a hobby project and everyone on the team is a volunteer. Anyone looking to join the team should be aware of this to avoidjhsdjhsdjhsdjhsd
            </p>
        </section>
    </div>
</main>

<?php get_footer(); ?>
