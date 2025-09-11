<?php get_header(); ?>

<?php
// Read hero fields from the Home page (no Options page needed)
$home_id   = get_queried_object_id(); // the "Home" page ID

$headline  = function_exists('get_field') ? (get_field('hero_headline', $home_id) ?: 'Get Moving Today') : 'Get Moving Today';
$sub       = function_exists('get_field') ? (get_field('hero_subheadline', $home_id) ?: 'All for Sport. All for You.') : 'All for Sport. All for You.';

$cta       = function_exists('get_field') ? get_field('hero_cta_link', $home_id) : null;  // ACF Link field (array)
$cta_label = function_exists('get_field') ? (get_field('hero_cta_label', $home_id) ?: (function_exists('pll__')? pll__('Shop Now') : 'Shop Now')) : (function_exists('pll__')? pll__('Shop Now') : 'Shop Now');
$cta_link  = is_array($cta) && !empty($cta['url']) ? $cta : ['url' => home_url('/')];

$bg        = function_exists('get_field') ? get_field('hero_bg', $home_id) : null;        // ACF Image field
$bgurl     = '';
if ($bg) {
  $bgurl = is_array($bg) && !empty($bg['url']) ? esc_url($bg['url']) : (is_string($bg) ? esc_url($bg) : '');
}
?>

<section class="homepage-hero">
  <div class="homepage-hero__inner container">
    <div class="homepage-hero__text">
      <h1 class="homepage-hero__title"><?php echo esc_html($headline); ?></h1>
      <p class="homepage-hero__sub"><?php echo esc_html($sub); ?></p>
      <div class="homepage-hero__actions">
        <a class="btn btn--primary" href="<?php echo esc_url(is_array($cta_link)?($cta_link['url']??'#'):'#'); ?>">
          <?php echo esc_html($cta_label); ?>
        </a>
        <a class="btn btn--secondary" href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">
          <?php echo function_exists('pll__') ? pll__('Read Blog') : __('Read Blog','omniora'); ?>
        </a>
      </div>
    </div>
    <div class="homepage-hero__image" <?php echo $bgurl ? 'style="background-image:url('.$bgurl.');"' : ''; ?>></div>
  </div>
</section>


<?php
// Include blog section template from THEME ROOT
$path = get_stylesheet_directory() . '/home-blog-template.php';
if ( file_exists( $path ) ) { include $path; }
?>

<?php get_footer(); ?>


