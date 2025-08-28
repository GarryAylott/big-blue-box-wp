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
 * 
 * @return array Lookup table of episode numbers to post URLs
 */
function bbb_get_podcast_episode_lookup(): array {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    // Query for posts in the podcasts category
    $podcast_posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'category_name'  => 'podcasts', 
        'orderby'        => 'date',
        'order'          => 'DESC'
    ]);

    $map = [];
    foreach ($podcast_posts as $post) {
        // Try multiple potential sources for episode numbers
        $ep = null;
        
        // Method 1: ACF field (direct)
        $ep = get_field('podcast_episode_number', $post->ID);
        
        // Method 2: Try alternative ACF field names
        if (empty($ep)) {
            $possible_field_names = ['episode_number', 'episode', 'pod_episode', 'podcast_episode'];
            foreach ($possible_field_names as $field_name) {
                $ep = get_field($field_name, $post->ID);
                if (!empty($ep)) {
                    break;
                }
            }
        }
        
        // Method 3: Try post meta directly (ACF might store with prefix)
        if (empty($ep)) {
            $meta_keys = get_post_custom_keys($post->ID);
            if (is_array($meta_keys)) {
                foreach ($meta_keys as $key) {
                    if (strpos($key, 'episode') !== false || strpos($key, 'podcast') !== false) {
                        $ep = get_post_meta($post->ID, $key, true);
                        if (!empty($ep) && is_numeric($ep)) {
                            break;
                        }
                    }
                }
            }
        }
        
        // Method 4: Parse from title (e.g., "Episode 42: The Title")
        if (empty($ep)) {
            $title = $post->post_title;
            if (preg_match('/episode\s*(\d+)/i', $title, $matches)) {
                $ep = $matches[1];
            }
        }
        
        if (is_string($ep)) {
            $ep = trim($ep);
        }
        
        if (!empty($ep) && is_numeric($ep)) {
            $ep_int = (int) $ep;
            $ep_str = (string) $ep_int;
            $permalink = get_permalink($post->ID);
            $map[$ep_int] = $permalink;
            $map[$ep_str] = $permalink;
        }
    }
    
    return $map;
}

/**
 * Format podcast cell as a link if a matching post is found.
 */
function bbb_format_podcast_cell($number, array $lookup): string {
    // Try both int and string keys for maximum compatibility
    $key_int = is_numeric($number) ? (int) $number : null;
    $key_str = is_numeric($number) ? (string) ((int) $number) : (string) $number;

    if ($key_int !== null && isset($lookup[$key_int])) {
        $url = esc_url($lookup[$key_int]);
        return sprintf('<a href="%s">%s</a>', $url, esc_html($number));
    }
    if (isset($lookup[$key_str])) {
        $url = esc_url($lookup[$key_str]);
        return sprintf('<a href="%s">%s</a>', $url, esc_html($number));
    }
    
    return esc_html((string) $number);
}