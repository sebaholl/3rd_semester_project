<?php
/**
 * Home: Blog highlights (ACF-friendly + i18n)
 * Fields:
 *  - blog_featured (Relationship → Post IDs)
 *  - blog_all_link (Link) [optional]
 */

$acf_items = function_exists('get_field') ? (get_field('blog_featured') ?: []) : [];
$all_link  = function_exists('get_field') ? get_field('blog_all_link') : null;

$lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';

// Normalize to IDs (+ filter to current language if available)
$post_ids = [];
if ($acf_items && is_array($acf_items)) {
  foreach ($acf_items as $it) {
    $id = is_object($it) ? (int)$it->ID : (int)$it;
    if ($id) $post_ids[] = $id;
  }
  if ($lang && function_exists('pll_get_post_language')) {
    $post_ids = array_values(array_filter($post_ids, function($pid) use ($lang) {
      $pl = pll_get_post_language($pid);
      return !$pl || $pl === $lang;
    }));
  }
}

// Fallback for the “All Blogs” button
if (empty($all_link)) {
  $posts_page_id = (int) get_option('page_for_posts');
  if ($posts_page_id) {
    if (function_exists('pll_get_post')) {
      $t = pll_get_post($posts_page_id);
      if ($t) $posts_page_id = $t;
    }
    $all_link = [
      'url'    => get_permalink($posts_page_id),
      'title'  => function_exists('pll__') ? pll__('All Blogs') : __('All Blogs','omniora'),
      'target' => ''
    ];
  }
}

// If nothing selected in ACF, fallback to latest posts (scoped to language)
if (empty($post_ids)) {
  $args = [
    'post_type'      => 'post',
    'posts_per_page' => 4,
    'post_status'    => 'publish'
  ];
  if ($lang) $args['lang'] = $lang;

  $q = new WP_Query($args);
  if ($q->have_posts()) {
    while ($q->have_posts()) { $q->the_post(); $post_ids[] = get_the_ID(); }
    wp_reset_postdata();
  }
}

// Empty state
if (empty($post_ids)) {
  $msg = function_exists('pll__') ? pll__('No blog posts found.') : __('No blog posts found.','omniora');
  echo '<section class="section"><div class="container"><p class="muted">'.esc_html($msg).'</p></div></section>';
  return;
}
?>

<section class="section section--blog">
  <div class="container">
    <header class="section-head">
      <h2 class="h2">
        <?php echo function_exists('pll__') ? pll__('From the Blog') : __('From the Blog','omniora'); ?>
      </h2>

      <?php if (!empty($all_link['url'])): ?>
        <a class="link-pill"
           href="<?php echo esc_url($all_link['url']); ?>"
           target="<?php echo esc_attr($all_link['target'] ?? ''); ?>">
          <?php
            $btn_title = !empty($all_link['title'])
              ? $all_link['title']
              : (function_exists('pll__') ? pll__('All Blogs') : __('All Blogs','omniora'));
            echo esc_html($btn_title);
          ?>
        </a>
      <?php endif; ?>
    </header>

    <div class="blog-grid">
      <?php foreach ($post_ids as $post_id): ?>
        <?php include get_stylesheet_directory() . '/post-card-blog.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
