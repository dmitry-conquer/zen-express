<?php
namespace WP_Starter_Theme;
if (!defined('ABSPATH')) {
  exit;
}

final class Setup
{
  public static function register()
  {
    add_action('after_setup_theme', [self::class, 'register_menus']);
    add_action('after_setup_theme', [self::class, 'setup_theme']);
    add_action('wp_enqueue_scripts', [self::class, 'remove_block_css']);
    add_action('login_head', [self::class, 'custom_login_styles']);
    add_action('login_headerurl', [self::class, 'custom_login_logo_url']);
    add_action('login_headertext', [self::class, 'custom_login_logo_url_title']);
    add_filter('wp_img_tag_add_auto_sizes', '__return_false');
  }

  public static function register_menus()
  {
    register_nav_menus([
      'header_menu' => __('Header menu'),
      'header_mobile_menu' => __('Header mobile menu'),
      'footer_menu' => __('Footer menu'),
    ]);
  }

  public static function remove_block_css()
  {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');
    wp_dequeue_style('storefront-gutenberg-blocks');
  }

  public static function setup_theme()
  {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo', [
      'height' => 100,
      'width' => 400,
      'flex-height' => true,
      'flex-width' => true,
    ]);
  }

  public static function allow_custom_mime_types()
  {
    $mimes['woff'] = 'font/woff';
    $mimes['woff2'] = 'font/woff2';
    return $mimes;
  }

  public static function custom_login_styles()
  {
    echo '<style>
			body.login {
					background: linear-gradient(357deg, rgb(70 182 191 / 67%) 0%, rgb(255 255 255 / 82%) 100%);
			}
			.login h1 a {
					background-image: url("");
					background-size: contain;
					width: 100%;
					height: 80px;
			}
			.login form {
					border-radius: 10px;
					box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			}
	</style>';
  }

  public static function custom_login_logo_url()
  {
    return home_url();
  }

  public static function custom_login_logo_url_title()
  {
    return get_bloginfo('name');
  }
}