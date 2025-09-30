<?php
/* DEBUG */ echo "<!-- USING category.php -->\n";
get_header();

$term = get_queried_object();
$pag_label = function_exists('pll__') ? pll__('Posts pagination') : __('Posts pagination','omniora');
$prev_txt  = function_exists('pll__') ? pll__('« Prev')           : __('« Prev','omniora');
$next_txt  = function_exists('pll__') ? pll__('Next »')           : __('Next »','omniora');
$empty_txt = function_exists('pll__') ? pll__('No posts in this category yet.') : __('No posts in this category yet.','omniora');
?>
<section class="section">
  <div class="container">
    <header class="archive-header">
      <h1 class="h2"><?php single_term_title(); ?></h1>
      <?php
        $desc = term_description($term, $term->taxonomy);
        if ($desc) echo '<div class="archive-intro">'.wp_kses_post($desc).'</div>';
      ?>
    </header>

    <?php if (have_posts()) : ?>
      <div class="blog-grid">
        <?php while (have_posts()) : the_post();
          $post_id = get_the_ID();
          include get_stylesheet_directory() . '/post-card-blog.php';
        endwhile; ?>
      </div>

      <nav class="pagination-wrap" aria-label="<?php echo esc_attr($pag_label); ?>">
        <?php the_posts_pagination([
          'mid_size'  => 2,
          'prev_text' => $prev_txt,
          'next_text' => $next_txt,
        ]); ?>
      </nav>

    <?php else: ?>
      <p class="muted"><?php echo esc_html($empty_txt); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer();

