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

    foreach ( $categories as $category ) {
        if ( 'articles' === $category->slug ) {
            printf(
                '<div class="category-tag rounded-xs"><i data-lucide="newspaper" class="icon-step--1"></i><span>%s</span></div>',
                esc_html__( 'Article', 'bigbluebox' )
            );
            break;
        }

        if ( 'podcasts' === $category->slug ) {
            printf(
                '<div class="category-tag rounded-xs"><i data-lucide="mic" class="icon-step--1"></i><span>%s</span></div>',
                esc_html__( 'Podcast', 'bigbluebox' )
            );
            break;
        }
    }
?>
