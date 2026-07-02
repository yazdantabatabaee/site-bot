<?php
defined('ABSPATH') || exit;

class DM_Orders {

    public static function create(int $user_id, array $data): int|false {
        global $wpdb;
        $ok = $wpdb->insert($wpdb->prefix . 'dm_orders', [
            'user_id'       => $user_id,
            'plan'          => sanitize_text_field($data['plan']),
            'price'         => intval($data['price']),
            'country'       => sanitize_text_field($data['country']),
            'account_email' => sanitize_text_field($data['account_email']),
            'status'        => 'pending_payment',
            'created_at'    => current_time('mysql'),
        ]);
        return $ok ? $wpdb->insert_id : false;
    }

    public static function get(int $id): ?array {
        global $wpdb;
        $r = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}dm_orders WHERE id=%d", $id),
            ARRAY_A
        );
        return $r ?: null;
    }

    public static function get_user_orders(int $user_id): array {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}dm_orders WHERE user_id=%d ORDER BY id DESC",
                $user_id
            ), ARRAY_A
        );
    }

    public static function update(int $id, array $data): void {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'dm_orders', $data, ['id' => $id]);
    }

    public static function all(string $status = ''): array {
        global $wpdb;
        $q = "SELECT o.*, u.name AS display_name, u.email AS user_email_real, u.mobile
              FROM {$wpdb->prefix}dm_orders o
              LEFT JOIN {$wpdb->prefix}dm_users u ON o.user_id = u.id";
        if ($status) $q .= $wpdb->prepare(' WHERE o.status=%s', $status);
        $q .= ' ORDER BY o.id DESC';
        return $wpdb->get_results($q, ARRAY_A);
    }

    public static function make_final_email(string $base, int $order_id): string {
        return $base . '_' . $order_id;
    }

    public static function status_label(string $status): string {
        return [
            'pending_payment' => '⏳ منتظر پرداخت',
            'pending_review'  => '🔍 در انتظار تایید',
            'approved'        => '✅ تایید شده',
            'active'          => '✅ فعال',
            'rejected'        => '❌ رد شده',
            'expired'         => '🕐 منقضی',
        ][$status] ?? $status;
    }

    public static function plans(): array {
        return [
            '1user' => ['name' => 'پلن ۱ کاربره', 'price' => 400000, 'users' => 1, 'days' => 30],
            '2user' => ['name' => 'پلن ۲ کاربره', 'price' => 600000, 'users' => 2, 'days' => 30],
        ];
    }

    public static function locations(): array {
        return [
            'armenia' => 'Armenia 🇦🇲',
            'germany' => 'Germany 🇩🇪',
            'turkey'  => 'Turkey 🇹🇷',
            'finland' => 'Finland 🇫🇮',
            'all'     => 'All 🌍',
        ];
    }
}
