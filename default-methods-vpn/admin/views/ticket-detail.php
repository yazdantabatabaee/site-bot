<?php defined('ABSPATH') || exit; ?>
<div class="wrap dm-admin">
  <h1>🎫 تیکت #<?= $ticket['id'] ?> — <?= esc_html($ticket['subject']) ?></h1>
  <a href="?page=dm-vpn-tickets" class="button" style="margin-bottom:20px">← بازگشت</a>
  <?php if (isset($_GET['replied'])): ?>
    <div class="notice notice-success is-dismissible"><p>✅ پاسخ ارسال شد.</p></div>
  <?php endif; ?>

  <div style="max-width:700px">
    <?php foreach ($msgs as $m): ?>
      <div style="margin-bottom:14px;<?= $m['sender']==='admin'?'text-align:left':'' ?>">
        <div style="display:inline-block;max-width:80%;padding:14px 18px;
             border-radius:12px;font-size:.92rem;line-height:1.7;
             background:<?= $m['sender']==='user'?'#f0f4ff':'#f9f9f9' ?>;
             border:1px solid <?= $m['sender']==='user'?'#c7d2fe':'#e2e8f0' ?>">
          <?= nl2br(esc_html($m['message'])) ?>
          <div style="font-size:.75rem;color:#888;margin-top:6px">
            <?= $m['sender']==='user'?'👤 کاربر':'🔧 ادمین' ?> · <?= esc_html(substr($m['created_at'],0,10)) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <div style="margin-top:24px;background:#fff;border:1px solid #ddd;border-radius:12px;padding:20px">
      <h3 style="margin:0 0 14px">💬 ارسال پاسخ</h3>
      <form method="post" action="<?= admin_url('admin-post.php') ?>">
        <?php wp_nonce_field('dm_ticket_reply'); ?>
        <input type="hidden" name="action"    value="dm_ticket_reply">
        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
        <textarea name="message" required placeholder="پاسخ خود را بنویسید..."
          style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;height:120px;font-family:inherit;font-size:14px"></textarea>
        <div style="margin-top:12px">
          <button type="submit" class="button button-primary">📤 ارسال پاسخ</button>
        </div>
      </form>
    </div>
  </div>
</div>
