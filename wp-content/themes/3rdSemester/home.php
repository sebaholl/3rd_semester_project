<?php get_header(); ?>

<section class="py-12">
  <div class="container mx-auto px-4">

    <!-- Header se search formem -->
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
      <h1 class="text-3xl font-bold">
        <?php echo function_exists('pll__') ? pll__('Blog') : __('Blog','omniora'); ?>
      </h1>

      <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative">
        <label for="blog-search" class="sr-only">
          <?php echo function_exists('pll__') ? pll__('Search blog') : __('Search blog','omniora'); ?>
        </label>
        <input
          id="blog-search"
          type="search"
          name="s"
          placeholder="<?php echo esc_attr(function_exists('pll__') ? pll__('Search blog…') : __('Search blog…','omniora')); ?>"
          class="w-72 max-w-full rounded-xl border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500"
        >
        <input type="hidden" name="post_type" value="post">
      </form>
    </header>

    <!-- Grid článků -->
    <?php if (have_posts()): ?>
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php while (have_posts()): the_post(); get_template_part('template-parts/post','card'); endwhile; ?>
      </div>

      <!-- Pagination -->
      <div class="mt-10">
        <?php
          the_posts_pagination([
            'mid_size'  => 1,
            'prev_text' => '←',
            'next_text' => '→',
            'screen_reader_text' => '',
          ]);
        ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">
        <?php echo function_exists('pll__') ? pll__('No articles found.') : __('No articles found.','omniora'); ?>
      </p>
    <?php endif; ?>

  </div>
</section>

<?php get_footer(); ?>

