<?php get_header(); ?>
<?php if (have_posts()): while (have_posts()): the_post(); ?>

  <div class="product-container">

    <?php
      $arguments = [
        'post_type'      => 'shoe',
        'posts_per_page' => -1
      ];
      $loop = new WP_Query($arguments);
    ?>

    <?php if ($loop->have_posts()): while ($loop->have_posts()): $loop->the_post();

      $title = get_the_title();
      $price = function_exists('get_field') ? get_field('price') : '';
      $cover = function_exists('get_field') ? get_field('cover') : null;

      // --- NEW: pull testimonial rating summary for this product
      if (function_exists('omniora_get_product_testimonial_rating')) {
        $rat = omniora_get_product_testimonial_rating(get_the_ID());
      } else {
        $rat = ['avg'=>0, 'count'=>0, 'stars'=>''];
      }
    ?>
      <div class="product-card">
        <div class="product-image">
          <?php
          if (!empty($cover['url'])) {
            echo '<img src="'.esc_url($cover['url']).'" alt="'.esc_attr($title).'">';
          } elseif (has_post_thumbnail()) {
            the_post_thumbnail('medium_large', ['alt' => $title]);
          } else {
            echo '<div class="img-placeholder">No image</div>';
          }
          ?>
        </div>

        <div class="product-info">
          <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html($title); ?></a></h3>

          <!-- NEW: rating strip -->
          <?php if ($rat['count'] > 0): ?>
            <div class="p-rating" aria-label="<?php echo esc_attr( sprintf(__('Rating %.1f out of 5 from %d testimonials','omniora'), $rat['avg'], $rat['count']) ); ?>">
              <span class="p-stars"><?php echo esc_html($rat['stars']); ?></span>
              <span class="p-count">(<?php echo (int)$rat['count']; ?>)</span>
            </div>
          <?php endif; ?>

          <div class="price">
            <span class="current"><?php echo esc_html($price); ?> kr.</span>
          </div>

          <div class="member-price">
            <a class="btn-buy" href="<?php the_permalink(); ?>"><?php _e('BUY NOW','omniora'); ?></a>
          </div>
        </div>
      </div>

    <?php endwhile; wp_reset_postdata(); endif; ?>

  </div>

<?php endwhile; endif; ?>
<?php get_footer(); ?>
