<?php

namespace WP_Starter_Theme;

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Walker for the mobile navigation menu.
 *
 * Renders an Alpine.js-powered accordion menu matching the design in
 * components/header.html. All CSS classes are defined as constants at the
 * top of the class — change them here when adapting to a new project.
 *
 * Usage:
 *   wp_nav_menu([
 *     'theme_location' => 'header_mobile_menu',
 *     'walker'         => new \WP_Starter_Theme\MobileMenuWalker(),
 *     'container'      => false,
 *     'items_wrap'     => '<ul>%3$s</ul>',
 *     'fallback_cb'    => false,
 *   ]);
 */
final class MobileMenuWalker extends \Walker_Nav_Menu {

  // ─── CSS Config ─────────────────────────────────────────────────────────────
  // All classes in one place — swap these when adapting to a new project.

  /** Depth-0 <li>: simple item */
  private const LI_BASE = 'border-b border-stone-100';

  /** Depth-0 <li>: item that has children (becomes an Alpine accordion toggle) */
  private const LI_HAS_CHILDREN = 'flex flex-wrap items-center justify-between border-b border-stone-100 transition-colors hover:bg-stone-50';

  /** Depth-0 <a>: regular link */
  private const LINK_BASE = 'flex items-center px-5 py-3 text-sm text-stone-600 transition-colors hover:bg-stone-50 hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset';

  /** Depth-0 <a>: active / current-page link */
  private const LINK_ACTIVE = 'relative flex items-center px-5 py-3 text-sm font-medium text-stone-900 transition-colors hover:bg-stone-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset';

  /** Depth-0 <a>: parent link (has children; click is stopped, accordion on <li>) */
  private const LINK_PARENT = 'px-5 py-3 text-sm text-stone-600 hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500';

  /** Vertical amber bar shown left of the active depth-0 item */
  private const ACTIVE_BAR = 'absolute top-1/2 left-0 h-5 w-0.5 -translate-y-1/2 rounded-r bg-amber-500';

  /** Depth-1+ submenu <ul> */
  private const SUBMENU = 'w-full border-t border-stone-100 bg-stone-50';

  /** Depth-1 <a>: regular sub-link */
  private const SUB_LINK_BASE = 'flex items-center gap-2.5 py-2.5 pr-5 pl-9 text-sm text-stone-500 transition-colors hover:bg-stone-100 hover:text-stone-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset';

  /** Depth-1 <a>: active sub-link */
  private const SUB_LINK_ACTIVE = 'flex items-center gap-2.5 bg-amber-50 py-2.5 pr-5 pl-9 text-sm font-medium text-stone-900 transition-colors hover:bg-amber-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-inset';

  /** Small dot before each depth-1 link */
  private const SUB_DOT = 'h-1 w-1 shrink-0 rounded-full bg-stone-300';

  /** Small dot before an active depth-1 link */
  private const SUB_DOT_ACTIVE = 'h-1 w-1 shrink-0 rounded-full bg-amber-500';

  /** Chevron icon inside the accordion toggle */
  private const CHEVRON = 'mr-5 h-4 w-4 shrink-0 text-stone-400 transition-transform duration-200';

  // ────────────────────────────────────────────────────────────────────────────

  /**
   * Opens a submenu <ul>.
   * Called once before iterating over child items.
   */
  public function start_lvl(&$output, $depth = 0, $args = null): void {
    $output .= sprintf(
      "\n<ul class=\"%s\" x-show=\"isOpen\" x-collapse>\n",
      self::SUBMENU
    );
  }

  /**
   * Closes a submenu <ul>.
   */
  public function end_lvl(&$output, $depth = 0, $args = null): void {
    $output .= "</ul>\n";
  }

