<?php

namespace WP_Starter_Theme;

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Walker for the desktop navigation menu.
 *
 * Renders an Alpine.js-powered three-tier nav matching the design in
 * components/header.html: depth-0 top bar → depth-1 hover dropdown →
 * depth-2 right-side flyout. All CSS classes are defined as constants at
 * the top of the class — change them here when adapting to a new project.
 *
 * Usage:
 *   wp_nav_menu([
 *     'theme_location' => 'header_desktop_menu',
 *     'walker'         => new \WP_Starter_Theme\DesktopMenuWalker(),
 *     'container'      => false,
 *     'items_wrap'     => '<ul class="flex items-center" role="list">%3$s</ul>',
 *     'fallback_cb'    => false,
 *   ]);
 */
final class DesktopMenuWalker extends \Walker_Nav_Menu {

  // ─── CSS Config ─────────────────────────────────────────────────────────────

  /** Depth-0 <a>: plain link */
  private const LINK_BASE_D0 = 'flex items-center px-4 py-2 text-sm text-stone-600 transition-colors hover:text-stone-900 focus-visible:rounded-lg focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none';

  /** Depth-0 <a>: active / current-page link */
  private const LINK_ACTIVE_D0 = 'flex items-center px-4 py-2 text-sm font-medium text-stone-900 transition-colors hover:text-stone-600 focus-visible:rounded-lg focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none';

  /** Depth-0 <a>: parent link that triggers a dropdown */
  private const LINK_PARENT_D0 = 'flex items-center gap-1.5 px-4 py-2 text-sm text-stone-600 transition-colors hover:text-stone-900 focus-visible:rounded-lg focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none';

  /** Down-chevron SVG classes (depth-0 dropdown trigger) */
  private const CHEVRON_DOWN = 'h-4 w-4 shrink-0 text-stone-400 transition-transform duration-200';

  /** Depth-1 <a>: plain sub-link */
  private const LINK_BASE_D1 = 'flex items-center px-4 py-2.5 text-sm text-stone-600 transition-colors hover:bg-stone-50 hover:text-stone-900 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none focus-visible:ring-inset';

  /** Depth-1 <a>: active sub-link */
  private const LINK_ACTIVE_D1 = 'relative flex items-center px-4 py-2.5 text-sm font-medium text-stone-900 transition-colors hover:bg-stone-50 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none focus-visible:ring-inset';

  /** Depth-1 <a>: parent link that triggers a flyout */
  private const LINK_PARENT_D1 = 'flex items-center justify-between gap-6 px-4 py-2.5 text-sm text-stone-600 transition-colors hover:bg-stone-50 hover:text-stone-900 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none focus-visible:ring-inset';

  /** Right-chevron SVG classes (depth-1 flyout trigger) */
  private const CHEVRON_RIGHT = 'h-4 w-4 shrink-0 text-stone-400 transition-transform duration-150';

  /** Amber vertical bar shown left of the active depth-1 item */
  private const D1_ACTIVE_BAR = 'absolute top-1/2 left-0 h-4 w-0.5 -translate-y-1/2 rounded-r bg-amber-500';

  // ────────────────────────────────────────────────────────────────────────────

  /**
   * Opens the submenu wrapper: a positioned <div> + <ul>.
   *
   * $depth is the parent's depth:
   *   0 → depth-1 dropdown (slides down, keyed on `open`)
   *   1 → depth-2 flyout   (slides right, keyed on `subOpen`)
   */
  public function start_lvl(&$output, $depth = 0, $args = null): void {
    if ($depth === 0) {
      $output .= '<div'
        . ' x-show="open"'
        . ' x-transition:enter="transition ease-out duration-150"'
        . ' x-transition:enter-start="opacity-0 -translate-y-1"'
        . ' x-transition:enter-end="opacity-100 translate-y-0"'
        . ' x-transition:leave="transition ease-in duration-100"'
        . ' x-transition:leave-start="opacity-100 translate-y-0"'
        . ' x-transition:leave-end="opacity-0 -translate-y-1"'
        . ' class="absolute top-full left-0 z-50 pt-2">' . "\n"
        . '<ul role="list" class="min-w-44 rounded-xl bg-white py-1.5 shadow-xl ring-1 shadow-stone-900/8 ring-stone-100">' . "\n";
    } else {
      $output .= '<div'
        . ' x-show="subOpen"'
        . ' x-transition:enter="transition ease-out duration-100"'
        . ' x-transition:enter-start="opacity-0 translate-x-2"'
        . ' x-transition:enter-end="opacity-100 translate-x-0"'
        . ' x-transition:leave="transition ease-in duration-75"'
        . ' x-transition:leave-start="opacity-100 translate-x-0"'
        . ' x-transition:leave-end="opacity-0 translate-x-2"'
        . ' class="absolute top-0 left-full z-50 pl-2">' . "\n"
        . '<ul role="list" class="min-w-48 rounded-xl bg-white py-1.5 shadow-xl ring-1 shadow-stone-900/8 ring-stone-100">' . "\n";
    }
  }

  /**
   * Closes the submenu <ul> and its wrapping <div>.
   */
  public function end_lvl(&$output, $depth = 0, $args = null): void {
    $output .= "</ul>\n</div>\n";
  }

