<?php
defined('ABSPATH') || exit;

class DM_Tickets {

    public static function create(int $user_id, string $subject,
                                  string $message, int $order_id = 0): int|false {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'dm_tickets', [
            'user_id'    => $user_id,
            'order_id'   => $order_id,
            'subject'    => sanitize_text_field($subject),
            'status'     => 'open',
            'created_at' => current_time('mysql'),
        ]);
        $tid = $wpdb->insert_id;
        if ($tid) {
            $wpdb->insert($wpdb->prefix . 'dm_ticket_messages', [
                'ticket_id'  => $tid,
                'sender'     => 'user',
                'message'    => sanitize_textarea_field($message),
                'created_at' => current_time('mysql'),
            ]);
        }
        return $tid ?: false;
    }

    public static function reply(int $ticket_id, string $message, string $sender): void {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'dm_ticket_messages', [
            'ticket_id'  => $ticket_id,
            'sender'     => $sender,
            'message'    => sanitize_textarea_field($message),
            'created_at' => current_time('mysql'),
        ]);
        if ($sender === 'admin') {
            $wpdb->update($wpdb->prefix . 'dm_tickets',
                ['status' => 'answered'], ['id' => $ticket_id]);
        }
    }

    public static function get(int $id): ?array {
        global $wpdb;
        $r = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}dm_tickets WHERE id=%d", $id),
            ARRAY_A
        );
        return $r ?: null;
    }

    public static function messages(int $ticket_id): array {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}dm_ticket_messages WHERE ticket_id=%d ORDER BY id",
                $ticket_id
            ), ARRAY_A
        );
    }

    public static function user_tickets(int $user_id): array {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}dm_tickets WHERE user_id=%d ORDER BY id DESC",
                $user_id
            ), ARRAY_A
        );
    }

    public static function all(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT t.*, u.name AS display_name, u.email AS user_email
             FROM {$wpdb->prefix}dm_tickets t
             LEFT JOIN {$wpdb->prefix}dm_users u ON t.user_id = u.id
             ORDER BY t.id DESC",
            ARRAY_A
        );
    }
}
