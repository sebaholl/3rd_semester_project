<?php

get_header(); ?>

<section class="section product">
  <div class="container">
    <?php if (have_posts()): while (have_posts()): the_post();

      $title = get_the_title();
      $price = function_exists('get_field') ? get_field('price') : '';
      $cover = function_exists('get_field') ? get_field('cover') : null;
    ?>

      <article <?php post_class('product__wrap'); ?>>
        <header class="product__head">
          <h1 class="h1"><?php echo esc_html($title); ?></h1>
        </header>

        <div class="product__grid">
          <div class="product__media">
            <?php
            if (!empty($cover['url'])) {
              echo '<img src="'.esc_url($cover['url']).'" alt="'.esc_attr($title).'">';
            } elseif (has_post_thumbnail()) {
              the_post_thumbnail('large', ['alt' => $title]);
            } else {
              echo '<div class="img-placeholder">No image</div>';
            }
            ?>
          </div>

          <div class="product__body">
            <p class="product__price"><?php echo esc_html($price); ?> kr.</p>
            <div class="product__content">
              <?php the_content(); ?>
            </div>
            <p><a class="btn btn--dark" href="#pdp-testimonials"><?php _e('What customers say','omniora'); ?></a></p>
          </div>
        </div>

        <!-- Testimonials tied to this product -->
        <section id="pdp-testimonials" class="pdp-testimonials">
          <h2 class="h2"><?php _e('What customers say','omniora'); ?></h2>

          <?php
          // Option A: direct query (no shortcode)
          $args = [
            'post_type'      => 'testimonial',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => [[
              'key'     => 'product_ref',
              'value'   => get_the_ID(),
              'compare' => '='
            ]]
          ];
          $q = new WP_Query($args);

          if ($q->have_posts()): ?>
            <div class="testimonial-grid cols-3">
              <?php while ($q->have_posts()): $q->the_post();
                $tid    = get_the_ID();
                $name   = get_the_title($tid);
                $quote  = function_exists('get_field') ? (string) get_field('quote', $tid) : get_the_excerpt($tid);
                $rating = function_exists('get_field') ? (int) get_field('rating', $tid) : 5;
                $avatar = has_post_thumbnail($tid) ? get_the_post_thumbnail($tid, 'avatar-96', ['class'=>'t-card__avatar','loading'=>'lazy','alt'=>esc_attr($name.' portrait')]) : '';
              ?>
                <article class="t-card">
                  <?php if ($avatar): ?><div class="t-card__media"><?php echo $avatar; ?></div><?php endif; ?>
                  <div class="t-card__body">
                    <div class="t-card__rating" aria-label="<?php echo esc_attr(sprintf(__('Rating: %d out of 5','omniora'), $rating)); ?>">
                      <?php echo str_repeat('★',$rating) . str_repeat('☆', 5-$rating); ?>
                    </div>
                    <blockquote class="t-card__quote"><p><?php echo wp_kses_post($quote); ?></p></blockquote>
                    <h3 class="t-card__name"><?php echo esc_html($name); ?></h3>
                  </div>
                </article>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          <?php else: ?>
            <p class="muted"><?php _e('No testimonials for this product yet.','omniora'); ?></p>
          <?php endif; ?>
        </section>

      </article>

    <?php endwhile; endif; ?>
  </div>
</section>

<?php get_footer();
