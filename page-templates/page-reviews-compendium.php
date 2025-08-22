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
$spinoff_slugs = ['torchwood','sarah-jane-adventures','class','k9-and-company','war-between-land-and-sea'];
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
?>

<div class="hero-bg-image">
    <img src="<?php echo get_bloginfo('template_url') ?>/images/pagebg_compendium.webp" decoding="async" alt="" fetchpriority="high">
</div>

<main id="primary" class="site-main reviews-compendium flow">
    <div class="wrapper">
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </header>
            <?php endwhile;
        endif; ?>

        <?php if (! empty($eras)) : ?>
            <div class="reviews-compendium__toolbar">
                <label for="era-jump" class="screen-reader-text">
                    <?php esc_html_e('Select a Doctor era or Spin-Off', 'bbb'); ?>
                </label>

                <select id="era-jump" class="era-jump" aria-label="<?php esc_attr_e('Select a Doctor era or Spin-Off', 'bbb'); ?>">
                    <option value=""><?php esc_html_e('Select a Doctor era or Spin-Off', 'bbb'); ?></option>
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
                // Get the current page's last modified date
                $page = get_post();
                $last_updated = $page ? strtotime($page->post_modified) : false;
                if ($last_updated) : ?>
                    <p class="reviews-compendium__updated text-sm">
                        <?php
                        printf(
                            esc_html__('Last updated %s', 'bbb'),
                            esc_html(date_i18n(get_option('date_format'), $last_updated))
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="review-tables flow-large">
                <?php foreach ($eras as $era) :
                    $slug          = (string) ($era['slug'] ?? '');
                    $label         = (string) ($era['label'] ?? '');
                    $season_start  = (int) ($era['season_start'] ?? 1);
                    $seasons       = is_array($era['seasons'] ?? null) ? $era['seasons'] : [];
                    $season_number = $season_start;
                    ?>

                    <section id="era-<?php echo esc_attr($slug); ?>" class="reviews-era" aria-labelledby="heading-<?php echo esc_attr($slug); ?>">

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

                        <h2 id="heading-<?php echo esc_attr($slug); ?>" class="table-heading"><?php echo esc_html($label); ?></h2>

                        <div class="table-wrap" role="region" aria-labelledby="heading-<?php echo esc_attr($slug); ?>">
                            <table class="reviews-table">
                                <caption class="screen-reader-text"><?php echo esc_html($label); ?></caption>

                                <thead>
                                    <tr>
                                        <th scope="col"><?php esc_html_e('Story', 'bbb'); ?></th>
                                        <th scope="col"><?php esc_html_e('Podcast Episode', 'bbb'); ?></th>
                                        <th scope="colgroup" colspan="2"><?php esc_html_e('Scores', 'bbb'); ?></th>
                                    </tr>
                                </thead>

                                <?php foreach ($seasons as $season) :
                                    $stories = is_array($season['stories'] ?? null) ? $season['stories'] : [];
                                    if (! empty($stories)) : ?>
                                        <tbody>
                                            <tr class="season-row">
                                                <th scope="rowgroup" colspan="2">
                                                    <?php printf(esc_html__('Season %d', 'bbb'), $season_number); ?>
                                                </th>
                                                <th scope="col"><?php esc_html_e('Garry', 'bbb'); ?></th>
                                                <th scope="col"><?php esc_html_e('Adam', 'bbb'); ?></th>
                                            </tr>
                                            <?php foreach ($stories as $story) :
                                                $title  = (string) ($story['title'] ?? '');
                                                $pod    = $story['podcast'] ?? '';
                                                $gScore = $story['scores']['garry'] ?? null;
                                                $aScore = $story['scores']['adam'] ?? null;
                                                ?>
                                                <tr>
                                                    <th scope="row" class="cell--story"><?php echo esc_html($title); ?></th>
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
                <?php endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e('No compendium data found.', 'bbb'); ?></p>
            <?php endif; ?>
            </div>
    </div>
</main>

<?php
get_footer();