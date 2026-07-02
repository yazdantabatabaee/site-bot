<?php get_header(); ?>

<div class="page-wrap">
  <?php while (have_posts()): the_post(); ?>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  <?php endwhile; ?>
</div>

<?php get_footer(); ?>
