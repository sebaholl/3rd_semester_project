<?php get_header(); ?>

<main class="search-page container" style="padding:60px 0;">

  <h1 style="font-size:2rem;margin-bottom:24px;">
    <?php echo pll__('Search results for:'); ?> 
    <span style="color:#1e3a8a;">"<?php echo get_search_query(); ?>"</span>
  </h1>

  <?php if (have_posts()) : ?>
    <ul class="search-results" style="list-style:none;padding:0;display:grid;gap:24px;">
      <?php while (have_posts()) : the_post(); ?>
        <li class="search-item" style="border-bottom:1px solid #ddd;padding-bottom:16px;">
          <a href="<?php the_permalink(); ?>" 
             style="font-size:1.2rem;font-weight:600;color:#001f3f;text-decoration:none;">
            <?php the_title(); ?>
          </a>
          <p style="color:#555;margin-top:8px;">
            <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
          </p>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else : ?>
    <p style="font-size:1.1rem;color:#666;">
      <?php echo pll__('No results found. Try another search.'); ?>
    </p>
  <?php endif; ?>

</main>

<?php get_footer(); ?>
