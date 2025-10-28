<?php
/* Single Shoe (product) — login-gated ACF front-end testimonial form + list */

/* ACF front-end forms need this before get_header() */
if ( function_exists('acf_form_head') ) acf_form_head();

get_header(); ?>
<section class="section product">
  <div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      $title   = get_the_title();
      $price   = function_exists('get_field') ? get_field('price') : '';
      $cover   = function_exists('get_field') ? get_field('cover') : null;
      $shoe_id = get_the_ID();

      // i18n labels
      $lbl_write  = function_exists('pll__') ? pll__('Write a review') : __('Write a review','omniora');
      $lbl_submit = function_exists('pll__') ? pll__('Submit review') : __('Submit review','omniora');
      $lbl_what   = function_exists('pll__') ? pll__('What customers say') : __('What customers say','omniora');
      $lbl_noimg  = function_exists('pll__') ? pll__('No image') : __('No image','omniora');
      $lbl_thanks = function_exists('pll__') ? pll__('Thanks! Your testimonial is awaiting review.') : __('Thanks! Your testimonial is awaiting review.','omniora');

      $thanks_url = add_query_arg('tmsg','thanks', get_permalink($shoe_id));
    ?>
      <article <?php post_class('product__wrap'); ?>>
        <header class="product__head">
          <h1 class="h1"><?php echo esc_html($title); ?></h1>
        </header>

        <div class="product__grid">
          <div class="product__media">
            <?php
            if ( !empty($cover['url']) ) {
              echo '<img src="'.esc_url($cover['url']).'" alt="'.esc_attr($title).'">';
            } elseif ( has_post_thumbnail() ) {
              the_post_thumbnail('large', ['alt' => $title]);
            } else {
              echo '<div class="img-placeholder">'.esc_html($lbl_noimg).'</div>';
            }
            ?>
          </div>
          <div class="product__body">
            <?php if ( $price ): ?>
              <p class="product__price">
                <?php echo esc_html( function_exists('omniora_format_price') ? omniora_format_price($price) : $price ); ?>
              </p>
            <?php endif; ?>
            <div class="product__content"><?php the_content(); ?></div>
          </div>
        </div>

        <!-- Front-end review form (login-gated) -->
        <section class="product__review-form" id="write-review" style="margin-top:24px;">
          <h2 class="h2"><?php echo esc_html($lbl_write); ?></h2>

          <?php if ( isset($_GET['tmsg']) && $_GET['tmsg'] === 'thanks' ) : ?>
            <p class="notice success"><?php echo esc_html($lbl_thanks); ?></p>
          <?php endif; ?>

          <?php if ( is_user_logged_in() && function_exists('acf_form') ) : ?>

            <?php
            // ACF form: creates a new "testimonial" and binds to this product
            acf_form([
              'post_id'              => 'new_post',
              'new_post'             => [
                'post_type'   => 'testimonial',
                'post_status' => 'pending', // moderation on
              ],
              'field_groups'         => [], // show fields per location rules
              'submit_value'         => $lbl_submit,
              'uploader'             => 'wp',
              'return'               => $thanks_url,
              'html_updated_message' => '',
              // Spam honeypot:
              'html_before_fields'   => '<input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">',
              // Hidden link to this product for the post-save hook:
              'hidden_fields'        => [ 'current_product' => (int) $shoe_id ],
            ]);
            ?>

          <?php else: // Guest: show login/register buttons with redirect back to form ?>
            <?php
              // Hard-wire your exact pages and include redirect back to the form
              $redirect_target = get_permalink($shoe_id) . '#write-review';
              $login_url = add_query_arg(
                'redirect_to',
                rawurlencode($redirect_target),
                home_url('/login-2/')
              );
              $reg_url = add_query_arg(
                'redirect_to',
                rawurlencode($redirect_target),
                home_url('/account/')
              );
            ?>
            <div class="notice error">
              <p><?php echo esc_html__('You must be logged in to write a testimonial.','omniora'); ?></p>
              <p>
                <a class="btn btn--dark" href="<?php echo esc_url($login_url); ?>">
                  <?php echo esc_html__('Log in','omniora'); ?>
                </a>
                <a class="btn btn--outline" href="<?php echo esc_url($reg_url); ?>">
                  <?php echo esc_html__('Create an account','omniora'); ?>
                </a>
              </p>
            </div>
          <?php endif; ?>
        </section>

        <?php
        // Latest testimonials for THIS product (current language)
        $keys = ['product_ref','related_product','product','related_shoe'];
        $meta = ['relation' => 'OR'];
        foreach ( $keys as $k ) {
          $meta[] = ['key' => $k, 'value' => $shoe_id,         'compare' => '='];
          $meta[] = ['key' => $k, 'value' => '"'.$shoe_id.'"', 'compare' => 'LIKE']; // relationship (serialized)
        }
        $q = new WP_Query([
          'post_type'      => 'testimonial',
          'posts_per_page' => 3,
          'post_status'    => 'publish',
          'orderby'        => 'date',
          'order'          => 'DESC',
          'meta_query'     => $meta,
          'lang'           => function_exists('pll_current_language') ? pll_current_language('slug') : '',
        ]);
        ?>
        <section id="pdp-testimonials" class="pdp-testimonials" style="margin-top:32px;">
          <h2 class="h2"><?php echo esc_html($lbl_what); ?></h2>

          <?php if ( $q->have_posts() ) : ?>
            <div class="testimonial-grid cols-3">
              <?php while ( $q->have_posts() ) : $q->the_post();
                $tid    = get_the_ID();
                $name   = get_the_title($tid);
                $quote  = function_exists('get_field') ? (string) get_field('quote', $tid) : get_the_excerpt($tid);
                $rating = function_exists('get_field') ? (int) get_field('rating', $tid) : 5;
                $avatar = has_post_thumbnail($tid)
                  ? get_the_post_thumbnail($tid, 'avatar-96', [
                      'class'   => 't-card__avatar',
                      'loading' => 'lazy',
                      'alt'     => esc_attr( ($name ?: __('Customer','omniora')) . ' portrait' ),
                    ])
                  : '';
                $r = max(1, min(5, $rating));
              ?>
                <article class="t-card">
                  <?php if ( $avatar ) : ?><div class="t-card__media"><?php echo $avatar; ?></div><?php endif; ?>
                  <div class="t-card__body">
                    <div class="t-card__rating" aria-label="<?php echo esc_attr( sprintf( __('Rating: %d out of 5','omniora'), $r ) ); ?>">
                      <?php echo str_repeat('★', $r) . str_repeat('☆', 5 - $r); ?>
                    </div>
                    <?php if ( $quote ) : ?><blockquote class="t-card__quote"><p><?php echo wp_kses_post($quote); ?></p></blockquote><?php endif; ?>
                    <?php if ( $name )  : ?><h3 class="t-card__name"><?php echo esc_html($name); ?></h3><?php endif; ?>
                  </div>
                </article>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          <?php else: ?>
            <p class="muted">
              <?php echo function_exists('pll__') ? pll__('No testimonials for this product yet.') : __('No testimonials for this product yet.','omniora'); ?>
            </p>
          <?php endif; ?>
        </section>
      </article>
    <?php endwhile; endif; ?>
  </div>
</section>

<?php get_footer(); ?>

