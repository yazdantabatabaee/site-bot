<?php defined('ABSPATH') || exit; ?>
<div class="wrap dm-admin">
  <h1>📦 Orders — Default Methods VPN</h1>

  <?php if (isset($_GET['msg'])): ?>
    <?php $msgs = [
      'approved' => ['success','✅ Order approved and VPN created successfully.'],
      'rejected' => ['warning','❌ Order rejected.'],
      'xui_error'=> ['error','⚠️ Order approved but VPN creation failed. Check <a href="'.admin_url('admin.php?page=dm-vpn-log').'">Debug Log</a>. Error: '.esc_html(urldecode($_GET['err']??''))],
    ]; $m = $msgs[$_GET['msg']] ?? null; ?>
    <?php if ($m): ?>
      <div class="notice notice-<?= $m[0] ?> is-dismissible"><p><?= $m[1] ?></p></div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Filters -->
  <div class="dm-admin-filters">
    <?php foreach ([
      ''                => 'All',
      'pending_payment' => '⏳ Pending Payment',
      'pending_review'  => '🔍 Pending Review',
      'approved'        => '✅ Approved',
      'active'          => '🟢 Active',
      'rejected'        => '❌ Rejected',
    ] as $k=>$v): ?>
      <a href="?page=dm-vpn-orders&filter=<?= $k ?>"
         class="dm-filter-btn <?= (($_GET['filter']??'')===$k)?'active':'' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>

  <table class="wp-list-table widefat fixed striped" style="margin-top:16px">
    <thead>
      <tr>
        <th width="55">ID</th>
        <th>User</th>
        <th>Plan</th>
        <th>Location</th>
        <th>Account Email</th>
        <th>Status</th>
        <th>Receipt</th>
        <th>Sub URL</th>
        <th>Date</th>
        <th width="160">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $o):
        $pname = $plans[$o['plan']]['name'] ?? $o['plan'];
      ?>
        <tr>
          <td><strong>#<?= $o['id'] ?></strong></td>
          <td>
            <strong><?= esc_html($o['display_name'] ?? '—') ?></strong>
            <br><span style="font-size:.8rem;color:#888"><?= esc_html($o['user_email']??'') ?></span>
          </td>
          <td><?= esc_html($pname) ?></td>
          <td><?= esc_html($o['country']??'—') ?></td>
          <td style="direction:ltr;font-size:.82rem;font-family:monospace"><?= esc_html($o['final_email']??'—') ?></td>
          <td><?= DM_Orders::status_label($o['status']) ?></td>
          <td>
            <?php if ($o['receipt_url']): ?>
              <a href="<?= esc_url($o['receipt_url']) ?>" target="_blank"
                 style="color:#6366f1;font-weight:600">🖼 View</a>
            <?php else: ?>
              <span style="color:#888">—</span>
            <?php endif; ?>
          </td>
          <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?php if ($o['sub_url']): ?>
              <a href="<?= esc_url($o['sub_url']) ?>" target="_blank"
                 style="color:#10b981;font-size:.8rem">🔗 Sub</a>
            <?php else: ?>
              <span style="color:#888">—</span>
            <?php endif; ?>
          </td>
          <td style="font-size:.82rem"><?= esc_html(substr($o['created_at'],0,10)) ?></td>
          <td>
            <?php if ($o['status']==='pending_review'): ?>
              <form method="post" action="<?= admin_url('admin-post.php') ?>" style="display:inline">
                <?php wp_nonce_field('dm_approve'); ?>
                <input type="hidden" name="action"   value="dm_approve">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <button class="button button-primary"
                        onclick="return confirm('Approve order #<?= $o['id'] ?> and create VPN?')">
                  ✅ Approve
                </button>
              </form>
              <button class="button" onclick="openReject(<?= $o['id'] ?>)" style="margin-top:4px">❌ Reject</button>
            <?php elseif ($o['status']==='approved'): ?>
              <span style="color:#f59e0b;font-size:.8rem">⚠️ VPN not created</span><br>
              <form method="post" action="<?= admin_url('admin-post.php') ?>" style="display:inline">
                <?php wp_nonce_field('dm_approve'); ?>
                <input type="hidden" name="action"   value="dm_approve">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <button class="button">🔁 Retry VPN</button>
              </form>
            <?php elseif ($o['status']==='active'): ?>
              <span style="color:#10b981;font-size:.82rem">✅ Active</span>
            <?php else: ?>
              <span style="color:#888;font-size:.82rem"><?= esc_html($o['status']) ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?>
        <tr><td colspan="10" style="text-align:center;padding:32px;color:#666">No orders found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Reject Modal -->
<div id="dm-rej-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:14px;padding:28px 32px;max-width:460px;width:92%;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <h2 style="margin:0 0 16px;font-size:1.1rem">❌ Reject Order <span id="rej-id"></span></h2>
    <form method="post" action="<?= admin_url('admin-post.php') ?>">
      <?php wp_nonce_field('dm_reject'); ?>
      <input type="hidden" name="action"   value="dm_reject">
      <input type="hidden" name="order_id" id="rej-oid">
      <label style="display:block;font-weight:600;margin-bottom:6px;font-size:.88rem">Reason (optional)</label>
      <textarea name="reason" rows="3" placeholder="Explain why..."
                style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;resize:vertical;font-family:inherit"></textarea>
      <div style="display:flex;gap:10px;margin-top:14px">
        <button type="button" class="button"
                onclick="document.getElementById('dm-rej-modal').style.display='none'">Cancel</button>
        <button type="submit" class="button"
                style="background:#ef4444;border-color:#ef4444;color:#fff">Reject Order</button>
      </div>
    </form>
  </div>
</div>
<script>
function openReject(id){
  document.getElementById('rej-id').textContent='#'+id;
  document.getElementById('rej-oid').value=id;
  document.getElementById('dm-rej-modal').style.display='flex';
}
document.getElementById('dm-rej-modal')?.addEventListener('click',function(e){
  if(e.target===this)this.style.display='none';
});
</script>
