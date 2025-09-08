<?php get_header(); ?>

<h1><?php echo function_exists('pll__') ? pll__('Blog') : __('Blog','omniora'); ?></h1>
<?php get_search_form(); ?>

<div class="blog-grid">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
  <article class="blog-card">
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p><small><?php echo get_the_date(); ?></small></p>
    <p><?php echo esc_html( wp_trim_words(get_the_excerpt(), 26) ); ?></p>
  </article>
<?php endwhile; else: ?>
  <p>No posts found.</p>
<?php endif; ?>
</div>

<?php the_posts_pagination(); ?>
<?php get_footer(); ?>
