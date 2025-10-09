<?php
/**
 * Template part for displaying a tag based on the post's category.
 *
 * @package Big_Blue_Box
 */
?>

<?php
    $categories = get_the_category();

    if ( empty( $categories ) ) {
        return;
    }

    $icon_svgs = array(
        'articles' => '<svg class="icon-step--1" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path><rect width="8" height="4" x="10" y="6" rx="1"></rect></svg>',
        'podcasts' => '<svg class="icon-step--1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 19v3"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><rect x="9" y="2" width="6" height="13" rx="3"></rect></svg>',
    );

    $icon_labels = array(
        'articles' => esc_html__( 'Article', 'bigbluebox' ),
        'podcasts' => esc_html__( 'Podcast', 'bigbluebox' ),
    );

    foreach ( $categories as $category ) {
        if ( isset( $icon_svgs[ $category->slug ], $icon_labels[ $category->slug ] ) ) {
            printf(
                '<div class="category-tag rounded-xs">%1$s<span>%2$s</span></div>',
                $icon_svgs[ $category->slug ],
                $icon_labels[ $category->slug ]
            );
            break;
        }
    }
?>
