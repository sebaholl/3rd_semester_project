<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header" role="banner">
  <div class="container" style="display:flex;align-items:center;gap:16px;padding:12px 0;flex-wrap:wrap;">
    
    <!-- Logo -->
    <div class="site-branding" style="display:flex;align-items:center;">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php if (has_custom_logo()) the_custom_logo(); ?>
      </a>
    </div>

    <!-- 🔹 Hamburger menu button (visible only on mobile) -->
    <button class="menu-toggle" aria-label="Toggle menu" style="display:none; margin-left:auto; background:transparent; border:1px solid rgba(255,255,255,.4); color:#fff; border-radius:6px; padding:6px 10px; cursor:pointer; font-size:22px;">
      ☰
    </button>

    <!-- Header actions -->
    <div class="header-actions" style="display:flex;align-items:center;gap:12px;margin-left:auto;flex-wrap:wrap;">
      
      <!-- Primary nav -->
      <nav class="primary-nav" role="navigation" aria-label="Primary">
        <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'nav']); ?>
      </nav>

      <!-- Language switch -->
      <?php if (function_exists('pll_the_languages')):
        $langs = pll_the_languages(['raw'=>1,'hide_if_empty'=>0]);
        if (!empty($langs)): ?>
          <nav class="lang-switch" aria-label="<?php echo esc_attr__('Language','omniora'); ?>">
            <ul>
              <?php foreach ($langs as $l): ?>
                <li class="<?php echo !empty($l['current_lang']) ? 'is-active' : ''; ?>">
                  <a href="<?php echo esc_url($l['url']); ?>" lang="<?php echo esc_attr($l['locale']); ?>" hreflang="<?php echo esc_attr($l['locale']); ?>">
                    <?php echo esc_html(strtoupper($l['slug'])); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>
      <?php endif; endif; ?>

      <!-- Search bar -->
      <form role="search" method="get" class="header-search-form" 
            action="<?php echo esc_url( pll_home_url() ); ?>"
            style="display:flex;align-items:center;gap:8px;">
        <input 
          type="search" 
          name="s" 
          placeholder="<?php echo function_exists('pll__') ? pll__('Search...') : __('Search...','omniora'); ?>" 
          value="<?php echo get_search_query(); ?>"
          style="padding:6px 10px;border-radius:4px;border:1px solid #ccc;"
        />
        <button type="submit" 
          style="padding:6px 12px;border:none;background:#001f3f;color:white;border-radius:4px;cursor:pointer;">
          <?php echo function_exists('pll__') ? pll__('Search') : __('Search','omniora'); ?>
        </button>
      </form>

    </div><!-- /header-actions -->
  </div>

  <div class="search-panel">
    <div class="container"><?php get_search_form(); ?></div>
  </div>
</header>

<!-- 🔹 Simple JS toggle for mobile menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.nav');

  if (menuToggle && nav) {
    // Show hamburger on mobile only
    const handleResize = () => {
      if (window.innerWidth <= 768) {
        menuToggle.style.display = 'block';
        nav.classList.remove('is-open');
      } else {
        menuToggle.style.display = 'none';
        nav.classList.remove('is-open');
      }
    };

    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('is-open');
    });

    window.addEventListener('resize', handleResize);
    handleResize();
  }
});
</script>

<main class="container" id="content" role="main">

