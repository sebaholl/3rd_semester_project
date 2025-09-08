<?php
// Woo šablona: použije se pro Shop, Single Product, Cart, Checkout, My Account…
get_header();
?>

<main class="shop-wrapper container">
  <?php
  // vykreslí obsah WooCommerce (archivy, single, cart, checkout, account…)
  if ( function_exists('woocommerce_content') ) {
    woocommerce_content();
  } else {
    // bezpečný fallback (neměl by nastat)
    while (have_posts()) : the_post(); the_content(); endwhile;
  }
  ?>
</main>

<?php get_footer(); ?>
