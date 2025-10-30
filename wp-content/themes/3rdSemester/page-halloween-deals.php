<?php
// Template Name: Halloween Deals Page
get_header(); 
?>

<?php
$loop = new WP_Query([
  'post_type'      => 'product',
  'posts_per_page' => -1,
//   'meta_query'     => [
//     [
//       'key'     => 'is_halloween_deal',
//       'value'   => '1',
//       'compare' => '='
//     ]
]);
?>
<div class="halloween-deals">
  <?php if ( $loop->have_posts() ) : ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
      <article class="deal-card">
        <a class="deal-card__media" href="<?php echo esc_url( get_the_permalink() ); ?>">
          <?php 
            if ( has_post_thumbnail() ) {
              echo get_the_post_thumbnail( get_the_ID(), 'medium', [
                'class' => 'deal-card__img',
                'alt'   => esc_attr( get_the_title() ),
              ] );
            } else {
              // Fallback image (optional)
              echo '<img class="deal-card__img" src="' . esc_url( get_template_directory_uri() . '/assets/pictures/default.png' ) . '" alt="' . esc_attr( get_the_title() ) . '">';
            }
          ?>
        </a>

        <div class="deal-card__body">
          <h2 class="deal-card__title">
            <a href="<?php echo esc_url( get_the_permalink() ); ?>">
              <?php echo esc_html( get_the_title() ); ?>
            </a>
          </h2>

          <p class="deal-card__excerpt">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
          </p>

          <div class="deal-card__meta">
            <span class="deal-card__price">
              <?php echo $product ? wp_kses_post( $product->get_price_html() ) : ''; ?>
            </span>

            <?php if ( function_exists( 'woocommerce_template_loop_add_to_cart' ) ) : ?>
              <div class="deal-card__cta">
                <?php woocommerce_template_loop_add_to_cart(); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endwhile; wp_reset_postdata(); ?>
  <?php else : ?>
    <p class="halloween-deals__empty">No Halloween deals found.</p>
  <?php endif; ?>
</div>
<?php get_footer() ?>