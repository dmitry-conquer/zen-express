<?php
namespace WP_Starter_Theme;
if (!defined('ABSPATH')) {
  exit;
}

final class Menu
{
  public static function register()
  {
    add_filter('nav_menu_link_attributes', [self::class, 'add_custom_menu_link_class'], 10, 4);
    add_filter('nav_menu_css_class', [self::class, 'add_custom_classes_to_menu_li'], 10, 4);
  }

  public static function add_custom_menu_link_class($atts, $item, $args, $depth)
  {
    if ($args->theme_location === 'header_menu') {
      $atts['class'] = 'header__nav-link';
    }
    if ($args->theme_location === 'header_mobile_menu') {
      $atts['class'] = 'header-overlay__nav-link';
    }
    if ($args->theme_location === 'footer_menu') {
      $atts['class'] = 'footer__nav-link';
    }
    return $atts;
  }

  public static function add_custom_classes_to_menu_li($classes, $item, $args, $depth)
  {
    if ($args->theme_location === 'header_menu') {
      $classes[] = 'header__nav-item';
    }
    if ($args->theme_location === 'header_mobile_menu') {
      $classes[] = 'header-overlay__nav-item';
    }
    if ($args->theme_location === 'footer_menu') {
      $classes[] = 'footer__nav-item';
    }
    return $classes;
  }
}