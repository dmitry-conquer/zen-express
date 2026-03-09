<?php
/**
 * The header for our theme
 *
 * This is the template that displays header>
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP Starter Theme
 */

?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <div id="page" class="site-page">
    <a class="skip-link sr-only" href="#primary">Skip to content</a>

    <?php get_template_part('template-parts/header', 'default'); ?>