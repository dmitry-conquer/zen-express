<?php

namespace WP_Starter_Theme;

if (!defined('ABSPATH')) {
  exit;
}

final class Utils {
  public static function register() {
    add_filter('excerpt_length', [self::class, 'excerpt_length_fn']);
  }

  public static function excerpt_length_fn($length) {
    return 20;
  }

  public static function get_bg($image_id, $size = 'full') {
    $attachment_id = absint($image_id);
    if (!$attachment_id) {
      return '';
    }

    $url = wp_get_attachment_image_url($attachment_id, $size);
    if (!$url) {
      return '';
    }

    return sprintf('style="%s"', esc_attr(sprintf("background-image:url('%s')", esc_url($url))));
  }

  public static function asterisk_to_span($text) {
    if (empty($text) || !is_string($text)) {
      return $text;
    }
    return preg_replace('/\*([^*]+?)\*/', '<span>$1</span>', $text);
  }

  public static function render_button(array $link, string $type = 'link', string $class = '', string $event = ''): void {
    if (empty($link['title'])) {
      return;
    }

    if ($type === 'dialog'): ?>

      <button
        x-data
        type="button"
        aria-haspopup="dialog"
        @click="$dispatch('<?= esc_attr($event) ?>')"
        class="<?= esc_attr($class) ?>">
        <?= esc_html($link['title']) ?>
      </button>

    <?php else:
      if (empty($link['url'])) {
        return;
      } ?>

      <a
        href="<?= esc_url($link['url']) ?>"
        target="<?= esc_attr($link['target'] ?: '_self') ?>"
        <?php if ($link['target'] === '_blank'): ?> rel="noopener" <?php endif ?>
        class="<?= esc_attr($class) ?>">
        <?= esc_html($link['title']) ?>
      </a>

<?php endif;
  }
}
