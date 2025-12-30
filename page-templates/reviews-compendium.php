<?php
/**
 * Template Name: Reviews Compendium
 * Description: Doctor/Spin-off tables with podcast episode numbers and review scores.
 *
 * @package BigBlueBox
 */

defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/reviews-compendium.php';

get_header();

$compendium     = bbb_get_reviews_compendium();
$eras           = $compendium['eras'] ?? [];
$episode_lookup = bbb_get_podcast_episode_lookup();

// Split doctors vs spin-offs
$spinoff_slugs = ['torchwood','sarah-jane-adventures','class','k9-and-company','k9','war-between-land-and-sea','animated-serials'];
$doctors  = [];
$spinoffs = [];

foreach ($eras as $era) {
    $slug = (string) ($era['slug'] ?? '');
    if (in_array($slug, $spinoff_slugs, true)) {
        $spinoffs[] = $era;
    } else {
        $doctors[] = $era;
    }
}

// Optional per-era subtexts (keyed by era slug). Empty by default; editable here or via filter.
$era_subtexts = apply_filters('bbb_reviews_era_subtexts', [
    'first-doctor' => 'Have you ever thought what it’s like to be wanderers in the Fourth Dimension?',
    'second-doctor' => 'Our lives are different to anybody else’s… There’s nobody in the universe can do what we’re doing.',
    'third-doctor' => 'Well, I’ve reversed the polarity of the neutron flow so the TARDIS should be free of the forcefield now.',
    'fourth-doctor' => 'You may be a doctor; but I’m <strong>the</strong> Doctor. The definite article, you might say.',
    'fifth-doctor' => 'That’s the trouble with regeneration. You never quite know what you’re going to get.',
    'sixth-doctor' => 'Change, my dear. And it seems not a moment too soon.',
    'seventh-doctor' => 'Somewhere there’s danger, somewhere there’s injustice, and somewhere else the tea’s getting cold.',
    'eighth-doctor' => 'I love humans. Always seeing patterns in things that aren’t there.',
    'ninth-doctor' => 'Before I go, I just want to tell you: you were fantastic. Absolutely fantastic. And do you know what? So was I!',
    'tenth-doctor' => 'People assume that time is a strict progression… it’s more like a big ball of wibbly-wobbly, timey-wimey… stuff.',
    'eleventh-doctor' => 'We’re all stories, in the end. Just make it a good one, eh?',
    'twelfth-doctor' => 'I am not a good man… I am… an idiot! With a box and a screwdriver.',
    'thirteenth-doctor' => 'I know exactly who I am. I’m the Doctor. Sorting out fair play throughout the universe.',
    'fourteenth-doctor' => 'Grandmaster of the Knowledge! … And let’s… Allons-y!',
    'fifteenth-doctor' => 'Okay. Name: ‘The Doctor’… Address: ‘That blue box over there’.'
]);
?>

<?php get_template_part('template-parts/content', 'hero-bg-image', [
    'image'   => get_template_directory_uri() . '/images/pagebg_compendium.webp',
    'sources' => [
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_compendium.avif',
            'type'   => 'image/avif'
        ],
        [
            'srcset' => get_template_directory_uri() . '/images/pagebg_compendium.webp',
            'type'   => 'image/webp'
        ]
    ]
]); ?>

