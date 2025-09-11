<article class="group rounded-2xl border border-gray-200 overflow-hidden bg-white shadow-sm hover:shadow-md transition">
  <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>" class="block">
    <!-- Obrázek -->
    <div class="aspect-[16/9] bg-gray-100 overflow-hidden">
      <?php if (has_post_thumbnail()): ?>
        <?php the_post_thumbnail('large', ['class'=>'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']); ?>
      <?php endif; ?>
    </div>

    <!-- Text -->
    <div class="p-5">
      <h2 class="text-lg font-semibold text-gray-900 group-hover:text-brand-600 transition">
        <?php the_title(); ?>
      </h2>
     <p class="mt-2 text-sm text-gray-600 excerpt-clamp">
  <?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?>
</p>
      <div class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-brand-600 group-hover:translate-x-1 transition">
        <?php echo function_exists('pll__') ? pll__('Read more') : __('Read more','omniora'); ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </div>
  </a>
</article>
