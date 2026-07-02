<?php
/**
 * Plugin Name: Default Methods VPN Shop
 * Description: VPN shop with 3x-ui integration — custom accounts, orders, tickets, renewals
 * Version: 3.0.0
 * Author: Default Methods
 * Text Domain: dm-vpn
 */
defined('ABSPATH') || exit;

define('DM_VPN_VERSION', '3.0.0');
define('DM_VPN_PATH',    plugin_dir_path(__FILE__));
define('DM_VPN_URL',     plugin_dir_url(__FILE__));

require_once DM_VPN_PATH . 'includes/class-dm-database.php';
require_once DM_VPN_PATH . 'includes/class-dm-auth.php';
require_once DM_VPN_PATH . 'includes/class-dm-xui-api.php';
require_once DM_VPN_PATH . 'includes/class-dm-orders.php';
require_once DM_VPN_PATH . 'includes/class-dm-tickets.php';
require_once DM_VPN_PATH . 'public/class-dm-public.php';
require_once DM_VPN_PATH . 'admin/class-dm-admin.php';

register_activation_hook(__FILE__, ['DM_Database', 'install']);

// DB migration: ensure all tables/columns exist even after in-place plugin updates
add_action('plugins_loaded', function () {
    global $wpdb;

    // ایجاد جداول جدید (dm_users, dm_sessions) اگر وجود نداشته باشند
    $tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}dm_users'");
    if (empty($tables)) {
        DM_Database::install();
    }

    // ستون‌های اضافی روی orders
    $cols = array_column(
        $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}dm_orders", ARRAY_A),
        'Field'
    );
    foreach (['configs LONGTEXT DEFAULT NULL', 'final_email VARCHAR(120) DEFAULT ""',
              'sub_url TEXT DEFAULT NULL'] as $col) {
        $name = explode(' ', $col)[0];
        if (!in_array($name, $cols, true)) {
            $wpdb->query("ALTER TABLE {$wpdb->prefix}dm_orders ADD COLUMN {$col}");
        }
    }
}, 5);

add_action('plugins_loaded', function () {
    new DM_Public();
    if (is_admin()) new DM_Admin();
}, 10);
