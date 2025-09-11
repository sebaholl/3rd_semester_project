<?php
/**
 * Home: Blog highlights (no Tailwind, ACF Free friendly + fallback)
 * Fields (ACF Free):
 *  - blog_featured (Relationship, Post Type=Post, Return=Post ID)
 *  - blog_all_link (Link) [optional]
 */

// ACF values (if ACF active)
$acf_items = function_exists('get_field') ? (get_field('blog_featured') ?: []) : [];
$all_link  = function_exists('get_field') ? get_field('blog_all_link') : null;

// Normalize Relationship results to Post IDs (handles IDs or WP_Post)
$post_ids = [];
if ($acf_items && is_array($acf_items)) {
  foreach ($acf_items as $it) { $post_ids[] = is_object($it) ? (int)$it->ID : (int)$it; }
  $post_ids = array_values(array_filter($post_ids));
}

// Fallback for the “All Blogs” buttonw
if (empty($all_link)) {
  $posts_page_id = (int) get_option('page_for_posts');
  if ($posts_page_id) {
    $all_link = [
      'url'    => get_permalink($posts_page_id),
      'title'  => __('All Blogs','omniora'),
      'target' => ''
    ];
  }
}

// If nothing selected in ACF, fallback to latest posts
if (empty($post_ids)) {
  $q = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 4,
    'post_status'    => 'publish'
  ]);
  if ($q->have_posts()) {
    while ($q->have_posts()) { $q->the_post(); $post_ids[] = get_the_ID(); }
    wp_reset_postdata();
  }
}

// If still nothing, show a friendly note and exit
if (empty($post_ids)) {
  echo '<section class="section"><div class="container"><p class="muted">No blog posts found.</p></div></section>';
  return;
}
?>

<section class="section section--blog">
  <div class="container">
    <header class="section-head">
      <h2 class="h2"><?php echo function_exists('pll__') ? pll__('From the Blog') : __('From the Blog','omniora'); ?></h2>

      <?php if (!empty($all_link['url'])): ?>
        <a class="link-pill" href="<?php echo esc_url($all_link['url']); ?>"
           target="<?php echo esc_attr($all_link['target'] ?? ''); ?>">
          <?php echo esc_html($all_link['title'] ?: __('All Blogs','omniora')); ?>
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



