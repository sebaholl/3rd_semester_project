<?php
/* DEBUG */ echo "<!-- USING home.php -->\n";
/** Blog index (Posts page) */
get_header(); ?>

<section class="section">
  <div class="container">
    <header class="section-head">
      <h1 class="h2"><?php echo function_exists('pll__') ? pll__('Blog') : __('Blog','omniora'); ?></h1>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="blog-grid">
        <?php while ( have_posts() ) : the_post();
          $post_id = get_the_ID();
          include get_stylesheet_directory() . '/post-card-blog.php';
        endwhile; ?>
      </div>

      <nav class="pagination-wrap" aria-label="<?php esc_attr_e('Posts pagination','omniora'); ?>">
        <?php
          the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => __('« Prev','omniora'),
            'next_text' => __('Next »','omniora'),
          ]);
        ?>
      </nav>

    <?php else: ?>
      <p class="muted"><?php _e('No posts yet.','omniora'); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php the_posts_pagination(); ?>

<?php get_footer();

