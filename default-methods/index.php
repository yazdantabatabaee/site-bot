<?php get_header(); ?>
<div class="page-wrap">
  <div class="container entry-content">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <article style="margin-bottom:48px">
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div style="color:var(--muted);margin:16px 0"><?php the_excerpt(); ?></div>
      </article>
    <?php endwhile; else: ?>
      <p>مطلبی یافت نشد.</p>
    <?php endif; ?>
  </div>
</div>
<?php get_footer(); ?>
