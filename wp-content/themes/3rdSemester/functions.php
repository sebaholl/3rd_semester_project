<?php
// Zapnutí základních podpor a registrace menu
add_action('after_setup_theme', function () {
  add_theme_support('title-tag'); // <title> v <head> spravuje WP → základ SEO
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
  add_theme_support('custom-logo', ['height'=>48,'width'=>160,'flex-width'=>true,'flex-height'=>true]);

  register_nav_menus([
    'primary' => __('Primary Menu', 'omniora'),
    'footer'  => __('Footer Menu', 'omniora'),
  ]);
});

// Načtení CSS a JS

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('omniora', get_stylesheet_uri(), [], '1.0.0');
  wp_enqueue_style(
    'shop-style',
    get_template_directory_uri() . '/Shopstyle.css',
    [],
    filemtime(get_template_directory() . '/Shopstyle.css')
  );
  wp_enqueue_script('omniora', get_template_directory_uri().'/assets/main.js', [], '1.0.0', true);
});

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('omniora', get_stylesheet_uri(), [], '1.0.0');
  wp_enqueue_script('omniora', get_template_directory_uri().'/assets/main.js', [], '1.0.0', true);
});

// ACF Options Page (jedno místo pro obsah hero sekce)
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'Theme Settings',
    'menu_title' => 'Theme Settings',
    'menu_slug'  => 'omniora-theme-settings',
    'capability' => 'edit_posts',
    'redirect'   => false
  ]);
}

// Helper pro čtení hodnot z ACF Options
function omni_opt($key, $fallback = '') {
  return function_exists('get_field') ? (get_field($key, 'option') ?: $fallback) : $fallback;
}

// Polylang: pár přeložitelných řetězců (uvidíš v Languages → Strings translations)
if (function_exists('pll_register_string')) {
  foreach (['Shop Now','Read Blog','Blog','Featured Products','From our Blog','Search'] as $s) {
    pll_register_string('omniora', $s, 'theme');
  }
}


