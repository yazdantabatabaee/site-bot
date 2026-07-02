<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="notif-bar">
  🎉 با کد <strong>WELCOME</strong> ۲۰٪ تخفیف اولین خرید بگیرید — <a href="<?= home_url('/shop/') ?>">همین حالا</a>
</div>

<header id="site-header">
  <div class="nav-inner">
    <a href="<?= home_url('/') ?>" class="nav-logo">
      <span class="nav-logo-mark">🛡️</span>
      <span>Default Methods</span>
    </a>

    <nav class="nav-menu" id="navMenu">
      <?php dm_nav_menu(); ?>
    </nav>

    <div class="nav-actions">
      <?php if (is_user_logged_in()): ?>
        <a href="<?= home_url('/dashboard/') ?>" class="btn btn-ghost">داشبورد</a>
      <?php else: ?>
        <a href="<?= wp_login_url(home_url('/dashboard/')) ?>" class="btn btn-ghost">ورود</a>
      <?php endif; ?>
      <a href="<?= home_url('/shop/') ?>" class="btn btn-primary btn-cta-nav">🚀 خرید VPN</a>
      <button class="nav-toggle" id="navToggle" aria-label="منو">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
