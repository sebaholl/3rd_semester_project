<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header" role="banner">
  <div class="container" style="display:flex;align-items:center;gap:16px;padding:12px 0;">
    <div class="site-branding">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php if (has_custom_logo()) the_custom_logo(); ?>
        <span><?php bloginfo('name'); ?></span>
      </a>
    </div>

    <div class="header-actions" style="display:flex;align-items:center;gap:12px;margin-left:auto;">
      <nav class="primary-nav" role="navigation" aria-label="Primary">
        <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'nav']); ?>
      </nav>

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

      <button class="btn btn-primary js-search-toggle">
        <?php echo function_exists('pll__') ? pll__('Search') : __('Search','omniora'); ?>
      </button>
    </div>
  </div>

  <div class="search-panel">
    <div class="container"><?php get_search_form(); ?></div>
  </div>
</header>

<main class="container" id="content" role="main">
