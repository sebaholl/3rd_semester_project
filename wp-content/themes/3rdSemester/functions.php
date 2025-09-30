<?php
/** Theme functions (clean) */

/* Setup */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
  add_theme_support('custom-logo', ['height'=>48,'width'=>160,'flex-width'=>true,'flex-height'=>true]);

  register_nav_menus([
    'primary' => __('Primary Menu', 'omniora'),
    'footer'  => __('Footer Menu', 'omniora'),
  ]);

  add_image_size('avatar-96', 96, 96, true);
});

/* Assets */
add_action('wp_enqueue_scripts', function () {
  $style_path = get_stylesheet_directory() . '/style.css';
  wp_enqueue_style('omniora', get_stylesheet_uri(), [], file_exists($style_path) ? filemtime($style_path) : '1.0.0');

  $shop_css_path = get_template_directory() . '/Shopstyle.css';
  if (file_exists($shop_css_path)) {
    wp_enqueue_style('shop-style', get_template_directory_uri().'/Shopstyle.css', [], filemtime($shop_css_path));
  }

  $main_js_path = get_template_directory() . '/assets/main.js';
  if (file_exists($main_js_path)) {
    wp_enqueue_script('omniora', get_template_directory_uri().'/assets/main.js', [], filemtime($main_js_path), true);
  }
});

/* ACF Options */
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'Theme Settings',
    'menu_title' => 'Theme Settings',
    'menu_slug'  => 'omniora-theme-settings',
    'capability' => 'edit_posts',
    'redirect'   => false
  ]);
}
function omni_opt($key, $fallback = '') {
  return function_exists('get_field') ? (get_field($key, 'option') ?: $fallback) : $fallback;
}

/* Polylang strings (safe if Polylang not installed) */
if (function_exists('pll_register_string')) {
  foreach (['Shop Now','Read Blog','Blog','Featured Products','From our Blog','Search','What customers say'] as $s) {
    pll_register_string('omniora', $s, 'theme');
  }
}

/* Testimonials: avg rating helper (supports post_object/relationship + multiple field names) */
function omniora_get_product_testimonial_rating($product_id) {
  $product_id = (int)$product_id;
  if (!$product_id) return ['avg'=>0, 'count'=>0, 'stars'=>''];

  $cache_key = 'omniora_t_avg_' . $product_id;
  if (($cached = get_transient($cache_key)) !== false) return $cached;

  $keys = ['product_ref','related_product','product','related_shoe'];
  $meta = ['relation' => 'OR'];
  foreach ($keys as $k) {
    $meta[] = ['key'=>$k, 'value'=>$product_id,         'compare'=>'='];        // post_object
    $meta[] = ['key'=>$k, 'value'=>'"'.$product_id.'"', 'compare'=>'LIKE'];     // relationship
  }

  $q = new WP_Query([
    'post_type'      => 'testimonial',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'fields'         => 'ids',
    'meta_query'     => $meta,
  ]);

  $sum = 0; $count = 0;
  if ($q->have_posts() && function_exists('get_field')) {
    foreach ($q->posts as $tid) {
      $r = (int) get_field('rating', $tid);
      if ($r > 0) { $sum += $r; $count++; }
    }
  }
  wp_reset_postdata();

  $avg   = $count ? round($sum / $count, 1) : 0.0;
  $stars = str_repeat('★', (int)round($avg)) . str_repeat('☆', 5 - (int)round($avg));
  $data  = ['avg'=>$avg, 'count'=>$count, 'stars'=>$stars];

  set_transient($cache_key, $data, 12 * HOUR_IN_SECONDS);
  return $data;
}

