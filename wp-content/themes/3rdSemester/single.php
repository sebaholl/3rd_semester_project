<?php
/** Single Post template (with minimal ACF fields) */
get_header(); ?>

<section class="single-article">
  <div class="container single-article__wrap">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article <?php post_class('single-article__body'); ?>>

        <!-- HEADER -->
        <header class="single-article__header">
          <h1 class="single-article__title"><?php the_title(); ?></h1>

          <?php
          // ACF: subtitle
          $subtitle = function_exists('get_field') ? trim((string) get_field('subtitle')) : '';
          if ($subtitle) :
          ?>
            <p class="single-article__subtitle"><?php echo esc_html($subtitle); ?></p>
          <?php endif; ?>

          <p class="single-article__meta">
            <time datetime="<?php echo esc_attr( get_the_date('c') ); ?>">
              <?php echo esc_html( get_the_date() ); ?>
            </time>
            <?php
              $cats = get_the_category_list(', ');
              if ( $cats ) {
                echo ' · <span class="single-article__cats">' . $cats . '</span>';
              }

              // ACF: reading time
              $reading_time = function_exists('get_field') ? (int) get_field('reading_time') : 0;
              if ($reading_time) {
                echo ' · <span class="badge">'.$reading_time.' ' . esc_html__('min read','omniora') . '</span>';
              }
            ?>
          </p>

          <?php
            // Prefer ACF hero override if present, else featured image
            $hero = function_exists('get_field') ? get_field('hero_image') : null;

            if ( is_array($hero) && !empty($hero['url']) ) : ?>
              <div class="single-article__thumb">
                <img src="<?php echo esc_url($hero['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
              </div>
          <?php elseif ( has_post_thumbnail() ) : ?>
            <div class="single-article__thumb">
              <?php the_post_thumbnail( 'large' ); ?>
            </div>
          <?php endif; ?>
        </header>

        <!-- OPTIONAL SUMMARY -->
        <?php
          $summary = function_exists('get_field') ? trim((string) get_field('summary')) : '';
          if ($summary) :
        ?>
          <p class="single-article__summary"><?php echo esc_html($summary); ?></p>
        <?php endif; ?>

        <!-- CONTENT -->
        <div class="single-article__content">
          <?php
            the_content();

            // Page links if the post is split with <!--nextpage-->
            wp_link_pages( [
              'before' => '<nav class="page-links">' . __( 'Pages:' ),
              'after'  => '</nav>',
            ] );
          ?>
        </div>

        <!-- HIGHLIGHT QUOTE -->
        <?php
          $highlight_quote = function_exists('get_field') ? trim((string) get_field('highlight_quote')) : '';
          if ($highlight_quote) :
        ?>
          <blockquote class="single-article__quote">
            <p><?php echo esc_html($highlight_quote); ?></p>
          </blockquote>
        <?php endif; ?>

        <!-- AUTHOR BOX -->
        <?php
          $author_id = get_the_author_meta( 'ID' );
          $author_bio = get_the_author_meta( 'description', $author_id );
        ?>
        <aside class="single-article__author">
          <div class="single-article__author-avatar">
            <?php echo get_avatar( $author_id, 72 ); ?>
          </div>
          <div class="single-article__author-meta">
            <h3 class="single-article__author-name"><?php echo esc_html( get_the_author() ); ?></h3>
            <?php if ( $author_bio ) : ?>
              <p class="single-article__author-bio"><?php echo esc_html( $author_bio ); ?></p>
            <?php endif; ?>
          </div>
        </aside>

        <!-- TAGS + SHARE -->
        <div class="single-article__meta-bottom">
          <?php $tags_list = get_the_tag_list( '', ' ' ); ?>
          <?php if ( $tags_list ) : ?>
            <div class="single-article__tags">
              <strong><?php _e( 'Tags:', 'omniora' ); ?></strong> <?php echo $tags_list; ?>
            </div>
          <?php endif; ?>

          <?php
            $share_url   = urlencode( get_permalink() );
            $share_title = urlencode( get_the_title() );
          ?>
          <div class="single-article__share">
            <strong><?php _e( 'Share:', 'omniora' ); ?></strong>
            <a rel="nofollow" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>">X</a>
            <a rel="nofollow" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>">Facebook</a>
            <a rel="nofollow" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>">LinkedIn</a>
          </div>
        </div>

        <!-- CTA (from ACF) -->
        <?php
          $cta_label = function_exists('get_field') ? trim((string) get_field('cta_label')) : '';
          $cta_url   = function_exists('get_field') ? trim((string) get_field('cta_url'))   : '';
          if ($cta_label && $cta_url) :
        ?>
          <p class="post-cta">
            <a class="btn btn--dark" href="<?php echo esc_url($cta_url); ?>">
              <?php echo esc_html($cta_label); ?>
            </a>
          </p>
        <?php endif; ?>

        <!-- RELATED POSTS (same categories) -->
        <?php
          $related = new WP_Query( [
            'post_type'           => 'post',
            'posts_per_page'      => 3,
            'post__not_in'        => [ get_the_ID() ],
            'ignore_sticky_posts' => true,
            'orderby'             => 'date',
            'tax_query'           => [
              [
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => wp_get_post_categories( get_the_ID() ),
              ]
            ],
          ] );

          if ( $related->have_posts() ) : ?>
            <section class="related-posts">
              <h3 class="related-posts__title"><?php _e( 'Related posts', 'omniora' ); ?></h3>
              <div class="related-posts__grid">
                <?php
                while ( $related->have_posts() ) : $related->the_post();
                  $post_id = get_the_ID();

                  $card = get_stylesheet_directory() . '/post-card-blog.php';
                  if ( file_exists( $card ) ) {
                    include $card; // uses your existing card partial
                  } else {
                    // Fallback simple card
                    ?>
                    <article class="post-card">
                      <a class="post-card__media" href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) {
                          the_post_thumbnail( 'medium', [ 'class' => 'post-card__img', 'alt' => the_title_attribute( [ 'echo' => false ] ) ] );
                        } else { ?>
                          <div class="post-card__placeholder">No image</div>
                        <?php } ?>
                      </a>
                      <div class="post-card__body">
                        <h4 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <a class="btn btn--dark" href="<?php the_permalink(); ?>"><?php _e( 'Read', 'omniora' ); ?></a>
                      </div>
                    </article>
                    <?php
                  }
                endwhile;
                wp_reset_postdata();
                ?>
              </div>
            </section>
          <?php endif; ?>

        <!-- FOOTER NAV + BACK BUTTON -->
        <footer class="single-article__footer">
          <nav class="single-article__nav">
            <div class="single-article__prev"><?php previous_post_link( '%link', '← %title' ); ?></div>
            <div class="single-article__next"><?php next_post_link( '%link', '%title →' ); ?></div>
          </nav>

          <a class="btn btn--dark" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
            <?php echo function_exists( 'pll__' ) ? pll__( '← Back to Blog' ) : __( '← Back to Blog', 'omniora' ); ?>
          </a>
        </footer>

        <!-- COMMENTS -->
        <?php comments_template(); ?>

      </article>

    <?php endwhile; endif; ?>
  </div>
</section>

<?php get_footer();



