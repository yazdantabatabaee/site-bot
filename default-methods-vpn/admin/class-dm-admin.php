<?php
defined('ABSPATH') || exit;

class DM_Admin {

    public function __construct() {
        add_action('admin_menu',              [$this, 'menus']);
        add_action('admin_enqueue_scripts',   [$this, 'assets']);
        add_action('admin_post_dm_approve',   [$this, 'action_approve']);
        add_action('admin_post_dm_reject',    [$this, 'action_reject']);
        add_action('admin_post_dm_settings',  [$this, 'action_settings']);
        add_action('admin_post_dm_test_api',  [$this, 'action_test_api']);
        add_action('admin_post_dm_ticket_reply',   [$this, 'action_ticket_reply']);
        add_action('admin_post_dm_renew_approve',  [$this, 'action_renew_approve']);
        add_action('admin_post_dm_renew_reject',   [$this, 'action_renew_reject']);
        add_action('admin_post_dm_clear_log',      [$this, 'action_clear_log']);
    }

    public function assets(): void {
        wp_enqueue_style('dm-admin', DM_VPN_URL . 'admin/css/dm-admin.css', [], DM_VPN_VERSION);
    }

    public function menus(): void {
        add_menu_page('Default Methods VPN', 'DM VPN', 'manage_options',
            'dm-vpn', [$this, 'page_orders'], 'dashicons-shield', 30);
        add_submenu_page('dm-vpn', 'Orders', 'Orders', 'manage_options',
            'dm-vpn-orders', [$this, 'page_orders']);
        add_submenu_page('dm-vpn', 'Tickets', 'Tickets', 'manage_options',
            'dm-vpn-tickets', [$this, 'page_tickets']);
        add_submenu_page('dm-vpn', 'Renewals', 'Renewals', 'manage_options',
            'dm-vpn-renewals', [$this, 'page_renewals']);
        add_submenu_page('dm-vpn', 'Settings', 'Settings', 'manage_options',
            'dm-vpn-settings', [$this, 'page_settings']);
        add_submenu_page('dm-vpn', 'Debug Log', 'Debug Log', 'manage_options',
            'dm-vpn-log', [$this, 'page_log']);
    }

    public function page_orders(): void {
        $status = sanitize_text_field($_GET['filter'] ?? '');
        $orders = DM_Orders::all($status);
        $plans  = DM_Orders::plans();
        include DM_VPN_PATH . 'admin/views/orders.php';
    }

    public function page_tickets(): void {
        $tid = intval($_GET['tid'] ?? 0);
        if ($tid) {
            $ticket = DM_Tickets::get($tid);
            $msgs   = DM_Tickets::messages($tid);
            include DM_VPN_PATH . 'admin/views/ticket-detail.php';
        } else {
            $tickets = DM_Tickets::all();
            include DM_VPN_PATH . 'admin/views/tickets.php';
        }
    }

