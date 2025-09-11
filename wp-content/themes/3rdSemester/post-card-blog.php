<?php
if (empty($post_id)) return;
$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
$excerpt   = wp_trim_words(get_the_excerpt($post_id), 26);

$img_html = '';

// 1) Featured image
if (has_post_thumbnail($post_id)) {
  $img_html = get_the_post_thumbnail($post_id, 'medium_large', [
    'class' => 'post-card__img',
    'alt'   => esc_attr($title),
    'loading' => 'lazy',
  ]);
} else {
  // 2) ACF hero_image (funguje pro ID i array; pokud ACF vrací URL, vezmeme ji taky)
  $hero = function_exists('get_field') ? get_field('hero_image', $post_id) : '';
  if ($hero) {
    $hero_id = is_array($hero) ? ($hero['ID'] ?? 0) : (is_numeric($hero) ? (int)$hero : 0);
    if ($hero_id) {
      $img_html = wp_get_attachment_image($hero_id, 'medium_large', false, [
        'class' => 'post-card__img',
        'alt'   => esc_attr($title),
        'loading' => 'lazy',
      ]);
    } else {
      $url = is_array($hero) ? ($hero['url'] ?? '') : (filter_var($hero, FILTER_VALIDATE_URL) ? $hero : '');
      if ($url) {
        $img_html = '<img class="post-card__img" src="'.esc_url($url).'" alt="'.esc_attr($title).'" loading="lazy">';
      }
    }
  }
}

// 3) Fallback
if (!$img_html) {
  $img_html = '<div class="post-card__placeholder">No image</div>';
}
?>
<article class="post-card">
  <a class="post-card__media" href="<?php echo esc_url($permalink); ?>">
    <?php echo $img_html; ?>
  </a>

  <div class="post-card__body">
    <h3 class="post-card__title">
      <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
    </h3>
    <p class="post-card__excerpt"><?php echo esc_html($excerpt); ?></p>
    <a class="btn btn--dark" href="<?php echo esc_url($permalink); ?>">
      <?php echo function_exists('pll__') ? pll__('Read') : __('Read','omniora'); ?>
    </a>
  </div>
</article>


