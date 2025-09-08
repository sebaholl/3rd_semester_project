<?php get_header(); ?>

<?php
// Vytáhneme obsah hero z ACF Options (fallbacky, když ACF ještě není vyplněné)
$headline  = omni_opt('hero_headline', 'Get Moving Today');
$sub       = omni_opt('hero_subheadline', 'All for Sport. All for You.');
$cta_label = omni_opt('hero_cta_label', function_exists('pll__')?pll__('Shop Now'):'Shop Now');
$cta_link  = omni_opt('hero_cta_link', ['url'=>home_url('/')]); // ACF Link vrací pole (url, title, target)
$bg        = omni_opt('hero_bg'); // ACF Image vrací pole
$bgurl     = is_array($bg) && !empty($bg['url']) ? esc_url($bg['url']) : '';
?>

<section class="hero">
  <div class="hero-text">
    <h1><?php echo esc_html($headline); ?></h1>
    <p><?php echo esc_html($sub); ?></p>
    <a class="btn btn-primary" href="<?php echo esc_url(is_array($cta_link)?($cta_link['url']??'#'):'#'); ?>">
      <?php echo esc_html($cta_label); ?>
    </a>
    <a class="btn btn-secondary" href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">
      <?php echo function_exists('pll__') ? pll__('Read Blog') : __('Read Blog','omniora'); ?>
    </a>
  </div>
  <div class="hero-image" style="background-image:url('<?php echo $bgurl; ?>')"></div>
</section>

<section>
  <h2><?php echo function_exists('pll__') ? pll__('From our Blog') : __('From our Blog','omniora'); ?></h2>
  <div class="blog-grid">
    <?php
      $q = new WP_Query(['post_type'=>'post','posts_per_page'=>3]);
      if ($q->have_posts()):
        while ($q->have_posts()): $q->the_post(); ?>
          <article class="blog-card">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html( wp_trim_words(get_the_excerpt(), 24) ); ?></p>
          </article>
    <?php endwhile; wp_reset_postdata(); else: ?>
      <p>No posts yet.</p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
