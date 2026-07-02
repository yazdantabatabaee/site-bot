<?php
defined('ABSPATH') || exit;

/**
 * DM_Auth — سیستم احراز هویت مستقل از کاربران وردپرس.
 * کاربران در جدول اختصاصی wp_dm_users ذخیره می‌شوند و
 * نشست (session) با یک کوکی امن + توکن در دیتابیس مدیریت می‌شود.
 */
class DM_Auth {

    const COOKIE_NAME = 'dm_session';
    const COOKIE_DAYS = 30;

    private static ?array $current_user = null;
    private static bool   $resolved     = false;

    /* ══════════════════════════════════════
       ثبت‌نام
    ══════════════════════════════════════ */
    public static function register(string $name, string $mobile,
                                     string $email, string $password): array {
        global $wpdb;

        $name  = sanitize_text_field($name);
        $email = strtolower(sanitize_email($email));
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        if (mb_strlen($name) < 2)
            return ['ok'=>false,'error'=>'نام باید حداقل ۲ کاراکتر باشد'];
        if ($email && !is_email($email))
            return ['ok'=>false,'error'=>'ایمیل نامعتبر است'];
        if (!$email && !$mobile)
            return ['ok'=>false,'error'=>'ایمیل یا شماره موبایل الزامی است'];
        if (mb_strlen($password) < 6)
            return ['ok'=>false,'error'=>'رمز عبور باید حداقل ۶ کاراکتر باشد'];

        // بررسی تکراری نبودن
        if ($email) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}dm_users WHERE email=%s", $email));
            if ($exists) return ['ok'=>false,'error'=>'این ایمیل قبلاً ثبت شده است'];
        }
        if ($mobile) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}dm_users WHERE mobile=%s", $mobile));
            if ($exists) return ['ok'=>false,'error'=>'این شماره موبایل قبلاً ثبت شده است'];
        }

        $ok = $wpdb->insert($wpdb->prefix.'dm_users', [
            'name'          => $name,
            'email'         => $email,
            'mobile'        => $mobile,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'created_at'    => current_time('mysql'),
        ]);

        if (!$ok) return ['ok'=>false,'error'=>'خطای دیتابیس، دوباره تلاش کنید'];

        $uid = $wpdb->insert_id;
        self::start_session($uid);

        return ['ok'=>true,'user_id'=>$uid];
    }

    /* ══════════════════════════════════════
       ورود
    ══════════════════════════════════════ */
    public static function login(string $identity, string $password): array {
        global $wpdb;
        $identity = trim($identity);
        $field    = is_email($identity) ? 'email' : 'mobile';
        $clean    = $field === 'mobile' ? preg_replace('/[^0-9]/', '', $identity) : strtolower($identity);

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dm_users WHERE {$field}=%s", $clean
        ), ARRAY_A);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['ok'=>false,'error'=>'ایمیل/موبایل یا رمز عبور اشتباه است'];
        }

        self::start_session((int)$user['id']);
        return ['ok'=>true,'user_id'=>(int)$user['id']];
    }

    /* ══════════════════════════════════════
       نشست (Session)
    ══════════════════════════════════════ */
    private static function start_session(int $user_id): void {
        global $wpdb;
        $token   = bin2hex(random_bytes(32));
        $expires = time() + self::COOKIE_DAYS * DAY_IN_SECONDS;

        $wpdb->insert($wpdb->prefix.'dm_sessions', [
            'user_id'    => $user_id,
            'token'      => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', $expires),
            'created_at' => current_time('mysql'),
        ]);

        setcookie(self::COOKIE_NAME, $token, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        self::$current_user = null;
        self::$resolved     = false;
    }

    public static function logout(): void {
        global $wpdb;
        $token = $_COOKIE[self::COOKIE_NAME] ?? '';
        if ($token) {
            $wpdb->delete($wpdb->prefix.'dm_sessions', ['token' => hash('sha256', $token)]);
        }
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
        self::$current_user = null;
        self::$resolved     = true;
    }

    /* ══════════════════════════════════════
       کاربر جاری
    ══════════════════════════════════════ */
    public static function current(): ?array {
        if (self::$resolved) return self::$current_user;
        self::$resolved = true;

        $token = $_COOKIE[self::COOKIE_NAME] ?? '';
        if (!$token) return null;

        global $wpdb;
        $hash = hash('sha256', $token);
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT s.user_id, u.name, u.email, u.mobile
             FROM {$wpdb->prefix}dm_sessions s
             JOIN {$wpdb->prefix}dm_users u ON s.user_id = u.id
             WHERE s.token=%s AND s.expires_at > NOW()",
            $hash
        ), ARRAY_A);

        if (!$row) return null;

        self::$current_user = [
            'id'     => (int)$row['user_id'],
            'name'   => $row['name'],
            'email'  => $row['email'],
            'mobile' => $row['mobile'],
        ];
        return self::$current_user;
    }

    public static function is_logged_in(): bool {
        return self::current() !== null;
    }

    public static function user_id(): int {
        return self::current()['id'] ?? 0;
    }

    /* ══════════════════════════════════════
       Verify request auth (for REST) — از کوکی می‌خواند
    ══════════════════════════════════════ */
    public static function require_login(): ?WP_REST_Response {
        if (!self::is_logged_in()) {
            return new WP_REST_Response(['error' => 'لطفاً وارد حساب کاربری شوید'], 401);
        }
        return null;
    }
}
