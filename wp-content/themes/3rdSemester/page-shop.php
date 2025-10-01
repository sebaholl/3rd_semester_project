<?php get_header(); ?>
<?php if (have_posts()): ?>
  <?php while (have_posts()): the_post(); ?>
    
  <?php  
  // Fetch data

  ?>
  

<!-- Output some HTML  -->
    <div class="product-container">
    <!-- Product Card -->
    
    
    <?php 
        $arguments = array(
            "post_type" => "shoe",
            "posts_per_page" => -1
        );
        
        $loop = new WP_Query($arguments);
        ?>

<?php if($loop->have_posts()): ?>
    <?php while($loop->have_posts()): $loop->the_post() ?>
    
    <?php 
                $title = get_the_title();
                $price = get_field('price');
                $cover = get_field('cover');
                
                ?>

            <div class="product-card">
                    <div class="product-image">
                    <img src="<?php echo esc_url($cover["url"])?>" alt="ASICS Gel-Kayano 32"> 
                    </div>
                    <div class="product-info">
                    <h3><?php echo esc_html($title); ?></h3>
                    <div class="price">
                        <span class="current"><?php echo esc_html($price); ?> kr.</span>
                    </div>
                    <div class="member-price">BUY NOW</div>
                    </div>
                    
            </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

    

    <?php endwhile; ?>
<?php endif; ?>


<?php get_footer(); ?>