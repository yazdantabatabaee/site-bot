<?php
defined('ABSPATH') || exit;

class DM_Public {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_shortcode('dm_shop',      [$this, 'sc_shop']);
        add_shortcode('dm_dashboard', [$this, 'sc_dashboard']);
        add_shortcode('dm_tickets',   [$this, 'sc_tickets']);
        add_shortcode('dm_login',     [$this, 'sc_login']);
        add_shortcode('dm_register',  [$this, 'sc_register']);
        add_action('rest_api_init',   [$this, 'routes']);
        add_action('init',            [$this, 'handle_logout']);
    }

    public function assets(): void {
        wp_enqueue_style('dm-vpn',
            DM_VPN_URL . 'public/css/dm-public.css', [], DM_VPN_VERSION);
        wp_enqueue_script('qrcodejs',
            'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js',
            [], null, true);
        wp_enqueue_script('dm-vpn',
            DM_VPN_URL . 'public/js/dm-public.js',
            ['jquery','qrcodejs'], DM_VPN_VERSION, true);

        $user = DM_Auth::current();
        wp_localize_script('dm-vpn', 'DM', [
            'rest'   => esc_url_raw(rest_url('dm/v1/')),
            'nonce'  => wp_create_nonce('wp_rest'),
            'logged' => (bool) $user,
            'user'   => $user,
            'loginUrl'    => home_url('/login/'),
            'dashboardUrl'=> home_url('/dashboard/'),
        ]);
    }

    /* ── logout via query param ?dm_logout=1 ── */
    public function handle_logout(): void {
        if (isset($_GET['dm_logout'])) {
            DM_Auth::logout();
            wp_redirect(home_url('/login/'));
            exit;
        }
    }

    /* ══════════════════════════════════════
       SHORTCODES
    ══════════════════════════════════════ */
    public function sc_shop(): string {
        ob_start(); include DM_VPN_PATH.'templates/shop.php'; return ob_get_clean();
    }

    public function sc_dashboard(): string {
        if (!DM_Auth::is_logged_in()) {
            ob_start(); include DM_VPN_PATH.'templates/login.php'; return ob_get_clean();
        }
        ob_start(); include DM_VPN_PATH.'templates/dashboard.php'; return ob_get_clean();
    }

    public function sc_tickets(): string {
        if (!DM_Auth::is_logged_in()) {
            ob_start(); include DM_VPN_PATH.'templates/login.php'; return ob_get_clean();
        }
        ob_start(); include DM_VPN_PATH.'templates/tickets.php'; return ob_get_clean();
    }

    public function sc_login(): string {
        if (DM_Auth::is_logged_in()) {
            wp_redirect(home_url('/dashboard/')); exit;
        }
        ob_start(); include DM_VPN_PATH.'templates/login.php'; return ob_get_clean();
    }

    public function sc_register(): string {
        if (DM_Auth::is_logged_in()) {
            wp_redirect(home_url('/dashboard/')); exit;
        }
        ob_start(); include DM_VPN_PATH.'templates/register.php'; return ob_get_clean();
    }

    /* ══════════════════════════════════════
       REST ROUTES
    ══════════════════════════════════════ */
    public function routes(): void {
        $open = '__return_true';
        register_rest_route('dm/v1','/register',['methods'=>'POST','callback'=>[$this,'api_register'],'permission_callback'=>$open]);
        register_rest_route('dm/v1','/login',   ['methods'=>'POST','callback'=>[$this,'api_login'],   'permission_callback'=>$open]);
        register_rest_route('dm/v1','/logout',  ['methods'=>'POST','callback'=>[$this,'api_logout'],  'permission_callback'=>$open]);

        register_rest_route('dm/v1','/order',   ['methods'=>'POST','callback'=>[$this,'api_order'],   'permission_callback'=>$open]);
        register_rest_route('dm/v1','/receipt', ['methods'=>'POST','callback'=>[$this,'api_receipt'], 'permission_callback'=>$open]);
        register_rest_route('dm/v1','/orders',  ['methods'=>'GET', 'callback'=>[$this,'api_orders'],  'permission_callback'=>$open]);
        register_rest_route('dm/v1','/ticket',  ['methods'=>'POST','callback'=>[$this,'api_ticket'],  'permission_callback'=>$open]);
        register_rest_route('dm/v1','/ticket/(?P<id>\d+)',['methods'=>'GET','callback'=>[$this,'api_ticket_get'],'permission_callback'=>$open]);
        register_rest_route('dm/v1','/renew',   ['methods'=>'POST','callback'=>[$this,'api_renew'],   'permission_callback'=>$open]);
    }

    /* ── ثبت‌نام ────────────────────────── */
    public function api_register(WP_REST_Request $r): WP_REST_Response {
        $res = DM_Auth::register(
            $r->get_param('name') ?? '',
            $r->get_param('mobile') ?? '',
            $r->get_param('email') ?? '',
            $r->get_param('password') ?? ''
        );
        if (!$res['ok']) return new WP_REST_Response(['error'=>$res['error']], 400);
        return new WP_REST_Response(['success'=>true]);
    }

    /* ── ورود ───────────────────────────── */
    public function api_login(WP_REST_Request $r): WP_REST_Response {
        $res = DM_Auth::login(
            $r->get_param('identity') ?? '',
            $r->get_param('password') ?? ''
        );
        if (!$res['ok']) return new WP_REST_Response(['error'=>$res['error']], 401);
        return new WP_REST_Response(['success'=>true]);
    }

    /* ── خروج ───────────────────────────── */
    public function api_logout(): WP_REST_Response {
        DM_Auth::logout();
        return new WP_REST_Response(['success'=>true]);
    }

    /* ── ثبت سفارش ─────────────────────── */
    public function api_order(WP_REST_Request $r): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $uid = DM_Auth::user_id();

        $plan    = sanitize_text_field($r->get_param('plan'));
        $country = sanitize_text_field($r->get_param('country'));
        $email_b = sanitize_text_field($r->get_param('account_email'));
        $plans   = DM_Orders::plans();

        if (!isset($plans[$plan]))
            return new WP_REST_Response(['error'=>'پلن نامعتبر'],400);
        if (!preg_match('/^[a-z0-9][a-z0-9._-]{2,29}$/', $email_b))
            return new WP_REST_Response(['error'=>'نام اکانت نامعتبر — فقط حروف کوچک انگلیسی، عدد، نقطه و خط تیره'],400);

        $oid = DM_Orders::create($uid, [
            'plan'=>$plan, 'price'=>$plans[$plan]['price'],
            'country'=>$country, 'account_email'=>$email_b,
        ]);
        if (!$oid) return new WP_REST_Response(['error'=>'خطای دیتابیس'],500);

        $final = DM_Orders::make_final_email($email_b, $oid);
        DM_Orders::update($oid, ['final_email'=>$final]);

        wp_mail(get_option('admin_email'),
            "سفارش جدید #{$oid} — Default Methods",
            "پلن: {$plans[$plan]['name']}\nلوکیشن: {$country}\nEmail: {$final}\n\n".
            admin_url('admin.php?page=dm-vpn-orders'));

        return new WP_REST_Response([
            'success'     => true,
            'order_id'    => $oid,
            'final_email' => $final,
            'price'       => $plans[$plan]['price'],
            'plan_name'   => $plans[$plan]['name'],
            'card_number' => get_option('dm_card_number',''),
            'card_owner'  => get_option('dm_card_owner',''),
        ]);
    }

    /* ── آپلود رسید ─────────────────────── */
    public function api_receipt(WP_REST_Request $r): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $uid = DM_Auth::user_id();

        $oid   = intval($r->get_param('order_id'));
        $order = DM_Orders::get($oid);
        if (!$order || (int)$order['user_id'] !== $uid)
            return new WP_REST_Response(['error'=>'سفارش یافت نشد'],404);
        if (empty($_FILES['receipt']))
            return new WP_REST_Response(['error'=>'فایل ارسال نشد'],400);

        require_once ABSPATH.'wp-admin/includes/file.php';
        require_once ABSPATH.'wp-admin/includes/media.php';
        require_once ABSPATH.'wp-admin/includes/image.php';

        $aid = media_handle_upload('receipt', 0);
        if (is_wp_error($aid))
            return new WP_REST_Response(['error'=>$aid->get_error_message()],500);

        $url = wp_get_attachment_url($aid);
        DM_Orders::update($oid,['receipt_url'=>$url,'status'=>'pending_review']);

        $user = DM_Auth::current();
        wp_mail(get_option('admin_email'),
            "رسید جدید — سفارش #{$oid}",
            "کاربر: {$user['name']}\nEmail اکانت: {$order['final_email']}\n".
            "رسید: {$url}\n\n".admin_url('admin.php?page=dm-vpn-orders'));

        return new WP_REST_Response(['success'=>true]);
    }

    /* ── سفارش‌های کاربر ─────────────────── */
    public function api_orders(): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $orders = DM_Orders::get_user_orders(DM_Auth::user_id());
        $plans  = DM_Orders::plans();
        foreach ($orders as &$o) {
            $o['plan_name']    = $plans[$o['plan']]['name'] ?? $o['plan'];
            $o['status_label'] = DM_Orders::status_label($o['status']);
            $o['configs']      = $o['configs'] ? json_decode($o['configs'],true) : [];
        }
        return new WP_REST_Response($orders);
    }

    /* ── ثبت تیکت ───────────────────────── */
    public function api_ticket(WP_REST_Request $r): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $uid = DM_Auth::user_id();

        $subject  = sanitize_text_field($r->get_param('subject'));
        $message  = sanitize_textarea_field($r->get_param('message'));
        $order_id = intval($r->get_param('order_id') ?? 0);
        if (!$subject || !$message)
            return new WP_REST_Response(['error'=>'موضوع و پیام الزامی است'],400);

        $tid = DM_Tickets::create($uid,$subject,$message,$order_id);
        if (!$tid) return new WP_REST_Response(['error'=>'خطای دیتابیس'],500);

        $user = DM_Auth::current();
        wp_mail(get_option('admin_email'),
            "تیکت جدید #{$tid}: {$subject}",
            "از: {$user['name']}\n\n{$message}\n\n".
            admin_url("admin.php?page=dm-vpn-tickets&tid={$tid}"));

        return new WP_REST_Response(['success'=>true,'ticket_id'=>$tid]);
    }

    /* ── دریافت تیکت ────────────────────── */
    public function api_ticket_get(WP_REST_Request $r): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $tid    = intval($r->get_param('id'));
        $ticket = DM_Tickets::get($tid);
        if (!$ticket || (int)$ticket['user_id'] !== DM_Auth::user_id())
            return new WP_REST_Response(['error'=>'تیکت یافت نشد'],404);
        return new WP_REST_Response([
            'ticket'   => $ticket,
            'messages' => DM_Tickets::messages($tid),
        ]);
    }

    /* ── درخواست تمدید ─────────────────── */
    public function api_renew(WP_REST_Request $r): WP_REST_Response {
        if ($err = DM_Auth::require_login()) return $err;
        $uid = DM_Auth::user_id();
        global $wpdb;

        $oid   = intval($r->get_param('order_id'));
        $order = DM_Orders::get($oid);
        if (!$order || (int)$order['user_id'] !== $uid)
            return new WP_REST_Response(['error'=>'سفارش یافت نشد'],404);
        if (empty($_FILES['receipt']))
            return new WP_REST_Response(['error'=>'رسید ارسال نشد'],400);

        require_once ABSPATH.'wp-admin/includes/file.php';
        require_once ABSPATH.'wp-admin/includes/media.php';
        require_once ABSPATH.'wp-admin/includes/image.php';
        $aid = media_handle_upload('receipt',0);
        $url = is_wp_error($aid)?'':wp_get_attachment_url($aid);

        $wpdb->insert($wpdb->prefix.'dm_renewals',[
            'user_id'=>$uid,'order_id'=>$oid,
            'receipt_url'=>$url,'status'=>'pending','created_at'=>current_time('mysql'),
        ]);
        $rid = $wpdb->insert_id;

        $user = DM_Auth::current();
        wp_mail(get_option('admin_email'),
            "درخواست تمدید #{$rid}",
            "سفارش #{$oid}\nEmail: {$order['final_email']}\nرسید: {$url}\n\n".
            admin_url('admin.php?page=dm-vpn-renewals'));

        return new WP_REST_Response(['success'=>true]);
    }
}
