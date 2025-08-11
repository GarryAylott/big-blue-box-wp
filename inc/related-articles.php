<?php
/**
 * Related Articles Helper (waterfall strategy)
 *
 * @package bigbluebox
 */

/**
 * Get related content for the current post using a waterfall strategy.
 *
 * WATERFALL (Articles context):
 *  1) Same category = Articles AND share 2 key tags (if we have ≥2)
 *  2) Same category = Articles AND share any tag(s)
 *  3) Share a "specific" category with the post (excludes generic categories like articles/podcasts)
 *  4) Fallback: random Articles
 *
 * WATERFALL (Podcasts context):
 *  1) Podcasts older than current (same category = Podcasts)
 *  2) Share specific categories/tags (if any)
 *  3) Fallback: latest older Podcasts (basically step 1 again)
 *
 * Notes:
 * - “Specific categories” excludes generic slugs (filterable).
 * - Uses no_found_rows + exclusions for performance.
 *
 * @param int    $limit   Number of posts.
 * @param string $context 'articles' | 'podcasts'
 * @return WP_Post[] Array of posts.
 */
function bbb_get_related_articles( int $limit = 2, string $context = 'articles' ): array {
    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return [];
    }

    // Gather categories + tags for the current post.
    $terms_cats = get_the_category( $post_id ); // array of WP_Term
    $cat_ids    = array_map( fn( $t ) => (int) $t->term_id, $terms_cats );
    $cat_slugs  = array_map( fn( $t ) => $t->slug, $terms_cats );

    $tags       = wp_get_post_tags( $post_id ); // array of WP_Term
    $tag_ids    = array_map( fn( $t ) => (int) $t->term_id, $tags );

    // Identify generic categories to exclude from "specific" category logic.
    $generic_cat_slugs = apply_filters( 'bbb_related_generic_categories', [ 'articles', 'podcasts' ] );
    $specific_cat_ids  = array_map(
        fn( $t ) => (int) $t->term_id,
        array_values( array_filter( $terms_cats, fn( $t ) => ! in_array( $t->slug, $generic_cat_slugs, true ) ) )
    );

    // Resolve the ID for the Articles/Podcasts categories if they exist.
    $articles_cat_id = ( $cat = get_category_by_slug( 'articles' ) ) ? (int) $cat->term_id : 0;
    $podcasts_cat_id = ( $cat = get_category_by_slug( 'podcasts' ) ) ? (int) $cat->term_id : 0;

    // Common base for all queries.
    $base = [
        'post_type'           => 'post',
        'post__not_in'        => [ $post_id ],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ];

    $collected = [];

    $run_query = function( array $args ) use ( $base, &$collected, $limit ) {
        // Don’t fetch more if we already have enough.
        if ( count( $collected ) >= $limit ) {
            return;
        }
        $args['posts_per_page'] = max( 0, $limit - count( $collected ) );
        $q = new WP_Query( array_merge( $base, $args ) );
        if ( $q->have_posts() ) {
            $collected = array_merge( $collected, $q->posts );
        }
    };

    if ( $context === 'podcasts' && $podcasts_cat_id ) {
        // --- PODCASTS WATERFALL ---

        // 1) Older podcasts (strict)
        $current_post_date = get_the_date( 'Y-m-d H:i:s', $post_id );
        $run_query( [
            'category__in' => [ $podcasts_cat_id ],
            'orderby'      => 'date',
            'order'        => 'DESC',
            'date_query'   => [
                [
                    'before'    => $current_post_date,
                    'inclusive' => false,
                ],
            ],
        ] );

        // 2) If still short, try podcasts sharing specific cats/tags
        if ( count( $collected ) < $limit ) {
            $args = [
                'category__in' => [ $podcasts_cat_id ],
                'orderby'      => 'rand',
            ];
            if ( $specific_cat_ids ) {
                $args['category__in'] = array_unique( array_merge( [ $podcasts_cat_id ], $specific_cat_ids ) );
            }
            if ( $tag_ids ) {
                $args['tag__in'] = $tag_ids;
            }
            $run_query( $args );
        }

        // 3) Fallback: older podcasts again (acts as latest-older)
        if ( count( $collected ) < $limit ) {
            $run_query( [
                'category__in' => [ $podcasts_cat_id ],
                'orderby'      => 'date',
                'order'        => 'DESC',
            ] );
        }
    } else {
        // --- ARTICLES WATERFALL ---

        // We prefer queries constrained to the Articles category.
        $articles_constraint = $articles_cat_id ? [ $articles_cat_id ] : $cat_ids;

        // 1) Same category (Articles) + share 2 tags (if we have ≥2 tags)
        if ( count( $tag_ids ) >= 2 ) {
            // Use the first two tags as a proxy for "pair match".
            $pair = array_slice( $tag_ids, 0, 2 );
            $run_query( [
                'category__in' => $articles_constraint,
                'tag__and'     => $pair,
                'orderby'      => 'rand',
            ] );
        }

        // 2) Same category (Articles) + share any tags
        if ( count( $collected ) < $limit && $tag_ids ) {
            $run_query( [
                'category__in' => $articles_constraint,
                'tag__in'      => $tag_ids,
                'orderby'      => 'rand',
            ] );
        }

        // 3) Share a specific (non-generic) category
        if ( count( $collected ) < $limit && $specific_cat_ids ) {
            $run_query( [
                'category__in' => $specific_cat_ids,
                'orderby'      => 'rand',
            ] );
        }

        // 4) Fallback: random Articles
        if ( count( $collected ) < $limit ) {
            $run_query( [
                'category__in' => $articles_constraint,
                'orderby'      => 'rand',
            ] );
        }
    }

    // Deduplicate just in case.
    if ( count( $collected ) > 1 ) {
        $collected = array_values( array_unique( $collected, SORT_REGULAR ) );
    }

    return array_slice( $collected, 0, $limit );
}