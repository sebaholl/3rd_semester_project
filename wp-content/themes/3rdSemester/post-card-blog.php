<?php
if (empty($post_id)) return;

$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
$excerpt   = wp_trim_words(get_the_excerpt($post_id), 26);

// Survey URL (language-aware if helper exists)
$survey_url = function_exists('omniora_get_survey_url') ? omniora_get_survey_url() : home_url('/survey/');

$img_html = '';

// 1) Featured image
if (has_post_thumbnail($post_id)) {
  $img_html = get_the_post_thumbnail($post_id, 'medium_large', [
    'class'   => 'post-card__img',
    'alt'     => esc_attr($title),
    'loading' => 'lazy',
  ]);
} else {
  // 2) ACF hero_image fallback
  $hero = function_exists('get_field') ? get_field('hero_image', $post_id) : '';
  if ($hero) {
    $hero_id = is_array($hero) ? ($hero['ID'] ?? 0) : (is_numeric($hero) ? (int)$hero : 0);
    if ($hero_id) {
      $img_html = wp_get_attachment_image($hero_id, 'medium_large', false, [
        'class'   => 'post-card__img',
        'alt'     => esc_attr($title),
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

// 3) Fallback (translatable)
if (!$img_html) {
  $noimg = function_exists('pll__') ? pll__('No image') : __('No image','omniora');
  $img_html = '<div class="post-card__placeholder">'.esc_html($noimg).'</div>';
}

// Taxonomy meta
$cats = get_the_category($post_id);
$primary_cat = $cats ? $cats[0] : null;
$tags = get_the_terms($post_id, 'post_tag');
?>
<article class="post-card">
  <a class="post-card__media" href="<?php echo esc_url($permalink); ?>">
    <?php echo $img_html; ?>
  </a>

  <div class="post-card__body">
    <h3 class="post-card__title">
      <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
    </h3>

    <!-- Category + Tags chips -->
    <p class="post-card__meta"
       aria-label="<?php echo esc_attr( function_exists('pll__') ? pll__('Post taxonomy') : __('Post taxonomy','omniora') ); ?>">
      <?php if ($primary_cat): ?>
        <a class="chip chip--cat" rel="category tag" href="<?php echo esc_url(get_category_link($primary_cat->term_id)); ?>">
          <?php echo esc_html($primary_cat->name); ?>
        </a>
      <?php endif; ?>

      <?php if (!empty($tags) && !is_wp_error($tags)): ?>
        <?php foreach ($tags as $t): ?>
          <a class="chip chip--tag" rel="tag" href="<?php echo esc_url(get_term_link($t)); ?>">
            <?php echo esc_html($t->name); ?>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </p>

    <p class="post-card__excerpt"><?php echo esc_html($excerpt); ?></p>

    <div class="post-card__actions">
      <a class="btn btn--dark" href="<?php echo esc_url($permalink); ?>">
        <?php echo function_exists('pll__') ? pll__('Read') : __('Read','omniora'); ?>
      </a>
      <a class="btn btn--outline" href="<?php echo esc_url($survey_url); ?>">
        <?php echo function_exists('pll__') ? pll__('Take the survey') : __('Take the survey','omniora'); ?>
      </a>
    </div>
  </div>
</article>


