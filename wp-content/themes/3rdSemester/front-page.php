<?php get_header(); ?>

<?php
// HERO obsah z ACF Options (fallbacky)
$headline  = function_exists('omni_opt') ? omni_opt('hero_headline', 'Get Moving Today') : 'Get Moving Today';
$sub       = function_exists('omni_opt') ? omni_opt('hero_subheadline', 'All for Sport. All for You.') : 'All for Sport. All for You.';
$cta_label = function_exists('omni_opt') ? omni_opt('hero_cta_label', function_exists('pll__')?pll__('Shop Now'):'Shop Now') : (function_exists('pll__')?pll__('Shop Now'):'Shop Now');
$cta_link  = function_exists('omni_opt') ? omni_opt('hero_cta_link', ['url'=>home_url('/')]) : ['url'=>home_url('/')];
$bg        = function_exists('omni_opt') ? omni_opt('hero_bg') : null;
$bgurl     = (is_array($bg) && !empty($bg['url'])) ? esc_url($bg['url']) : '';
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
  <div class="hero-image" style="<?php echo $bgurl ? "background-image:url('{$bgurl}')" : ''; ?>"></div>
</section>

<?php
// ZDE vložíš blog mozaiku – teď hned pod hero.
// Až vytvoříš sekci kategorií, tenhle include jen přesuneš pod ně.
get_template_part('template-parts/home','blog');
?>

<?php get_footer(); ?>