  /**
   * Renders the opening <li> and full <a> for a single menu item.
   *
   * Depth-0 parent items append a chevron SVG here so it sits between
   * the <a> and the submenu <div> added by start_lvl().
   */
  public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void {
    $is_active    = in_array('current-menu-item', $item->classes, true)
                 || in_array('current-menu-ancestor', $item->classes, true);
    $has_children = in_array('menu-item-has-children', $item->classes, true);
    $title        = apply_filters('the_title', $item->title, $item->ID);
    $url          = esc_url($item->url);

    if ($depth === 0) {
      $this->render_depth_0($output, $is_active, $has_children, $title, $url);
    } elseif ($depth === 1) {
      $this->render_depth_1($output, $is_active, $has_children, $title, $url);
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
   * Renders a depth-0 <li> + <a> (and down-chevron for parent items).
   */
  private function render_depth_0(
    string &$output,
    bool $is_active,
    bool $has_children,
    string $title,
    string $url
  ): void {
    if ($has_children) {
      $output .= "<li"
        . ' x-data="{ open: false }"'
        . ' class="relative"'
        . ' @mouseenter="open = true"'
        . ' @mouseleave="open = false"'
        . ' @focusin="open = true"'
        . ' @focusout="$event.currentTarget.contains($event.relatedTarget) || (open = false)"'
        . ' @keydown.escape="open = false"'
        . ">\n";

      $aria_current = $is_active ? ' aria-current="page"' : '';
      $output .= sprintf(
        "<a href=\"%s\"%s class=\"%s\" :aria-expanded=\"open.toString()\" aria-haspopup=\"true\">%s%s</a>\n",
        $url,
        $aria_current,
        self::LINK_PARENT_D0,
        esc_html($title),
        $this->chevron_down_svg()
      );

    } elseif ($is_active) {
      $output .= "<li>\n";
      $output .= sprintf(
        "<a href=\"%s\" aria-current=\"page\" class=\"%s\">%s</a>\n",
        $url,
        self::LINK_ACTIVE_D0,
        esc_html($title)
      );

    } else {
      $output .= "<li>\n";
      $output .= sprintf(
        "<a href=\"%s\" class=\"%s\">%s</a>\n",
        $url,
        self::LINK_BASE_D0,
        esc_html($title)
      );
    }
  }

  /**
   * Renders a depth-1 <li> + <a> (and right-chevron for flyout parents).
   */
  private function render_depth_1(
    string &$output,
    bool $is_active,
    bool $has_children,
    string $title,
    string $url
  ): void {
    if ($has_children) {
      $output .= "<li"
        . ' x-data="{ subOpen: false }"'
        . ' class="relative"'
        . ' @mouseenter="subOpen = true"'
        . ' @mouseleave="subOpen = false"'
        . ' @focusin="subOpen = true"'
        . ' @focusout="$event.currentTarget.contains($event.relatedTarget) || (subOpen = false)"'
        . ' @keydown.escape.stop="subOpen = false"'
        . ">\n";

      $aria_current = $is_active ? ' aria-current="page"' : '';
      $output .= sprintf(
        "<a href=\"%s\"%s class=\"%s\" :aria-expanded=\"subOpen.toString()\" aria-haspopup=\"true\">%s%s</a>\n",
        $url,
        $aria_current,
        self::LINK_PARENT_D1,
        esc_html($title),
        $this->chevron_right_svg()
      );

    } elseif ($is_active) {
      $output .= "<li>\n";
      $output .= sprintf(
        "<a href=\"%s\" aria-current=\"page\" class=\"%s\">%s%s</a>\n",
        $url,
        self::LINK_ACTIVE_D1,
        $this->active_bar_d1(),
        esc_html($title)
      );

    } else {
      $output .= "<li>\n";
      $output .= sprintf(
        "<a href=\"%s\" class=\"%s\">%s</a>\n",
        $url,
        self::LINK_BASE_D1,
        esc_html($title)
      );
    }
  }

  /**
   * Renders a depth-2+ <li> + <a> (flyout leaf items, no active indicator).
   */
  private function render_depth_n(
    string &$output,
    bool $is_active,
    string $title,
    string $url
  ): void {
    $output .= "<li>\n";
    $aria_current = $is_active ? ' aria-current="page"' : '';
    $output .= sprintf(
      "<a href=\"%s\"%s class=\"%s\">%s</a>\n",
      $url,
      $aria_current,
      self::LINK_BASE_D1,
      esc_html($title)
    );
  }

  // ─── SVG / markup helpers ───────────────────────────────────────────────────

  /**
   * Returns the down-chevron SVG used on depth-0 dropdown triggers.
   * Alpine rotates it 180° when `open` is true.
   */
  private function chevron_down_svg(): string {
    return sprintf(
      '<svg class="%s" :style="open ? \'transform: rotate(180deg)\' : \'\'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">'
      . '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>'
      . '</svg>',
      self::CHEVRON_DOWN
    );
  }

  /**
   * Returns the right-chevron SVG used on depth-1 flyout triggers.
   * Alpine nudges it 2px right when `subOpen` is true.
   */
  private function chevron_right_svg(): string {
    return sprintf(
      '<svg class="%s" :style="subOpen ? \'transform: translateX(2px)\' : \'\'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">'
      . '<path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>'
      . '</svg>',
      self::CHEVRON_RIGHT
    );
  }

  /**
   * Returns the amber vertical bar shown left of an active depth-1 link.
   */
  private function active_bar_d1(): string {
    return sprintf('<span class="%s" aria-hidden="true"></span>', self::D1_ACTIVE_BAR);
  }
}
