<?php
/**
 * Theme functions (clean version)
 */

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

  add_image_size('avatar-96', 96, 96, true); // square avatar
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

/* Polylang strings (safe if Polylang is not installed) */
if (function_exists('pll_register_string')) {
  foreach (['Shop Now','Read Blog','Blog','Featured Products','From our Blog','Search','What customers say'] as $s) {
    pll_register_string('omniora', $s, 'theme');
  }
}

/* --- Testimonials: rating helper --- */
function omniora_get_product_testimonial_rating($product_id) {
  $product_id = (int) $product_id;
  if (!$product_id) return ['avg'=>0, 'count'=>0, 'stars'=>''];

  $cache_key = 'omniora_t_avg_' . $product_id;
  $cached = get_transient($cache_key);
  if ($cached !== false) return $cached;

  $q = new WP_Query([
    'post_type'      => 'testimonial',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'fields'         => 'ids',
    'meta_query'     => [[
      'key'     => 'product_ref',
      'value'   => $product_id,
      'compare' => '='
    ]]
  ]);

  $count = 0; $sum = 0;
  if ($q->have_posts() && function_exists('get_field')) {
    foreach ($q->posts as $tid) {
      $r = (int) get_field('rating', $tid);
      if ($r > 0) { $sum += $r; $count++; }
    }
  }
  wp_reset_postdata();

  $avg   = $count ? round($sum / $count, 1) : 0.0;
  $stars = str_repeat('★', (int) round($avg)) . str_repeat('☆', 5 - (int) round($avg));

  $data = ['avg'=>$avg, 'count'=>$count, 'stars'=>$stars];
  set_transient($cache_key, $data, 12 * HOUR_IN_SECONDS);
  return $data;
}

/* Clear rating cache when testimonials change */
add_action('save_post_testimonial', function ($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (wp_is_post_revision($post_id)) return;

  if (function_exists('get_field')) {
    $prod_id = (int) get_field('product_ref', $post_id);
    if ($prod_id) {
      global $wpdb;
      $like = $wpdb->esc_like('omniora_t_avg_'.$prod_id) . '%';
      $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", '_transient_'.$like) );
      $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", '_transient_timeout_'.$like) );
    }
  }
});

/* Shortcode: [testimonials limit="6" columns="3" product="123"] */
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

  if (!empty($atts['product']) && function_exists('get_field')) {
    $product_id = is_numeric($atts['product']) ? (int)$atts['product'] : 0;
    if (!$product_id) {
      $p = get_page_by_path(sanitize_title($atts['product']), OBJECT, ['shoe']);
      if ($p) $product_id = (int)$p->ID;
    }
    if ($product_id) {
      $args['meta_query'] = [[
        'key'   => 'product_ref',
        'value' => $product_id,
        'compare' => '='
      ]];
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
    $quote  = function_exists('get_field') ? (string) get_field('quote', $id) : get_the_excerpt($id);
    $name   = get_the_title($id);
    $rating = function_exists('get_field') ? (int) get_field('rating', $id) : 5;

    $avatar = has_post_thumbnail($id)
      ? get_the_post_thumbnail($id, 'avatar-96', ['class'=>'t-card__avatar','loading'=>'lazy','alt'=>esc_attr(($name ?: __('Customer','omniora')).' portrait')])
      : '';

    $r = max(1, min(5, (int)$rating));
    $stars = str_repeat('★', $r) . str_repeat('☆', 5 - $r);

    echo '<article class="t-card">';
      if ($avatar) echo '<div class="t-card__media">'.$avatar.'</div>';
      echo '<div class="t-card__body">';
        echo '<div class="t-card__rating" aria-label="'.esc_attr(sprintf(__('Rating: %d out of 5', 'omniora'), $r)).'">'.$stars.'</div>';
        if ($quote) echo '<blockquote class="t-card__quote"><p>'.wp_kses_post($quote).'</p></blockquote>';
        if ($name)  echo '<h3 class="t-card__name">'.esc_html($name).'</h3>';
      echo '</div>';
    echo '</article>';
  }

  echo '</div>';
  wp_reset_postdata();
  return ob_get_clean();
});
