<?php defined('ABSPATH') || exit; ?>
<div class="wrap dm-admin">
  <h1>🎫 مدیریت تیکت‌ها</h1>
  <table class="wp-list-table widefat fixed striped" style="margin-top:16px">
    <thead>
      <tr><th>ID</th><th>کاربر</th><th>موضوع</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $t): ?>
        <tr>
          <td><strong>#<?= $t['id'] ?></strong></td>
          <td><?= esc_html($t['display_name'] ?? $t['user_email'] ?? '-') ?></td>
          <td><?= esc_html($t['subject']) ?></td>
          <td><?= ['open'=>'🟡 باز','answered'=>'🟢 پاسخ داده شده','closed'=>'⚫ بسته'][$t['status']] ?? $t['status'] ?></td>
          <td><?= esc_html(substr($t['created_at'],0,10)) ?></td>
          <td><a href="?page=dm-vpn-tickets&tid=<?= $t['id'] ?>" class="button">💬 پاسخ</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($tickets)): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#666">هیچ تیکتی یافت نشد</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
