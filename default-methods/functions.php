<?php
defined('ABSPATH') || exit;

/* ── Theme setup ─────────────────────────── */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    add_theme_support('custom-logo', ['height'=>60,'width'=>60,'flex-height'=>true,'flex-width'=>true]);
    register_nav_menus([
        'primary' => 'Primary Menu',
        'footer'  => 'Footer Menu',
    ]);
});

/* ── Assets ──────────────────────────────── */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('dm-theme', get_stylesheet_uri(), [], '1.0.0');
    wp_enqueue_style('dm-theme-bridge',
        get_template_directory_uri() . '/assets/css/plugin-bridge.css',
        ['dm-theme'], '1.0.0');
    wp_enqueue_script('dm-theme-js',
        get_template_directory_uri() . '/assets/js/main.js',
        [], '1.0.0', true);
});

/* ── Widget areas ────────────────────────── */
add_action('widgets_init', function () {
    register_sidebar([
        'name' => 'Footer', 'id' => 'footer-widgets',
        'before_widget' => '<div class="footer-col">', 'after_widget' => '</div>',
        'before_title'  => '<h4>', 'after_title' => '</h4>',
    ]);
});

/* ── Nav menu fallback ───────────────────── */
function dm_nav_menu() {
    if (has_nav_menu('primary')) {
        wp_nav_menu(['theme_location'=>'primary','container'=>false,
            'menu_class'=>'', 'items_wrap'=>'%3$s', 'walker'=>new DM_Nav_Walker()]);
    } else {
        echo '<a href="'.home_url('/#how').'">چگونه کار می‌کند</a>';
        echo '<a href="'.home_url('/#pricing').'">پلن‌ها</a>';
        echo '<a href="'.home_url('/#locations').'">لوکیشن‌ها</a>';
        echo '<a href="'.home_url('/#faq').'">سوالات متداول</a>';
        echo '<a href="'.home_url('/tickets/').'">پشتیبانی</a>';
    }
}

class DM_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = in_array('current-menu-item', $item->classes) ? ' current-menu-item' : '';
        $output .= '<a href="'.esc_url($item->url).'" class="'.$classes.'">'.esc_html($item->title).'</a>';
    }
}

/* ── Excerpt length ──────────────────────── */
add_filter('excerpt_length', fn() => 24);
