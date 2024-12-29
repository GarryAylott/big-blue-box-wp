<?php
/**
 * Template part for displaying a tag based on the post's category.
 *
 * @package Big_Blue_Box
 */
?>

<?php
    $categories = get_the_category();
    if (!empty($categories)) {
        foreach ($categories as $category) {
            if ($category->slug == 'articles') {
                echo '<div class="category-tag rounded-xs"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none">
  <path fill="#fff" d="M5.25 2.5a.74.74 0 0 0-.75.75v9.5c0 .281-.063.531-.156.75h9.406a.74.74 0 0 0 .75-.75v-9.5a.76.76 0 0 0-.75-.75h-8.5Zm-3 12.5C1 15 0 14 0 12.75V3.5a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75v9.25c0 .438.313.75.75.75a.74.74 0 0 0 .75-.75v-9.5C3 2.031 4 1 5.25 1h8.5C14.969 1 16 2.031 16 3.25v9.5A2.26 2.26 0 0 1 13.75 15H2.25ZM5.5 4.25a.74.74 0 0 1 .75-.75h3a.76.76 0 0 1 .75.75v2.5a.74.74 0 0 1-.75.75h-3a.722.722 0 0 1-.75-.75v-2.5Zm6.25-.75h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h1a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-1a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm-5.5 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Zm0 2.5h6.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-6.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75Z"/>
</svg> <span>Article</span></div>';
            } elseif ($category->slug == 'podcasts') {
                echo '<div class="category-tag rounded-xs"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" fill="none">
    <path fill="#fff" d="M7.5 3c0-.813-.688-1.5-1.5-1.5A1.5 1.5 0 0 0 4.5 3v5c0 .844.656 1.5 1.5 1.5A1.5 1.5 0 0 0 7.5 8V3ZM3 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3ZM2 6.75V8c0 2.219 1.781 4 4 4 2.188 0 4-1.781 4-4V6.75a.74.74 0 0 1 .75-.75.76.76 0 0 1 .75.75V8c0 2.813-2.094 5.094-4.75 5.469V14.5h1.5a.76.76 0 0 1 .75.75.74.74 0 0 1-.75.75h-4.5a.722.722 0 0 1-.75-.75.74.74 0 0 1 .75-.75h1.5v-1.031A5.502 5.502 0 0 1 .5 8V6.75A.74.74 0 0 1 1.25 6a.76.76 0 0 1 .75.75Z"/>
</svg> <span>Podcast</span></div>';
            }
        }
    }
?>