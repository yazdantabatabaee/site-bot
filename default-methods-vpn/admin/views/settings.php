<?php defined('ABSPATH') || exit; ?>
<div class="wrap dm-admin">
  <h1>⚙️ Settings — Default Methods VPN</h1>

  <?php if (isset($_GET['saved'])): ?>
    <div class="notice notice-success is-dismissible"><p>✅ Settings saved.</p></div>
  <?php endif; ?>
  <?php if (isset($_GET['test'])): ?>
    <div class="notice notice-<?= $_GET['test']==='ok'?'success':'error' ?> is-dismissible">
      <p><?= esc_html(urldecode($_GET['msg'] ?? '')) ?></p>
    </div>
  <?php endif; ?>

  <!-- HOW IT WORKS -->
  <div style="background:#fff8e1;border:1px solid #f59e0b;border-radius:10px;padding:18px 22px;max-width:960px;margin-bottom:24px">
    <h3 style="margin:0 0 8px;color:#92400e">📐 نحوه عملکرد</h3>
    <p style="font-size:.88rem;color:#78350f;margin:0;line-height:1.8">
      سرور وردپرس نمی‌تواند مستقیم به پنل 3x-ui متصل شود چون در شبکه‌های مختلف هستند.<br>
      به جای آن، یک <strong>Webhook Server</strong> روی سرور بات (همان سرور پنل) اجرا می‌کنیم.<br>
      وردپرس → Webhook روی سرور بات → پنل 3x-ui
    </p>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:960px">

    <!-- WEBHOOK -->
    <div style="background:#fff;border:1px solid #ddd;border-radius:12px;padding:24px">
      <h2 style="margin:0 0 4px;font-size:1rem;color:#6366f1">🔌 Webhook Connection</h2>
      <p style="font-size:.82rem;color:#888;margin:0 0 18px">
        آدرس و secret باید با <code>.env</code> سرور بات یکی باشند.
        بعد از ذخیره، دکمه Test را بزنید.
      </p>
      <form method="post" action="<?= admin_url('admin-post.php') ?>">
        <?php wp_nonce_field('dm_settings'); ?>
        <input type="hidden" name="action" value="dm_settings">

        <div style="margin-bottom:16px">
          <label style="display:block;font-weight:600;margin-bottom:5px;font-size:.85rem">
            Webhook URL
            <span style="font-weight:400;color:#888"> (آدرس سرور بات + /webhook)</span>
          </label>
          <input type="url" name="dm_webhook_url"
                 value="<?= esc_attr(get_option('dm_webhook_url','')) ?>"
                 placeholder="https://your-bot-server.com:8443/webhook"
                 style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;direction:ltr;font-family:monospace;font-size:.88rem">
          <p style="font-size:.78rem;color:#888;margin-top:5px">
            مثال: <code>https://cp.defaultmethods.ir:8443/webhook</code>
          </p>
        </div>

        <div style="margin-bottom:18px">
          <label style="display:block;font-weight:600;margin-bottom:5px;font-size:.85rem">
            Webhook Secret
            <span style="font-weight:400;color:#888"> (همان مقدار WEBHOOK_SECRET در .env بات)</span>
          </label>
          <input type="text" name="dm_webhook_secret"
                 value="<?= esc_attr(get_option('dm_webhook_secret','')) ?>"
                 placeholder="da155c7916a5bfcd..."
                 style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;direction:ltr;font-family:monospace;font-size:.82rem">
        </div>

        <div style="display:flex;gap:10px">
          <button type="submit" class="button button-primary">💾 Save</button>
        </div>
      </form>

      <!-- Test -->
      <form method="post" action="<?= admin_url('admin-post.php') ?>"
            style="margin-top:14px;padding-top:14px;border-top:1px solid #eee">
        <?php wp_nonce_field('dm_test_api'); ?>
        <input type="hidden" name="action" value="dm_test_api">
        <button type="submit" class="button">🔌 Test Webhook Connection</button>
      </form>
    </div>

    <!-- PAYMENT & INBOUNDS -->
    <div style="background:#fff;border:1px solid #ddd;border-radius:12px;padding:24px">
      <h2 style="margin:0 0 18px;font-size:1rem;color:#10b981">💳 Payment Settings</h2>
      <form method="post" action="<?= admin_url('admin-post.php') ?>">
        <?php wp_nonce_field('dm_settings'); ?>
        <input type="hidden" name="action"           value="dm_settings">
        <input type="hidden" name="dm_webhook_url"    value="<?= esc_attr(get_option('dm_webhook_url','')) ?>">
        <input type="hidden" name="dm_webhook_secret" value="<?= esc_attr(get_option('dm_webhook_secret','')) ?>">

        <div style="margin-bottom:14px">
          <label style="display:block;font-weight:600;margin-bottom:5px;font-size:.85rem">Card Number</label>
          <input type="text" name="dm_card_number"
                 value="<?= esc_attr(get_option('dm_card_number','')) ?>"
                 placeholder="6219-XXXX-XXXX-XXXX"
                 style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;direction:ltr;font-family:monospace">
        </div>
        <div style="margin-bottom:20px">
          <label style="display:block;font-weight:600;margin-bottom:5px;font-size:.85rem">Card Owner</label>
          <input type="text" name="dm_card_owner"
                 value="<?= esc_attr(get_option('dm_card_owner','')) ?>"
                 placeholder="Yazdan Tabatabaee"
                 style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px">
        </div>

        <label style="display:block;font-weight:600;margin-bottom:5px;font-size:.85rem">
          Inbound IDs Map (JSON)
        </label>
        <textarea name="dm_inbound_map" rows="9"
          style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:monospace;font-size:.8rem;direction:ltr"><?= esc_textarea(get_option('dm_inbound_map', json_encode([
            'armenia' => [8,9,13,18,28],
            'germany' => [49,50,51,52,53],
            'turkey'  => [93,94,95,96,97],
            'finland' => [98,99,100,101,102],
            'all'     => [8,9,13,18,28,49,50,51,52,53,93,94,95,96,97,98,99,100,101,102],
          ], JSON_PRETTY_PRINT))) ?></textarea>

        <button type="submit" class="button button-primary" style="margin-top:12px">💾 Save</button>
      </form>
    </div>
  </div>

  <!-- PAGES SETUP -->
  <div style="max-width:960px;margin-top:24px;background:#fffbeb;border:1px solid #f59e0b;border-radius:12px;padding:20px">
    <h3 style="margin:0 0 12px;color:#92400e">📄 Required WordPress Pages — Elementor Pro</h3>
    <table style="border-collapse:collapse;width:100%;font-size:.88rem">
      <tr style="background:#fef3c7">
        <th style="padding:8px 14px;text-align:left;border:1px solid #f59e0b">Page Title</th>
        <th style="padding:8px 14px;text-align:left;border:1px solid #f59e0b">URL Slug</th>
        <th style="padding:8px 14px;text-align:left;border:1px solid #f59e0b">Shortcode</th>
        <th style="padding:8px 14px;text-align:left;border:1px solid #f59e0b">Status</th>
      </tr>
      <?php
      $pages = [
        ['Shop',            'shop',      '[dm_shop]'],
        ['Dashboard',       'dashboard', '[dm_dashboard]'],
        ['Support Tickets', 'tickets',   '[dm_tickets]'],
        ['Login',           'login',     '[dm_login]'],
        ['Register',        'register',  '[dm_register]'],
      ];
      foreach ($pages as [$title, $slug, $sc]):
        $page = get_page_by_path($slug);
      ?>
        <tr>
          <td style="padding:8px 14px;border:1px solid #f59e0b"><?= $title ?></td>
          <td style="padding:8px 14px;border:1px solid #f59e0b;font-family:monospace">/<?= $slug ?>/</td>
          <td style="padding:8px 14px;border:1px solid #f59e0b"><code><?= $sc ?></code></td>
          <td style="padding:8px 14px;border:1px solid #f59e0b">
            <?php if ($page): ?>
              <a href="<?= get_permalink($page->ID) ?>" target="_blank"
                 style="color:#10b981;font-weight:600">✅ Active</a>
            <?php else: ?>
              <a href="<?= admin_url('post-new.php?post_type=page') ?>"
                 style="color:#ef4444;font-weight:600">❌ Create page</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- DEBUG LINK -->
  <div style="max-width:960px;margin-top:16px">
    <a href="<?= admin_url('admin.php?page=dm-vpn-log') ?>" class="button">
      🐛 View Debug Log
    </a>
    <span style="font-size:.82rem;color:#888;margin-right:10px">
      اگر کلاینت ساخته نشد اینجا ببینید چرا.
    </span>
  </div>
</div>
