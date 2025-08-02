<?php
/**
 * Enqueuing all scripts and styles.
 *
 * @package Big_Blue_Box
 */

function bbb_custom_pagination($query = null) {
    global $wp_query;
    $query = $query ?? $wp_query;

    $total = intval($query->max_num_pages);
    if ($total <= 1) return;

    $current = max(1, get_query_var('paged'));
    $range = 1; // Only 1 number each side

    echo '<nav class="pagination" role="navigation" aria-label="Pagination"><ul class="pagination__list">';

    // Previous
    if ($current > 1) {
        echo '<li><a class="pagination-item pagination__prev" href="' . esc_url(get_pagenum_link($current - 1)) . '" aria-label="Previous Page">
            <span class="pagination__text">Previous Page</span>
            <span class="pagination__icon" aria-hidden="true">&lt;</span>
        </a></li>';
    }

    // First page
    if ($current == 1) {
        echo '<li><span class="pagination-item pagination__num is-current">1</span></li>';
    } else {
        echo '<li><a class="pagination-item pagination__num" href="' . esc_url(get_pagenum_link(1)) . '">1</a></li>';
    }

    // Dots before
    if ($current - $range > 2) {
        echo '<li><span class="pagination-item pagination__dots">…</span></li>';
    }

    // Main range (just 1 neighbor)
    for ($i = max(2, $current - $range); $i <= min($total - 1, $current + $range); $i++) {
        // Hide numbers that are not current or neighbor on mobile
        $mobile_hide = ($i != $current && abs($i - $current) > 0) ? ' mobile-hide' : '';
        if ($i == $current) {
            echo '<li><span class="pagination-item pagination__num is-current' . $mobile_hide . '">' . $i . '</span></li>';
        } else {
            echo '<li><a class="pagination-item pagination__num' . $mobile_hide . '" href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
        }
    }

    // Dots after
    if ($current + $range < $total - 1) {
        echo '<li><span class="pagination-item pagination__dots">…</span></li>';
    }

    // Last page
    if ($total > 1) {
        if ($current == $total) {
            echo '<li><span class="pagination-item pagination__num is-current">' . $total . '</span></li>';
        } else {
            echo '<li><a class="pagination-item pagination__num" href="' . esc_url(get_pagenum_link($total)) . '">' . $total . '</a></li>';
        }
    }

    // Next
    if ($current < $total) {
        echo '<li><a class="pagination-item pagination__next" href="' . esc_url(get_pagenum_link($current + 1)) . '" aria-label="Next Page">
            <span class="pagination__text">Next Page</span>
            <span class="pagination__icon" aria-hidden="true">&gt;</span>
        </a></li>';
    }

    echo '</ul></nav>';
}