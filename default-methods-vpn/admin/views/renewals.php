<?php defined('ABSPATH') || exit; ?>
<div class="wrap dm-admin">
  <h1>🔄 درخواست‌های تمدید</h1>
  <?php if (isset($_GET['msg'])): ?>
    <div class="notice notice-<?= $_GET['msg']==='ok'?'success':'error' ?> is-dismissible">
      <p><?= $_GET['msg']==='ok'?'✅ تمدید اعمال شد':($_GET['msg']==='rejected'?'❌ رد شد':'⚠️ خطا در پنل') ?></p>
    </div>
  <?php endif; ?>
  <table class="wp-list-table widefat fixed striped" style="margin-top:16px">
    <thead>
      <tr><th>ID</th><th>کاربر</th><th>سفارش</th><th>ایمیل اکانت</th><th>رسید</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
    </thead>
    <tbody>
      <?php foreach ($renewals as $r): ?>
        <tr>
          <td>#<?= $r['id'] ?></td>
          <td><?= esc_html($r['display_name'] ?? '-') ?></td>
          <td>#<?= $r['order_id'] ?></td>
          <td style="direction:ltr;font-size:.85rem"><?= esc_html($r['final_email'] ?? '-') ?></td>
          <td><?php if ($r['receipt_url']): ?><a href="<?= esc_url($r['receipt_url']) ?>" target="_blank">🖼 مشاهده</a><?php else: ?>-<?php endif; ?></td>
          <td><?= ['pending'=>'⏳ در انتظار','approved'=>'✅ تایید','rejected'=>'❌ رد','xui_error'=>'⚠️ خطا'][$r['status']] ?? $r['status'] ?></td>
          <td><?= esc_html(substr($r['created_at'],0,10)) ?></td>
          <td>
            <?php if ($r['status'] === 'pending'): ?>
              <form method="post" action="<?= admin_url('admin-post.php') ?>" style="display:inline">
                <?php wp_nonce_field('dm_renew'); ?>
                <input type="hidden" name="action"     value="dm_renew_approve">
                <input type="hidden" name="renewal_id" value="<?= $r['id'] ?>">
                <button class="button button-primary" onclick="return confirm('تمدید اعمال شود؟')">✅ تایید</button>
              </form>
              <form method="post" action="<?= admin_url('admin-post.php') ?>" style="display:inline">
                <?php wp_nonce_field('dm_renew'); ?>
                <input type="hidden" name="action"     value="dm_renew_reject">
                <input type="hidden" name="renewal_id" value="<?= $r['id'] ?>">
                <button class="button">❌ رد</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($renewals)): ?>
        <tr><td colspan="8" style="text-align:center;padding:24px;color:#666">هیچ درخواستی یافت نشد</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
