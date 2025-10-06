<?php get_header(); ?>

<?php
// Current front page (per language)
$home_id = get_queried_object_id();

// Hero fields (ACF on the page)
$headline   = function_exists('get_field') ? (string) get_field('hero_headline', $home_id)    : '';
$sub        = function_exists('get_field') ? (string) get_field('hero_subheadline', $home_id) : '';
$cta_link   = function_exists('get_field') ? get_field('hero_cta_link', $home_id)            : null; // ACF Link array
$cta_label  = function_exists('get_field') ? (string) get_field('hero_cta_label', $home_id)  : '';
$bg         = function_exists('get_field') ? get_field('hero_bg', $home_id)                  : null; // ACF Image

// Fallbacks (translatable)
if (!$headline)  $headline  = function_exists('pll__') ? pll__('Get Moving Today') : 'Get Moving Today';
if (!$sub)       $sub       = function_exists('pll__') ? pll__('All for Sport. All for You.') : 'All for Sport. All for You.';
if (!$cta_label) $cta_label = function_exists('pll__') ? pll__('Shop Now') : __('Shop Now','omniora');

// CTA URL: prefer ACF link; else try Shop page in current language; else home
$cta_url    = '#';
$cta_target = '';
if (is_array($cta_link) && !empty($cta_link['url'])) {
  $cta_url    = $cta_link['url'];
  $cta_target = !empty($cta_link['target']) ? $cta_link['target'] : '';
} else {
  $shop_page = get_page_by_path('shop'); // EN slug by default; Polylang will map below if available
  if ($shop_page && function_exists('pll_get_post')) {
    $shop_translated_id = pll_get_post($shop_page->ID);
    if ($shop_translated_id) $shop_page = get_post($shop_translated_id);
  }
  $cta_url = $shop_page ? get_permalink($shop_page->ID) : home_url('/');
}

// Hero background URL
$bgurl = '';
if (is_array($bg) && !empty($bg['url']))      $bgurl = esc_url($bg['url']);
elseif (is_string($bg) && !empty($bg))        $bgurl = esc_url($bg);

// Blog page URL in current language
$posts_page_id = (int) get_option('page_for_posts');
if ($posts_page_id && function_exists('pll_get_post')) {
  $translated = pll_get_post($posts_page_id);
  if ($translated) $posts_page_id = $translated;
}
$blog_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
$read_blog_text = function_exists('pll__') ? pll__('Read Blog') : __('Read Blog','omniora');
?>

<section class="homepage-hero">
  <div class="homepage-hero__inner container">
    <div class="homepage-hero__text">
      <h1 class="homepage-hero__title"><?php echo esc_html($headline); ?></h1>
      <p class="homepage-hero__sub"><?php echo esc_html($sub); ?></p>
      <div class="homepage-hero__actions">
        <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>" target="<?php echo esc_attr($cta_target); ?>">
          <?php echo esc_html($cta_label); ?>
        </a>
        <a class="btn btn--secondary" href="<?php echo esc_url($blog_url); ?>">
          <?php echo esc_html($read_blog_text); ?>
        </a>
        <!-- Survey CTA -->
        <a class="btn btn--secondary" href="<?php echo esc_url( function_exists('omniora_get_survey_url') ? omniora_get_survey_url() : home_url('/survey/') ); ?>">
          <?php echo function_exists('pll__') ? pll__('Take the survey') : __('Take the survey','omniora'); ?>
        </a>
      </div>
    </div>
    <div class="homepage-hero__image" <?php echo $bgurl ? 'style="background-image:url('.$bgurl.');"' : ''; ?>></div>
  </div>
</section>

<?php
// Blog highlights section
$path = get_stylesheet_directory() . '/home-blog-template.php';
if ( file_exists( $path ) ) { include $path; }
?>

<?php get_footer(); ?>



