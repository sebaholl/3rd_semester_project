<?php
/**
 * Home blog highlights (mosaic)
 * Requires ACF fields described in the docs.
 */

$items = get_field('blog_featured') ?: [];
$all_link = get_field('blog_all_link');

// Fallback na "Posts page", pokud není ACF link vyplněn
if (empty($all_link)) {
  $posts_page_id = (int) get_option('page_for_posts');
  if ($posts_page_id) {
    $all_link = [
      'url' => get_permalink($posts_page_id),
      'title' => 'All Blogs',
      'target' => ''
    ];
  }
}

// Nic k zobrazení?
if (empty($items)) return;
?>

<section class="container my-16">
  <!-- Section header -->
  <header class="mb-6 flex items-center justify-between gap-4">
    <h2 class="font-display text-2xl md:text-3xl">From the Blog</h2>

    <?php if (!empty($all_link['url'])): ?>
      <a href="<?php echo esc_url($all_link['url']); ?>"
         target="<?php echo esc_attr($all_link['target'] ?? ''); ?>"
         class="inline-flex items-center px-4 py-2 rounded-2xl border border-gray-300 hover:bg-gray-50">
        <?php echo esc_html($all_link['title'] ?: 'All Blogs'); ?>
      </a>
    <?php endif; ?>
  </header>

  <!-- Mosaic grid: 2 columns desktop, 1 column mobile -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach ($items as $index => $row):
      $post_id   = $row['post_ref'] ?? 0;
      if (!$post_id) continue;

      $style     = $row['tile_style'] ?: 'image_card'; // bezpečný default
      $kicker    = trim($row['kicker'] ?? '');
      $title     = trim($row['title_override'] ?? '') ?: get_the_title($post_id);
      $excerpt   = trim($row['excerpt_override'] ?? '') ?: wp_trim_words(get_post_field('post_excerpt', $post_id) ?: get_post_field('post_content', $post_id), 24);
      $cta_label = trim($row['cta_label'] ?? '') ?: __('Read', 'your-textdomain');
      $permalink = get_permalink($post_id);

      // Image: override → featured image
      $img_id = !empty($row['image_override']) ? (int)$row['image_override'] : get_post_thumbnail_id($post_id);
      $img    = $img_id ? wp_get_attachment_image($img_id, 'large', false, ['class'=>'w-full h-full object-cover']) : null;

      if ($style === 'text_card'): ?>
        <!-- TEXT CARD -->
        <article class="bg-gray-100 rounded-2xl p-8 flex flex-col justify-between">
          <div>
            <?php if ($kicker): ?>
              <div class="text-xs tracking-wide uppercase text-gray-500 mb-2"><?php echo esc_html($kicker); ?></div>
            <?php endif; ?>

            <h3 class="text-xl md:text-2xl font-semibold text-brand-700"><?php echo esc_html($title); ?></h3>

            <?php if ($excerpt): ?>
              <p class="mt-3 text-sm text-gray-600"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
          </div>

          <div class="mt-6">
            <a href="<?php echo esc_url($permalink); ?>"
               class="inline-flex items-center px-5 py-2 rounded-xl bg-ink text-white shadow-card hover:translate-y-[-1px] transition">
              <?php echo esc_html($cta_label); ?>
            </a>
          </div>
        </article>

      <?php else: ?>
        <!-- IMAGE CARD -->
        <a href="<?php echo esc_url($permalink); ?>"
           class="group block rounded-2xl overflow-hidden bg-gray-200 aspect-[16/9] relative">
          <?php if ($img) echo $img; ?>
          <span class="sr-only"><?php echo esc_html($title); ?></span>
          <!-- volitelný jemný hover efekt -->
          <span class="absolute inset-0 ring-0 group-hover:ring-2 ring-brand-500/60 rounded-2xl transition"></span>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>