  /**
   * Renders the opening <li> and full <a> for a single menu item.
   *
   * For depth-0 parent items the chevron SVG is appended here so that it sits
   * between the <a> and the submenu <ul> added by start_lvl().
   */
  public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void {
    $is_active    = in_array('current-menu-item', $item->classes, true)
                 || in_array('current-menu-ancestor', $item->classes, true);
    $has_children = in_array('menu-item-has-children', $item->classes, true);
    $title        = apply_filters('the_title', $item->title, $item->ID);
    $url          = esc_url($item->url);

    if ($depth === 0) {
      $this->render_depth_0($output, $is_active, $has_children, $title, $url);
    } else {
      $this->render_depth_n($output, $is_active, $title, $url);
    }
  }

  /**
   * Closes the <li> element.
   */
  public function end_el(&$output, $item, $depth = 0, $args = null): void {
    $output .= "</li>\n";
  }

  // ─── Private renderers ──────────────────────────────────────────────────────

  /**
   * Renders a depth-0 <li> + <a> (and chevron for parent items).
   */
  private function render_depth_0(
    string &$output,
    bool $is_active,
    bool $has_children,
    string $title,
    string $url
  ): void {
    if ($has_children) {
      // Accordion toggle: Alpine state lives on the <li>
      $output .= sprintf(
        "<li class=\"%s\" x-data=\"{ isOpen: false }\" :aria-expanded=\"isOpen.toString()\" @click=\"isOpen = !isOpen\">\n",
        self::LI_HAS_CHILDREN
      );

      // Parent link — click bubbling is stopped so only the chevron tap toggles
      $aria_current = $is_active ? ' aria-current="page"' : '';
      $output .= sprintf(
        "<a href=\"%s\"%s class=\"%s\" @click.stop>%s</a>\n",
        $url,
        $aria_current,
        self::LINK_PARENT,
        esc_html($title)
      );

      // Chevron rotates when accordion is open (inline style driven by Alpine)
      $output .= $this->chevron_svg();

    } elseif ($is_active) {
      $output .= sprintf("<li class=\"%s\">\n", self::LI_BASE);
      $output .= sprintf(
        "<a href=\"%s\" aria-current=\"page\" class=\"%s\">%s%s</a>\n",
        $url,
        self::LINK_ACTIVE,
        $this->active_bar(),
        esc_html($title)
      );

    } else {
      $output .= sprintf("<li class=\"%s\">\n", self::LI_BASE);
      $output .= sprintf(
        "<a href=\"%s\" class=\"%s\">%s</a>\n",
        $url,
        self::LINK_BASE,
        esc_html($title)
      );
    }
  }

  /**
   * Renders a depth-1+ <li> + <a>.
   */
  private function render_depth_n(
    string &$output,
    bool $is_active,
    string $title,
    string $url
  ): void {
    $output .= "<li>\n";

    if ($is_active) {
      $output .= sprintf(
        "<a href=\"%s\" aria-current=\"page\" class=\"%s\">%s%s</a>\n",
        $url,
        self::SUB_LINK_ACTIVE,
        $this->sub_dot(true),
        esc_html($title)
      );
    } else {
      $output .= sprintf(
        "<a href=\"%s\" class=\"%s\">%s%s</a>\n",
        $url,
        self::SUB_LINK_BASE,
        $this->sub_dot(false),
        esc_html($title)
      );
    }
  }

  // ─── SVG / markup helpers ───────────────────────────────────────────────────

  /**
   * Returns the chevron SVG used inside accordion toggles.
   * The inline Alpine binding rotates it when isOpen is true.
   */
  private function chevron_svg(): string {
    return sprintf(
      '<svg class="%s" :style="isOpen ? \'transform: rotate(180deg)\' : \'\'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">'
      . '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>'
      . "</svg>\n",
      self::CHEVRON
    );
  }

  /**
   * Returns the amber vertical bar shown left of an active depth-0 link.
   */
  private function active_bar(): string {
    return sprintf('<span class="%s" aria-hidden="true"></span>', self::ACTIVE_BAR);
  }

  /**
   * Returns the small dot prepended to each depth-1 link.
   *
   * @param bool $active Use the amber (active) variant when true.
   */
  private function sub_dot(bool $active): string {
    return sprintf(
      '<span class="%s" aria-hidden="true"></span>',
      $active ? self::SUB_DOT_ACTIVE : self::SUB_DOT
    );
  }
}
