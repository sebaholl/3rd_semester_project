</main>

<footer class="site-footer" role="contentinfo">
  <div class="container">
    <nav aria-label="Footer">
      <?php wp_nav_menu(['theme_location'=>'footer','container'=>false,'menu_class'=>'nav']); ?>
    </nav>
    <small>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></small>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
