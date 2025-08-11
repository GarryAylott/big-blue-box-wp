<?php
/**
 * Insert "Article Promo Banner" into long posts in the "articles" category.
 *
 * @package bigbluebox
 */

function bbb_insert_article_promo_banners( string $content ): string {
    // Only on single posts in the main loop.
    if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $post_id = get_the_ID();

    // Gate: only “articles” category posts.
    if ( ! has_category( 'articles', $post_id ) ) {
        return $content;
    }

    // Threshold (filterable).
    $min_total_words = (int) apply_filters( 'bbb_articlepromo_min_words', 1050 );

    // Quick bail if the post is too short overall.
    $total_words = str_word_count( wp_strip_all_tags( $content ) );
    if ( $total_words < $min_total_words ) {
        return $content;
    }

    // Get a single related candidate (random for Articles; older-first for Podcasts).
    $context    = has_category( 'Podcasts', $post_id ) ? 'podcasts' : 'articles';
    $candidates = function_exists( 'bbb_get_related_articles' ) ? bbb_get_related_articles( 1, $context ) : [];

    // Optional: ensure we can test placement even if nothing matched.
    if ( empty( $candidates ) ) {
        $candidates = get_posts( [
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'orderby'        => 'rand',
            'post__not_in'   => [ $post_id ],
            'no_found_rows'  => true,
        ] );
        if ( empty( $candidates ) ) {
            error_log('bbb_insert_article_promo_banners: No candidates found.');
            return $content;
        }
    }

    // Defensive: ensure $candidates is an array of post objects
    $candidate = $candidates[0];
    if (!is_object($candidate) || !isset($candidate->ID)) {
        error_log('bbb_insert_article_promo_banners: Invalid candidate detected.');
        return $content;
    }

    // Normalize to predictable <p> … </p> structure, then split while keeping delimiters.
    $html   = wpautop( $content );
    $tokens = preg_split( '/(<\/p>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
    if ( ! is_array( $tokens ) || ! $tokens ) {
        error_log('bbb_insert_article_promo_banners: Tokenization failed.');
        return $content;
    }

    $words_so_far = 0;
    $out = [];

    // Helper: render one banner for a given post, without triggering this filter recursively.
    $render_banner = function( $post_obj ): string {
        $post_obj = get_post( $post_obj );
        if ( ! $post_obj ) {
            error_log('bbb_insert_article_promo_banners: render_banner: invalid post object.');
            return '';
        }
        global $post;
        $old_post = $post;
        $post = $post_obj;
        // Temporarily remove this filter to prevent recursion
        remove_filter('the_content', 'bbb_insert_article_promo_banners', 15);
        setup_postdata( $post_obj );
        ob_start();
        get_template_part( 'template-parts/content-article-promo-banner' );
        $html = ob_get_clean();
        wp_reset_postdata();
        $post = $old_post;
        // Re-add the filter
        add_filter('the_content', 'bbb_insert_article_promo_banners', 15);
        return is_string( $html ) ? $html : '';
    };

    // Helper: check that the next non-empty token starts with a <p (so we’re between two paragraphs).
    $next_is_paragraph = function( array $t, int $start_index ): bool {
        $count = count( $t );
        for ( $j = $start_index; $j < $count; $j++ ) {
            $candidate = trim( $t[ $j ] );
            if ( $candidate === '' ) {
                continue;
            }
            return ( $candidate[0] === '<' ) && ( stripos( $candidate, '<p' ) === 0 );
        }
        return false;
    };

    // Find the paragraph closest to halfway through the article
    $halfway = (int) floor( $total_words / 2 );
    $closest_index = null;
    $closest_diff = null;
    $count = count( $tokens );
    $word_counts = [];

    // First pass: record word count at each paragraph end
    for ( $i = 0; $i < $count; $i++ ) {
        $segment = $tokens[ $i ];
        $words_so_far += str_word_count( wp_strip_all_tags( $segment ) );
        // Only consider after closing a paragraph
        if ( stripos( $segment, '</p>' ) !== false ) {
            $word_counts[$i] = $words_so_far;
            $diff = abs( $words_so_far - $halfway );
            if ( $closest_diff === null || $diff < $closest_diff ) {
                $closest_diff = $diff;
                $closest_index = $i;
            }
        }
    }

    // Second pass: build output and insert banner at the right spot
    $out = [];
    $words_so_far = 0;
    for ( $i = 0; $i < $count; $i++ ) {
        $segment = $tokens[ $i ];
        $out[]   = $segment;
        $words_so_far += str_word_count( wp_strip_all_tags( $segment ) );

        // Insert only after the chosen paragraph, and only if next is a <p>
        if (
            $i === $closest_index
            && isset($tokens[$i + 1])
            && $next_is_paragraph( $tokens, $i + 1 )
        ) {
            $banner_html = $render_banner( $candidate );
            if ( $banner_html !== '' ) {
                $out[] = $banner_html;
            } else {
                error_log('bbb_insert_article_promo_banners: render_banner returned empty.');
            }
        }
    }

    return implode( '', $out );
}
add_filter('the_content', 'bbb_insert_article_promo_banners', 15);