    public function page_renewals(): void {
        global $wpdb;
        $renewals = $wpdb->get_results(
            "SELECT r.*,u.name AS display_name,u.email AS user_email,o.final_email
             FROM {$wpdb->prefix}dm_renewals r
             LEFT JOIN {$wpdb->prefix}dm_users u ON r.user_id=u.id
             LEFT JOIN {$wpdb->prefix}dm_orders o ON r.order_id=o.id
             ORDER BY r.id DESC", ARRAY_A);
        include DM_VPN_PATH . 'admin/views/renewals.php';
    }

    public function page_settings(): void {
        include DM_VPN_PATH . 'admin/views/settings.php';
    }

    public function page_log(): void {
        $logs = (array) get_option('dm_debug_log', []);
        include DM_VPN_PATH . 'admin/views/debug-log.php';
    }

    /* ── تایید سفارش ────────────────────────── */
    public function action_approve(): void {
        check_admin_referer('dm_approve');
        $order_id = intval($_POST['order_id']);
        $order    = DM_Orders::get($order_id);
        if (!$order) wp_die('Order not found');

        $plans    = DM_Orders::plans();
        $plan     = $plans[$order['plan']] ?? ['users'=>1,'days'=>30];
        $ids      = DM_XUI_API::inbound_ids($order['country']);

        // لاگ قبل از شروع
        error_log("[DM-VPN] Approving order #{$order_id} email={$order['final_email']} country={$order['country']} ids=" . implode(',', $ids));

        $xui    = new DM_XUI_API();
        $result = $xui->provision_with_country(
            $order['final_email'], $order['country'], $ip_limit, $days
        );

        if ($result['success']) {
            // ذخیره configs به صورت JSON
            $configs_json = wp_json_encode($result['links']);
            DM_Orders::update($order_id, [
                'status'  => 'active',
                'sub_url' => $result['sub_url'],
                'configs' => $configs_json,
            ]);

            // ارسال ایمیل به کاربر
            $user = self::get_dm_user($order['user_id']);
            if ($user && $user['email']) {
                $links_text = implode("\n", $result['links']);
                $subject    = '✅ Your VPN is Ready — Default Methods';
                $body       = "Hi {$user['name']},\n\n"
                            . "Order #{$order_id} has been approved!\n\n"
                            . "📧 Account Email: {$order['final_email']}\n"
                            . "📡 Subscription Link:\n{$result['sub_url']}\n\n"
                            . ($links_text ? "🔧 Configs:\n{$links_text}\n\n" : '')
                            . "View your dashboard:\n" . home_url('/dashboard/');
                wp_mail($user['email'], $subject, $body);
            }
            wp_redirect(admin_url('admin.php?page=dm-vpn-orders&msg=approved'));
        } else {
            $err = implode(' | ', $xui->get_last_errors());
            DM_Orders::update($order_id, ['status' => 'approved', 'notes' => $err]);
            wp_redirect(admin_url('admin.php?page=dm-vpn-orders&msg=xui_error&err=' . urlencode($err)));
        }
        exit;
    }

    /* ── رد سفارش ───────────────────────────── */
    public function action_reject(): void {
        check_admin_referer('dm_reject');
        $order_id = intval($_POST['order_id']);
        $reason   = sanitize_textarea_field($_POST['reason'] ?? '');
        $order    = DM_Orders::get($order_id);
        if (!$order) wp_die('Order not found');

        DM_Orders::update($order_id, ['status' => 'rejected', 'notes' => $reason]);
        $user = self::get_dm_user($order['user_id']);
        if ($user && $user['email']) {
            wp_mail($user['email'],
                'Order Rejected — Default Methods',
                "Hi {$user['name']},\n\nOrder #{$order_id} was not approved.\n"
                . ($reason ? "Reason: {$reason}\n" : '')
                . "\nPlease contact support.");
        }
        wp_redirect(admin_url('admin.php?page=dm-vpn-orders&msg=rejected'));
        exit;
    }

    /* ── Test API ────────────────────────────── */
    public function action_test_api(): void {
        check_admin_referer('dm_test_api');
        $xui    = new DM_XUI_API();
        $result = $xui->test_connection();
        wp_redirect(admin_url('admin.php?page=dm-vpn-settings&test=' . ($result['ok']?'ok':'fail')
            . '&msg=' . urlencode($result['msg'])));
        exit;
    }

    /* ── Settings ────────────────────────────── */
    public function action_settings(): void {
        check_admin_referer('dm_settings');
        foreach (['dm_webhook_url','dm_webhook_secret',
                  'dm_card_number','dm_card_owner'] as $k) {
            update_option($k, sanitize_text_field($_POST[$k] ?? ''));
        }
        $map = $_POST['dm_inbound_map'] ?? '';
        if (json_decode($map)) update_option('dm_inbound_map', $map);
        wp_redirect(admin_url('admin.php?page=dm-vpn-settings&saved=1'));
        exit;
    }

    /* ── Ticket reply ────────────────────────── */
    public function action_ticket_reply(): void {
        check_admin_referer('dm_ticket_reply');
        $tid = intval($_POST['ticket_id']);
        $msg = sanitize_textarea_field($_POST['message']);
        DM_Tickets::reply($tid, $msg, 'admin');
        $ticket = DM_Tickets::get($tid);
        $user   = self::get_dm_user($ticket['user_id'] ?? 0);
        if ($user && $user['email']) {
            wp_mail($user['email'], "Reply to Ticket #{$tid}",
                "Support replied:\n\n{$msg}\n\nView: " . home_url('/tickets/?tid='.$tid));
        }
        wp_redirect(admin_url("admin.php?page=dm-vpn-tickets&tid={$tid}&replied=1"));
        exit;
    }

    /* ── Renew approve ───────────────────────── */
    public function action_renew_approve(): void {
        global $wpdb;
        check_admin_referer('dm_renew');
        $rid   = intval($_POST['renewal_id']);
        $r     = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dm_renewals WHERE id=%d", $rid), ARRAY_A);
        if (!$r) wp_die('Not found');

        $order = DM_Orders::get($r['order_id']);
        $xui   = new DM_XUI_API();
        $ok    = $xui->renew_client($order['final_email'] ?? '');
        $wpdb->update($wpdb->prefix.'dm_renewals',
            ['status' => $ok ? 'approved' : 'xui_error'], ['id' => $rid]);
        $user = self::get_dm_user($r['user_id']);
        if ($user && $user['email'] && $ok) {
            wp_mail($user['email'], '✅ Subscription Renewed — Default Methods',
                "Hi {$user['name']},\n\nYour subscription has been renewed for 30 days.\n"
                . "Account: {$order['final_email']}");
        }
        wp_redirect(admin_url('admin.php?page=dm-vpn-renewals&msg='.($ok?'ok':'err')));
        exit;
    }

    /* ── Renew reject ────────────────────────── */
    public function action_renew_reject(): void {
        global $wpdb;
        check_admin_referer('dm_renew');
        $rid = intval($_POST['renewal_id']);
        $wpdb->update($wpdb->prefix.'dm_renewals', ['status'=>'rejected'], ['id'=>$rid]);
        wp_redirect(admin_url('admin.php?page=dm-vpn-renewals&msg=rejected'));
        exit;
    }

    /* ── Clear log ───────────────────────────── */
    public function action_clear_log(): void {
        check_admin_referer('dm_clear_log');
        update_option('dm_debug_log', []);
        wp_redirect(admin_url('admin.php?page=dm-vpn-log&cleared=1'));
        exit;
    }

    /* ── Lookup DM user (not WP user) ────────── */
    private static function get_dm_user(int $id): ?array {
        global $wpdb;
        $r = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dm_users WHERE id=%d", $id
        ), ARRAY_A);
        return $r ?: null;
    }
}