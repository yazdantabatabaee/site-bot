<?php
defined('ABSPATH') || exit;

class DM_Database {
    public static function install(): void {
        global $wpdb;
        $c = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        /* ── کاربران اختصاصی سایت (مستقل از WP) ── */
        dbDelta("CREATE TABLE {$wpdb->prefix}dm_users (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(120) DEFAULT '',
            mobile varchar(20) DEFAULT '',
            password_hash varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY mobile (mobile)
        ) $c;");

        /* ── نشست‌های ورود ── */
        dbDelta("CREATE TABLE {$wpdb->prefix}dm_sessions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            token varchar(64) NOT NULL,
            expires_at datetime NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY token (token)
        ) $c;");

        dbDelta("CREATE TABLE {$wpdb->prefix}dm_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL DEFAULT 0,
            user_email varchar(100) DEFAULT '',
            plan varchar(20) NOT NULL,
            price int(11) NOT NULL DEFAULT 0,
            country varchar(60) DEFAULT '',
            account_email varchar(100) DEFAULT '',
            final_email varchar(120) DEFAULT '',
            sub_url text DEFAULT '',
            configs longtext DEFAULT '',
            receipt_url text DEFAULT '',
            status varchar(30) NOT NULL DEFAULT 'pending_payment',
            notes text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $c;");

        dbDelta("CREATE TABLE {$wpdb->prefix}dm_tickets (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            order_id bigint(20) DEFAULT 0,
            subject varchar(200) NOT NULL,
            status varchar(20) DEFAULT 'open',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $c;");

        dbDelta("CREATE TABLE {$wpdb->prefix}dm_ticket_messages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ticket_id bigint(20) NOT NULL,
            sender varchar(10) NOT NULL,
            message text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY ticket_id (ticket_id)
        ) $c;");

        dbDelta("CREATE TABLE {$wpdb->prefix}dm_renewals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            order_id bigint(20) NOT NULL,
            receipt_url text DEFAULT '',
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $c;");
    }
}
