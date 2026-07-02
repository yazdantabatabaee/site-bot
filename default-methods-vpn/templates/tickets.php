<?php
defined('ABSPATH') || exit;
$user    = DM_Auth::current();
$tickets = DM_Tickets::user_tickets($user['id']);
?>
<div class="dm">
  <div class="dm-hero" style="padding:44px 20px 32px">
    <div class="dm-logo" style="width:64px;height:64px;font-size:1.8rem;margin-bottom:16px">🎫</div>
    <h1 style="font-size:1.7rem">پشتیبانی</h1>
    <p>سوال یا مشکل دارید؟ تیکت ارسال کنید.</p>
  </div>

  <div style="max-width:720px;margin:0 auto;padding:0 16px 48px">

    <div class="dm-card" style="margin-bottom:28px">
      <h2 style="font-size:1.1rem;margin-bottom:20px">✉️ تیکت جدید</h2>
      <div class="dm-field">
        <label class="dm-label">موضوع</label>
        <input class="dm-input" id="t-sub" placeholder="موضوع تیکت را بنویسید">
      </div>
      <div class="dm-field">
        <label class="dm-label">پیام</label>
        <textarea class="dm-input" id="t-msg" rows="5"
                  placeholder="مشکل یا سوال خود را توضیح دهید..." style="resize:vertical"></textarea>
      </div>
      <div id="t-alert"></div>
      <button class="dm-btn dm-btn-primary" id="t-send">📤 ارسال تیکت</button>
    </div>

    <?php if ($tickets): ?>
      <h3 style="margin-bottom:16px;font-size:1rem">تیکت‌های قبلی</h3>
      <?php foreach ($tickets as $t):
        $msgs  = DM_Tickets::messages($t['id']);
        $badge = match($t['status']){'answered'=>'green','closed'=>'gray',default=>'yellow'};
        $label = ['open'=>'باز','answered'=>'پاسخ داده شده','closed'=>'بسته'][$t['status']]??$t['status'];
      ?>
        <div class="dm-card" style="margin-bottom:16px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:8px">
            <div>
              <strong style="font-size:1rem">#<?= $t['id'] ?> — <?= esc_html($t['subject']) ?></strong>
              <div style="font-size:.8rem;color:var(--muted);margin-top:3px"><?= substr($t['created_at'],0,10) ?></div>
            </div>
            <span class="dm-badge dm-badge-<?= $badge ?>"><?= $label ?></span>
          </div>
          <div class="dm-chat">
            <?php foreach ($msgs as $m): ?>
              <div class="dm-msg dm-msg-<?= $m['sender'] ?>">
                <?= nl2br(esc_html($m['message'])) ?>
                <div class="dm-msg-time">
                  <?= $m['sender']==='user'?'👤 شما':'🔧 پشتیبانی' ?>
                  · <?= substr($m['created_at'],0,10) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('t-send')?.addEventListener('click',function(){
  const sub=document.getElementById('t-sub').value.trim();
  const msg=document.getElementById('t-msg').value.trim();
  const al =document.getElementById('t-alert');
  if(!sub||!msg){al.innerHTML='<div class="dm-alert dm-alert-err">❌ موضوع و پیام الزامی است</div>';return;}
  this.disabled=true;this.innerHTML='<span class="dm-spin"></span>';
  fetch(DM.rest+'ticket',{method:'POST',
    headers:{'X-WP-Nonce':DM.nonce,'Content-Type':'application/json'},
    body:JSON.stringify({subject:sub,message:msg})})
  .then(r=>r.json()).then(d=>{
    if(d.error){al.innerHTML='<div class="dm-alert dm-alert-err">❌ '+d.error+'</div>';}
    else{
      al.innerHTML='<div class="dm-alert dm-alert-ok">✅ تیکت #'+d.ticket_id+' ثبت شد. به زودی پاسخ می‌گیرید.</div>';
      document.getElementById('t-sub').value='';
      document.getElementById('t-msg').value='';
      setTimeout(()=>location.reload(),2000);
    }
  }).catch(()=>al.innerHTML='<div class="dm-alert dm-alert-err">❌ خطای شبکه</div>')
  .finally(()=>{this.disabled=false;this.textContent='📤 ارسال تیکت';});
});
</script>
