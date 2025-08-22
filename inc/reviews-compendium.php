<?php
/**
 * Reviews Compendium helpers.
 *
 * @package BigBlueBox
 */

defined('ABSPATH') || exit;

/**
 * Get path to the compendium JSON file.
 */
function bbb_reviews_compendium_file(): string {
    return get_stylesheet_directory() . '/data/reviews-compendium.json';
}

/**
 * Get last updated timestamp for the JSON file.
 */
function bbb_reviews_compendium_last_updated(): ?int {
    $path = bbb_reviews_compendium_file();
    return file_exists($path) ? filemtime($path) : null;
}

/**
 * Load and cache the compendium JSON data.
 *
 * @return array
 */
function bbb_get_reviews_compendium(): array {
    $path = bbb_reviews_compendium_file();
    if (! file_exists($path)) {
        return ['eras' => []];
    }

    $mtime     = (string) filemtime($path);
    $cache_key = 'bbb_reviews_compendium_' . $mtime;
    $cached    = wp_cache_get($cache_key, 'theme');

    if (false !== $cached) {
        return $cached;
    }

    $json = file_get_contents($path);
    $data = json_decode($json ?: '[]', true, 512, JSON_THROW_ON_ERROR);

    if (! is_array($data) || ! isset($data['eras'])) {
        $data = ['eras' => []];
    }

    wp_cache_set($cache_key, $data, 'theme', DAY_IN_SECONDS);
    return $data;
}

/**
 * Build a lookup of podcast episode number => permalink.
 */
function bbb_get_podcast_episode_lookup(): array {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => 'podcast_episode_number',
    ]);

    $map = [];
    foreach ($posts as $post_id) {
        $ep = get_post_meta($post_id, 'podcast_episode_number', true);
        if ($ep !== '') {
            $ep_int = (int) $ep; // normalise to integer
            $map[$ep_int] = get_permalink($post_id);
        }
    }

    return $map;
}

/**
 * Format podcast cell as a link if a matching post is found.
 */
function bbb_format_podcast_cell($number, array $lookup): string {
    $num = (int) $number; // force integer
    if (isset($lookup[$num])) {
        $url = esc_url($lookup[$num]);
        return sprintf('<a href="%s">%d</a>', $url, $num);
    }
    return (string) $num;
}