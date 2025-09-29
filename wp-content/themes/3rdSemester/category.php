<?php
/* DEBUG */ echo "<!-- USING category.php -->\n";
get_header();

$term = get_queried_object();
?>
<section class="section">
  <div class="container">
    <header class="archive-header">
      <h1 class="h2"><?php single_term_title(); ?></h1>
      <?php
        $desc = term_description($term, $term->taxonomy);
        if ($desc) {
          echo '<div class="archive-intro">' . wp_kses_post($desc) . '</div>';
        }
      ?>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="blog-grid">
        <?php while ( have_posts() ) : the_post();
          $post_id = get_the_ID();
          include get_stylesheet_directory() . '/post-card-blog.php';
        endwhile; ?>
      </div>

      <nav class="pagination-wrap" aria-label="<?php esc_attr_e('Posts pagination','omniora'); ?>">
        <?php the_posts_pagination([
          'mid_size'  => 2,
          'prev_text' => __('« Prev','omniora'),
          'next_text' => __('Next »','omniora'),
        ]); ?>
      </nav>

    <?php else: ?>
      <p class="muted"><?php _e('No posts in this category yet.','omniora'); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer();
