<?php

namespace WP_Starter_Theme;

if (!defined('ABSPATH')) {
  exit;
}

define('ASSETS_VERSION', '1.0.0');

class Assets {
  public static function register() {
    add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets']);

    add_action('init', [self::class, 'remove_global_styles']);

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
  }

  public static function enqueue_assets() {
    if (!is_admin()) {
      wp_enqueue_style('style-css', WP_STARTER_THEME_URI . '/assets/css/style.css', [], filemtime(WP_STARTER_THEME_DIR . '/assets/css/style.css') ?: ASSETS_VERSION);
      wp_enqueue_script('script-js', WP_STARTER_THEME_URI . '/assets/js/script.js', [], filemtime(WP_STARTER_THEME_DIR . '/assets/js/script.js') ?: ASSETS_VERSION, true);
    }
  }
  
  public static function dequeue_style() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
  }

  public static function remove_global_styles() {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
  }
}
