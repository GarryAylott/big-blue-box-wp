<?php
/**
 * Load custom log in styles
 *
 * @package Big_Blue_Box
 */

// Enqueue custom login stylesheet
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('bbb-login', get_stylesheet_directory_uri() . '/style.css');
});
