<?php defined('ABSPATH') || exit; ?>
<div class="wrap">
  <h1>🐛 Debug Log — DM VPN</h1>
  <?php if (isset($_GET['cleared'])): ?>
    <div class="notice notice-success is-dismissible"><p>✅ Log cleared.</p></div>
  <?php endif; ?>
  <p style="color:#666">آخرین ۵۰ رویداد API. برای رفع مشکل اتصال به 3x-ui استفاده کنید.</p>
  <form method="post" action="<?= admin_url('admin-post.php') ?>" style="margin-bottom:16px">
    <?php wp_nonce_field('dm_clear_log'); ?>
    <input type="hidden" name="action" value="dm_clear_log">
    <button class="button button-secondary">🗑 پاک کردن Log</button>
  </form>
  <div style="background:#1e1e1e;color:#d4d4d4;padding:20px;border-radius:8px;font-family:monospace;font-size:.83rem;line-height:1.7;max-height:600px;overflow-y:auto">
    <?php if (empty($logs)): ?>
      <span style="color:#6a9955">// No logs yet. Try approving an order.</span>
    <?php else: ?>
      <?php foreach ($logs as $line):
        $color = str_contains($line,'error') || str_contains($line,'FAIL') || str_contains($line,'failed')
          ? '#f48771' : (str_contains($line,'OK') || str_contains($line,'found') ? '#6a9955' : '#9cdcfe');
      ?>
        <div style="color:<?= $color ?>"><?= esc_html($line) ?></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
