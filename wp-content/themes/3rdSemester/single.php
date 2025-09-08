<?php get_header(); ?>

<article>
  <header>
    <h1><?php the_title(); ?></h1>
    <p><small><?php echo get_the_date(); ?></small></p>
    <?php if (has_post_thumbnail()) the_post_thumbnail('large', ['class'=>'featured']); ?>
  </header>

  <div class="content">
    <?php while (have_posts()): the_post(); the_content(); endwhile; ?>
  </div>

  <?php comments_template(); ?>
</article>

<?php get_footer(); ?>