/* Clear rating cache on testimonial save */
add_action('save_post_testimonial', function ($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (wp_is_post_revision($post_id)) return;
  if (!function_exists('get_field')) return;

  $keys = ['product_ref','related_product','product','related_shoe'];
  $product_ids = [];

  foreach ($keys as $k) {
    $val = get_field($k, $post_id);
    if (is_array($val)) {
      foreach ($val as $v) {
        if (is_object($v) && isset($v->ID)) $product_ids[] = (int)$v->ID;
        elseif (is_numeric($v))             $product_ids[] = (int)$v;
      }
    } else {
      if (is_object($val) && isset($val->ID)) $product_ids[] = (int)$val->ID;
      elseif (is_numeric($val))               $product_ids[] = (int)$val;
    }
  }

  $product_ids = array_values(array_unique(array_filter($product_ids)));
  if (!$product_ids) return;

  global $wpdb;
  foreach ($product_ids as $pid) {
    $like = $wpdb->esc_like('omniora_t_avg_'.$pid) . '%';
    $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", '_transient_'.$like) );
    $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", '_transient_timeout_'.$like) );
  }
});

/* Shortcode: [testimonials limit="6" columns="3" product="123|slug"] */
add_shortcode('testimonials', function ($atts) {
  $atts = shortcode_atts([
    'limit'   => 6,
    'columns' => 3,
    'order'   => 'DESC',
    'product' => ''
  ], $atts, 'testimonials');

  $args = [
    'post_type'      => 'testimonial',
    'posts_per_page' => (int)$atts['limit'],
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => strtoupper($atts['order']) === 'ASC' ? 'ASC' : 'DESC',
  ];

  if (!empty($atts['product'])) {
    $pid = is_numeric($atts['product']) ? (int)$atts['product'] : 0;
    if (!$pid) {
      $p = get_page_by_path(sanitize_title($atts['product']), OBJECT, ['shoe']);
      if ($p) $pid = (int)$p->ID;
    }
    if ($pid) {
      $keys = ['product_ref','related_product','product','related_shoe'];
      $meta = ['relation' => 'OR'];
      foreach ($keys as $k) {
        $meta[] = ['key'=>$k, 'value'=>$pid,         'compare'=>'='];
        $meta[] = ['key'=>$k, 'value'=>'"'.$pid.'"', 'compare'=>'LIKE'];
      }
      $args['meta_query'] = $meta;
    }
  }

  $q = new WP_Query($args);
  if (!$q->have_posts()) return '<p class="muted">'.esc_html__('No testimonials yet.', 'omniora').'</p>';

  $cols = max(1, min(4, (int)$atts['columns']));
  ob_start();
  echo '<div class="testimonial-grid cols-' . esc_attr($cols) . '">';

  while ($q->have_posts()) {
    $q->the_post();
    $id     = get_the_ID();
    $quote  = function_exists('get_field') ? (string)get_field('quote', $id) : get_the_excerpt($id);
    $name   = get_the_title($id);
    $rating = function_exists('get_field') ? (int)get_field('rating', $id) : 5;

    $avatar = has_post_thumbnail($id)
      ? get_the_post_thumbnail($id, 'avatar-96', ['class'=>'t-card__avatar','loading'=>'lazy','alt'=>esc_attr(($name ?: __('Customer','omniora')).' portrait')])
      : '';

    $r = max(1, min(5, $rating));
    $stars = str_repeat('★', $r) . str_repeat('☆', 5 - $r);

    echo '<article class="t-card">';
      if ($avatar) echo '<div class="t-card__media">'.$avatar.'</div>';
      echo '<div class="t-card__body">';
        echo '<div class="t-card__rating" aria-label="'.esc_attr(sprintf(__('Rating: %d out of 5','omniora'), $r)).'">'.$stars.'</div>';
        if ($quote) echo '<blockquote class="t-card__quote"><p>'.wp_kses_post($quote).'</p></blockquote>';
        if ($name)  echo '<h3 class="t-card__name">'.esc_html($name).'</h3>';
      echo '</div>';
    echo '</article>';
  }

  echo '</div>';
  wp_reset_postdata();
  return ob_get_clean();
});
