<?php get_header(); ?>
<?php if (have_posts()): while (have_posts()): the_post(); ?>

  <div class="product-container">

    <!-- Survey banner CTA -->
    <p class="survey-banner" style="text-align:center; margin: 16px 0 24px;">
      <a class="btn btn--dark" href="<?php echo esc_url( function_exists('omniora_get_survey_url') ? omniora_get_survey_url() : home_url('/survey/') ); ?>">
        <?php echo function_exists('pll__') ? pll__('Help us tailor your gear') : __('Help us tailor your gear','omniora'); ?>
      </a>
    </p>

    <?php
      $loop = new WP_Query([
        'post_type'      => 'shoe',
        'posts_per_page' => -1,
        'lang'           => function_exists('pll_current_language') ? pll_current_language('slug') : ''
      ]);
    ?>

    <?php if ($loop->have_posts()): while ($loop->have_posts()): $loop->the_post();
      $title = get_the_title();
      $price = function_exists('get_field') ? get_field('price') : '';
      $cover = function_exists('get_field') ? get_field('cover') : null;
      $rat   = function_exists('omniora_get_product_testimonial_rating')
        ? omniora_get_product_testimonial_rating(get_the_ID())
        : ['avg'=>0,'count'=>0,'stars'=>''];
    ?>
      <div class="product-card">
        <div class="product-image">
          <?php
          if (!empty($cover['url'])) {
            echo '<img src="'.esc_url($cover['url']).'" alt="'.esc_attr($title).'">';
          } elseif (has_post_thumbnail()) {
            the_post_thumbnail('medium_large', ['alt' => $title]);
          } else {
            $noimg = function_exists('pll__') ? pll__('No image') : __('No image','omniora');
            echo '<div class="img-placeholder">'.esc_html($noimg).'</div>';
          }
          ?>
        </div>

        <div class="product-info">
          <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html($title); ?></a></h3>

          <?php if (!empty($rat['count'])): ?>
            <div class="p-rating" aria-label="<?php
              echo esc_attr( sprintf(
                function_exists('pll__') ? pll__('Rating %1$.1f / 5 from %2$d testimonials') : __('Rating %1$.1f / 5 from %2$d testimonials','omniora'),
                $rat['avg'],
                $rat['count']
              ) );
            ?>">
              <span class="p-stars"><?php echo esc_html($rat['stars']); ?></span>
              <span class="p-count">(<?php echo (int)$rat['count']; ?>)</span>
            </div>
          <?php endif; ?>

          <?php if ($price): ?>
            <div class="price">
              <span class="current"><?php echo esc_html( function_exists('omniora_format_price') ? omniora_format_price($price) : $price ); ?></span>
            </div>
          <?php endif; ?>

          <div class="member-price">
            <a class="btn-buy" href="<?php the_permalink(); ?>">
              <?php echo function_exists('pll__') ? pll__('BUY NOW') : __('BUY NOW','omniora'); ?>
            </a>
          </div>
        </div>
      </div>
    <?php endwhile; wp_reset_postdata(); endif; ?>
  </div>

<?php endwhile; endif; ?>
<?php get_footer(); ?>