<main id="primary" class="site-main reviews-compendium">
    <div class="wrapper">
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <header class="page-title">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </header>
            <?php endwhile;
        endif; ?>

        <?php if (! empty($eras)) : ?>
            <div class="reviews-compendium__toolbar">
                <label for="era-jump" class="screen-reader-text">
                    <?php esc_html_e('Select a Doctor era or Spin-Off', 'bigbluebox'); ?>
                </label>

                <select id="era-jump" class="era-jump" aria-label="<?php esc_attr_e('Select a Doctor era or Spin-Off', 'bigbluebox'); ?>">
                    <option value=""><?php esc_html_e('Select a Doctor era or Spin-Off', 'bigbluebox'); ?></option>
                    <?php
                    // Output Doctors first, then Spin-Offs, as a flat list
                    if ($doctors) :
                        foreach ($doctors as $era) : ?>
                            <option value="#era-<?php echo esc_attr($era['slug']); ?>">
                                <?php echo esc_html($era['label']); ?>
                            </option>
                        <?php endforeach;
                    endif;
                    if ($spinoffs) :
                        foreach ($spinoffs as $era) : ?>
                            <option value="#era-<?php echo esc_attr($era['slug']); ?>">
                                <?php echo esc_html($era['label']); ?>
                            </option>
                        <?php endforeach;
                    endif;
                    ?>
                </select>
                
                <?php
                // Show the compendium data's last modified time (fallback to page modified)
                $json_last_updated = function_exists('bbb_reviews_compendium_last_updated') ? bbb_reviews_compendium_last_updated() : null;
                $page             = get_post();
                $page_last        = $page ? strtotime($page->post_modified) : null;
                $last_updated     = $json_last_updated ?: $page_last;
                if ($last_updated) : ?>
                    <p class="reviews-compendium__updated text-sm">
                        <?php
                        printf(
                            esc_html__('Last updated: %s', 'bigbluebox'),
                            esc_html(date_i18n('j F, Y', $last_updated))
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="review-tables flow-large">
                <?php $era_index = 0; $printed_spinoff_heading = false; foreach ($eras as $era) :
                    $slug          = (string) ($era['slug'] ?? '');
                    $label         = (string) ($era['label'] ?? '');
                    $season_start  = (int) ($era['season_start'] ?? 1);
                    $seasons       = is_array($era['seasons'] ?? null) ? $era['seasons'] : [];
                    $season_number = $season_start;
                    ?>

                    <?php
                    // Insert a "Spin-offs" sub-header before the first spin-off table
                    if (! $printed_spinoff_heading && in_array($slug, $spinoff_slugs, true)) :
                        $printed_spinoff_heading = true; ?>
                        <h2 class="subheading"><?php esc_html_e( 'Spin-offs', 'bigbluebox' ); ?></h2>
                    <?php endif; ?>

                    <section id="era-<?php echo esc_attr($slug); ?>" class="reviews-era" aria-labelledby="heading-<?php echo esc_attr($slug); ?>">
                        <div class="era-header">
                            <div class="era-title">
                                <?php
                                // Doctor / spin-off image (with -table.webp suffix)
                                $image_path = get_template_directory() . '/images/doctor-images/' . sanitize_title($slug) . '-table.webp';
                                $image_url  = get_template_directory_uri() . '/images/doctor-images/' . sanitize_title($slug) . '-table.webp';

                                if (file_exists($image_path)) :
                                    ?>
                                    <img 
                                        src="<?php echo esc_url($image_url); ?>" 
                                        alt="" 
                                        class="era-image"
                                    />
                                <?php endif; ?>
                                <div class="era-title__text">
                                    <h2 id="heading-<?php echo esc_attr($slug); ?>" class="table-heading"><?php echo esc_html($label); ?></h2>
                                    <?php $subtext = $era_subtexts[$slug] ?? ''; if ($subtext !== '') : ?>
                                        <p class="small"><?php echo wp_kses($subtext, ['strong' => []]); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($era_index > 0) : ?>
                                <a href="#primary" class="link-action" aria-label="<?php esc_attr_e('Back to the top of the page', 'bigbluebox'); ?>">
                                    <?php esc_html_e('Back to top', 'bigbluebox'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="table-wrap" role="region" aria-labelledby="heading-<?php echo esc_attr($slug); ?>">
                            <table class="reviews-table">
                                <caption class="screen-reader-text"><?php echo esc_html($label); ?></caption>

                                <thead>
                                    <tr>
                                        <th scope="col"><?php esc_html_e('Story', 'bigbluebox'); ?></th>
                                        <th scope="col"><?php esc_html_e('Podcast Episode', 'bigbluebox'); ?></th>
                                        <th scope="colgroup" colspan="2"><?php esc_html_e('Scores', 'bigbluebox'); ?></th>
                                    </tr>
                                </thead>

                                <?php
                                // Helpers for custom season headings and inline breaks
                                $season_heading = function(string $slug, int $season_number) use ($seasons): ?string {
                                    // Use the season numbers provided in the JSON by default
                                    $display_season = $season_number;
                                    // Eighth Doctor: keep season row but leave label blank (single-season)
                                    if ($slug === 'eighth-doctor' && $season_number === 27) {
                                        return '';
                                    }
                                    if ($slug === 'fourth-doctor' && $season_number === 16) {
                                        return __('Season 16: The Key to Time', 'bigbluebox');
                                    }
                                    if ($slug === 'sixth-doctor' && $season_number === 23) {
                                        return __('Season 23: The Trial of a Timelord', 'bigbluebox');
                                    }
                                    if ($slug === 'torchwood' && $season_number === 3) {
                                        return __('Season 3: Children of Earth', 'bigbluebox');
                                    }
                                    if ($slug === 'torchwood' && $season_number === 4) {
                                        return __('Season 4: Miracle Day', 'bigbluebox');
                                    }
                                    // Second Doctor: Season 4 continues from First Doctor
                                    if ($slug === 'second-doctor' && $season_number === 4) {
                                        return __('Season 4 (Cont.)', 'bigbluebox');
                                    }
                                    if ($slug === 'fourteenth-doctor' && $display_season === 14) {
                                        return __('Doctor Who: 60th Anniversary Specials', 'bigbluebox');
                                    }
                                    // SJA: Season 1 is the New Year's special; subsequent seasons offset by one
                                    if ($slug === 'sarah-jane-adventures') {
                                        if ($season_number === 1) {
                                            return __("New Year's Special 2007", 'bigbluebox');
                                        }
                                        return sprintf(__('Season %d', 'bigbluebox'), max(1, $season_number - 1));
                                    }
                                    // Class: keep the season row but omit the default label text
                                    if ($slug === 'class' && $season_number === 1) {
                                        return '';
                                    }
                                    // K-9 and Company: keep the season row but omit the default label text
                                    if ($slug === 'k9-and-company' && $season_number === 1) {
                                        return '';
                                    }
                                    // Animated Serials: keep the season row but omit the default label text
                                    if ($slug === 'animated-serials' && $season_number === 1) {
                                        return '';
                                    }
                                    // Default label
                                    return sprintf(__('Season %d', 'bigbluebox'), $display_season);
                                };

                                $print_heading_row = function(?string $label) {
                                    if ($label === null) {
                                        return; // explicit hide
                                    }
                                    ?>
                                    <tr class="season-row">
                                        <th scope="rowgroup" colspan="2"><?php echo esc_html($label); ?></th>
                                        <th scope="col"><?php esc_html_e('Garry', 'bigbluebox'); ?></th>
                                        <th scope="col"><?php esc_html_e('Adam', 'bigbluebox'); ?></th>
                                    </tr>
                                    <?php
                                };

                                $injected_breaks = [];

                                foreach ($seasons as $season) :
                                    $stories = is_array($season['stories'] ?? null) ? $season['stories'] : [];
                                    if (! empty($stories) || $slug === 'animated-serials') : ?>
                                        <tbody>
                                            <?php
                                            // Print (or hide) the season heading for this era/season
                                            $label = $season_heading($slug, $season_number);
                                            $print_heading_row($label);

                                            foreach ($stories as $story) :
                                                $title  = (string) ($story['title'] ?? '');
                                                $pod    = $story['podcast'] ?? '';
                                                $gScore = $story['scores']['garry'] ?? null;
                                                $aScore = $story['scores']['adam'] ?? null;
                                               // Inline specials breaks for specific seasons
                                                // Tenth Doctor: Series 4 specials (insert before The Next Doctor)
                                                if ($slug === 'tenth-doctor' && $season_number === 4 && stripos($title, 'The Next Doctor') === 0 && empty($injected_breaks['tenth_s4'])) {
                                                    $print_heading_row(__('Season 4: The Specials', 'bigbluebox'));
                                                    $injected_breaks['tenth_s4'] = true;
                                                }
                                                // Eleventh Doctor: Season 7 specials (insert before The Day of the Doctor)
                                                if ($slug === 'eleventh-doctor' && $season_number === 7 && stripos($title, 'The Day of the Doctor') === 0 && empty($injected_breaks['eleventh_s7'])) {
                                                    $print_heading_row(__('Season 7: The Specials', 'bigbluebox'));
                                                    $injected_breaks['eleventh_s7'] = true;
                                                }
                                                // Thirteenth Doctor: 2022 specials (insert before Eve of the Daleks / Eye of the Daleks)
                                                if ($slug === 'thirteenth-doctor' && $season_number === 13 && (stripos($title, 'Eve of the Daleks') === 0 || stripos($title, 'Eye of the Daleks') === 0) && empty($injected_breaks['thirteenth_s13'])) {
                                                    $print_heading_row(__('Doctor Who: The Specials 2022', 'bigbluebox'));
                                                    $injected_breaks['thirteenth_s13'] = true;
                                                }

                                                // Per-story title tweaks
                                                $render_title = $title;
                                                if ($slug === 'eleventh-doctor' && $season_number === 7 && stripos($title, 'The Day of the Doctor') === 0) {
                                                    $render_title = __('The Day of the Doctor: 50th Anniversary Special', 'bigbluebox');
                                                }
                                                ?>
                                                <tr>
                                                    <th scope="row" class="cell--story"><?php echo esc_html($render_title); ?></th>
                                                    <td class="cell--pod">
                                                        <?php echo bbb_format_podcast_cell($pod, $episode_lookup); ?>
                                                    </td>
                                                    <td class="cell--score cell--garry">
                                                        <?php echo $gScore !== null ? esc_html($gScore) : '&ndash;'; ?>
                                                    </td>
                                                    <td class="cell--score cell--adam">
                                                        <?php echo $aScore !== null ? esc_html($aScore) : '&ndash;'; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    <?php endif;
                                    $season_number++;
                                endforeach; ?>
                            </table>
                        </div>
                    </section>
                <?php $era_index++; endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e('No compendium data found.', 'bigbluebox'); ?></p>
            <?php endif; ?>
            </div>
    </div>
</main>

<?php get_footer(); ?>
