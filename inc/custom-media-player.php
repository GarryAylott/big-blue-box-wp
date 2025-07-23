<?php
/**
 * Dequeues the default WordPress theme classes from the body class array.
 *
 * @package Big_Blue_Box
 */

// Remove the default WordPress CSS
add_action('wp_enqueue_scripts', function() {
  wp_deregister_style('mediaelement');
  wp_deregister_style('wp-mediaelement');
});

// Remove legacy comments
add_filter('wp_audio_shortcode', function($html) {
  return preg_replace(
    '/<!--\[if lt IE 9\]><script>document.createElement\(\'audio\'\);<\/script><!\[endif\]-->/i',
    '',
    $html
  );
});

// Scoped CSS styles for custom media player
add_action('wp_print_footer_scripts', function() {
  if (!wp_script_is('mediaelement', 'done')) return;
  ?>
  <script>
    (function(){
      var s = window._wpmejsSettings = window._wpmejsSettings || {};
      s.features = s.features || mejs.MepDefaults.features;
      s.features.push('scoper');
      MediaElementPlayer.prototype.buildscoper = function(player){
        player.container.addClass('my-podcast-player');
      };
    })();
  </script>
  <?php
});
