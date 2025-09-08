<?php if (post_password_required()) return; ?>

<section id="comments" style="margin-top:24px;">
  <?php if (have_comments()): ?>
    <h3><?php echo get_comments_number(); ?> comments</h3>
    <ol class="comment-list">
      <?php wp_list_comments(['style'=>'ol','short_ping'=>true]); ?>
    </ol>
    <?php the_comments_pagination(); ?>
  <?php endif; ?>

  <?php comment_form(); ?>
</section>
