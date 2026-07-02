<?php
defined('ABSPATH') || exit;

/**
 * این کلاس دیگر مستقیم به پنل 3x-ui وصل نمی‌شود.
 * چون سرور وردپرس و سرور پنل جدا هستند، به جای آن
 * یک Webhook روی سرور بات/پنل صدا زده می‌شود.
 */
class DM_XUI_API {

    private string $webhook_url;
    private string $secret;
    private array  $last_error = [];

    public function __construct() {
        $this->webhook_url = rtrim(get_option('dm_webhook_url', ''), '/');
        $this->secret       = get_option('dm_webhook_secret', '');
    }

    private function log(string $msg): void {
        $this->last_error[] = $msg;
        error_log('[DM-VPN] ' . $msg);
        $logs = (array) get_option('dm_debug_log', []);
        array_unshift($logs, date('Y-m-d H:i:s') . ' — ' . $msg);
        update_option('dm_debug_log', array_slice($logs, 0, 50));
    }

    public function get_last_errors(): array { return $this->last_error; }

    /* ── امضای HMAC برای امنیت ───────────────── */
    private function sign(string $body): string {
        return hash_hmac('sha256', $body, $this->secret);
    }

    private function call(string $path, array $payload): array|false {
        if (!$this->webhook_url || !$this->secret) {
            $this->log('Webhook URL or Secret is not configured');
            return false;
        }
        $body = wp_json_encode($payload);
        $resp = wp_remote_post($this->webhook_url . $path, [
            'timeout' => 60,   // ساخت کلاینت ممکن است چند ثانیه طول بکشد
            'headers' => [
                'Content-Type'     => 'application/json',
                'X-DM-Signature'   => $this->sign($body),
            ],
            'body' => $body,
            'sslverify' => false,
        ]);

        if (is_wp_error($resp)) {
            $this->log('Webhook error: ' . $resp->get_error_message());
            return false;
        }
        $code = wp_remote_retrieve_response_code($resp);
        $data = json_decode(wp_remote_retrieve_body($resp), true);

        if ($code !== 200 || empty($data['success'])) {
            $this->log('Webhook failed [' . $code . ']: ' . ($data['error'] ?? 'unknown'));
            return false;
        }
        $this->log('Webhook call OK: ' . $path);
        return $data;
    }

    /* ── Test connection ─────────────────────── */
    public function test_connection(): array {
        if (!$this->webhook_url) {
            return ['ok' => false, 'msg' => 'Webhook URL is empty'];
        }
        $resp = wp_remote_get(rtrim($this->webhook_url, '/') . '/health', [
            'timeout' => 10, 'sslverify' => false,
        ]);
        if (is_wp_error($resp)) {
            return ['ok' => false, 'msg' => 'Connection failed: ' . $resp->get_error_message()];
        }
        $code = wp_remote_retrieve_response_code($resp);
        if ($code === 200) {
            return ['ok' => true, 'msg' => 'Webhook reachable ✅'];
        }
        return ['ok' => false, 'msg' => "Webhook returned HTTP {$code}"];
    }

    /* ── Full provision (create + subId + links) ── */
    public function provision_client(string $email, array $inbound_ids,
                                      int $ip_limit, int $days): array {
        // پیدا کردن country از روی inbound_ids کار اضافه‌ای است؛
        // به جای آن مستقیم email + country را در admin بفرستیم.
        // این متد توسط admin class با $country واقعی صدا زده می‌شود — پایین override شده.
        return $this->provision_with_country($email, '', $ip_limit, $days);
    }

    public function provision_with_country(string $email, string $country,
                                            int $ip_limit, int $days): array {
        $this->log("provision START: email={$email} country={$country}");

        $result = $this->call('/vpn', [
            'action'   => 'create_client',
            'email'    => $email,
            'country'  => $country,
            'ip_limit' => $ip_limit,
            'days'     => $days,
        ]);

        if ($result === false) {
            return ['success' => false, 'error' => implode(' | ', $this->last_error)];
        }

        $this->log("provision END: sub_url=" . ($result['sub_url'] ?? '') .
                   " links=" . count($result['links'] ?? []));

        return [
            'success' => true,
            'sub_id'  => $result['sub_id']  ?? '',
            'sub_url' => $result['sub_url'] ?? '',
            'links'   => $result['links']   ?? [],
        ];
    }

    /* ── Renew ────────────────────────────────── */
    public function renew_client(string $email, int $days = 30): bool {
        $result = $this->call('/vpn', [
            'action' => 'renew_client',
            'email'  => $email,
            'days'   => $days,
        ]);
        return $result !== false;
    }

    /* ── Inbound IDs (فقط برای نمایش/مرجع؛ منطق واقعی روی بات است) ── */
    public static function inbound_ids(string $country): array {
        $map = json_decode(get_option('dm_inbound_map', ''), true);
        if (empty($map) || !is_array($map)) {
            $map = [
                'armenia' => [8,9,13,18,28],
                'germany' => [49,50,51,52,53],
                'turkey'  => [93,94,95,96,97],
                'finland' => [98,99,100,101,102],
                'all'     => [8,9,13,18,28,49,50,51,52,53,93,94,95,96,97,98,99,100,101,102],
            ];
        }
        $key = strtolower(explode(' ', trim($country))[0]);
        return $map[$key] ?? $map['armenia'] ?? [];
    }

    public static function link_name(string $link): string {
        if (str_contains($link, '#')) {
            return urldecode(explode('#', $link, 2)[1]);
        }
        return 'Config';
    }

    /* این متد دیگر استفاده نمی‌شود (کانفیگ‌ها مستقیم از webhook می‌آیند) */
    public function fetch_sub_links(string $sub_url): array {
        return [];
    }
}
