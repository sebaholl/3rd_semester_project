<?php
/**
 * Post Card partial
 * Expects $post_id to be set by the parent template
 */
if (empty($post_id)) return;
$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
$excerpt   = wp_trim_words( get_the_excerpt($post_id), 26 );
$has_img   = has_post_thumbnail($post_id);
?>
<article class="post-card">
  <a class="post-card__media" href="<?php echo esc_url($permalink); ?>">
    <?php if ($has_img): ?>
      <?php echo get_the_post_thumbnail($post_id, 'large', ['class'=>'post-card__img','alt'=>esc_attr($title)]); ?>
    <?php else: ?>
      <div class="post-card__placeholder">No image</div>
    <?php endif; ?>
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


