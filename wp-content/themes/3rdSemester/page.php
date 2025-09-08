<?php get_header(); ?>

<article class="page">
  <header><h1><?php the_title(); ?></h1></header>
  <div class="content">
    <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
  </div>
</article>

<?php get_footer(); ?>