<?php
defined('ABSPATH') || exit;
$user    = DM_Auth::current();
$orders  = DM_Orders::get_user_orders($user['id']);
$tickets = DM_Tickets::user_tickets($user['id']);
$plans   = DM_Orders::plans();
?>
<div class="dm">

  <div class="dm-hero" style="padding:44px 20px 32px">
    <div class="dm-logo" style="width:64px;height:64px;font-size:1.8rem;margin-bottom:16px">👤</div>
    <h1 style="font-size:1.7rem">داشبورد من</h1>
    <p>خوش آمدید <strong><?= esc_html($user['name']) ?></strong></p>
    <a href="<?= home_url('/?dm_logout=1') ?>" style="display:inline-block;margin-top:12px;font-size:.85rem;color:var(--muted)">
      🚪 خروج از حساب
    </a>
  </div>

  <div style="max-width:920px;margin:0 auto;padding:0 16px 48px">

    <div class="dm-tabs">
      <button class="dm-tab active" data-tab="orders">📦 سفارش‌ها</button>
      <button class="dm-tab" data-tab="tickets">🎫 تیکت‌ها</button>
      <button class="dm-tab" data-tab="renew">🔄 تمدید</button>
    </div>

    <!-- ── ORDERS ── -->
    <div id="tab-orders">
      <?php if (empty($orders)): ?>
        <div class="dm-card" style="text-align:center;padding:52px 24px">
          <div style="font-size:3.5rem;margin-bottom:16px">📭</div>
          <h3 style="font-size:1.2rem;margin-bottom:8px">هنوز سفارشی ندارید</h3>
          <p style="color:var(--muted);margin-bottom:24px">اولین VPN خود را همین الان تهیه کنید.</p>
          <a href="<?= home_url('/shop/') ?>" class="dm-btn dm-btn-primary">🛒 خرید VPN</a>
        </div>
      <?php else: ?>
        <?php foreach ($orders as $o):
          $pname  = $plans[$o['plan']]['name'] ?? $o['plan'];
          $badge  = match($o['status']) {
            'active'                       => 'green',
            'pending_payment','pending_review','approved' => 'yellow',
            'rejected','expired'           => 'red',
            default                        => 'gray',
          };
          $configs = $o['configs'] ? json_decode($o['configs'], true) : [];
        ?>
          <div class="dm-order-card">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
              <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
                  <strong style="font-size:1rem">سفارش #<?= $o['id'] ?></strong>
                  <span class="dm-badge dm-badge-<?= $badge ?>"><?= DM_Orders::status_label($o['status']) ?></span>
                </div>
                <div style="color:var(--muted);font-size:.85rem">
                  <?= esc_html($pname) ?> &nbsp;·&nbsp; <?= esc_html($o['country'] ?: '—') ?>
                  &nbsp;·&nbsp; <?= esc_html(substr($o['created_at'],0,10)) ?>
                </div>
                <?php if ($o['final_email']): ?>
                  <div style="font-size:.8rem;color:var(--muted);margin-top:4px;direction:ltr">
                    📧 <?= esc_html($o['final_email']) ?>
                  </div>
                <?php endif; ?>
              </div>
              <?php if ($o['status'] === 'active' && $o['sub_url']): ?>
                <button class="dm-btn dm-btn-primary" style="padding:10px 20px;font-size:.88rem"
                        onclick="toggleVPN(<?= $o['id'] ?>,this)">
                  🔗 کانفیگ‌ها
                </button>
              <?php endif; ?>
            </div>

            <?php if ($o['status'] === 'active' && $o['sub_url']): ?>
              <div id="vpn-<?= $o['id'] ?>" style="display:none;margin-top:22px">

                <div class="dm-sub-box" style="margin-bottom:16px">
                  <h3>📡 لینک اشتراک — همه کانفیگ‌ها</h3>
                  <div class="dm-sub-url" onclick="copyAndFlash(this,'<?= esc_js($o['sub_url']) ?>')">
                    <?= esc_html($o['sub_url']) ?>
                  </div>
                  <p style="font-size:.8rem;color:var(--muted);margin:8px 0 14px">
                    این لینک را در V2rayNG، Hiddify یا Nekobox وارد کنید.
                  </p>
                  <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap">
                    <div id="sub-qr-<?= $o['id'] ?>" style="background:#fff;border-radius:8px;padding:4px;width:110px;height:110px;flex-shrink:0"></div>
                    <div>
                      <p style="font-size:.82rem;color:var(--muted);line-height:1.7">
                        QR را اسکن کنید یا لینک را کپی کنید.<br>
                        در اپ، روی «+» کلیک کرده و «Import from clipboard» را بزنید.
                      </p>
                      <button class="dm-btn dm-btn-ghost" style="margin-top:10px;padding:8px 16px;font-size:.82rem"
                              onclick="copyAndFlash(document.querySelector('#vpn-<?= $o['id'] ?> .dm-sub-url'),'<?= esc_js($o['sub_url']) ?>')">
                        📋 کپی لینک ساب
                      </button>
                    </div>
                  </div>
                </div>

                <?php if (!empty($configs)): ?>
                  <div style="margin-bottom:10px;color:var(--muted);font-size:.88rem">
                    🔧 <strong style="color:var(--text)"><?= count($configs) ?> کانفیگ</strong> جداگانه:
                  </div>
                  <div class="dm-configs">
                    <?php foreach ($configs as $ci => $link):
                      $name = DM_XUI_API::link_name($link); ?>
                      <div class="dm-cfg">
                        <div class="dm-cfg-qr" id="cfg-qr-<?= $o['id'] ?>-<?= $ci ?>"></div>
                        <div class="dm-cfg-info">
                          <div class="dm-cfg-name"><?= esc_html($name) ?></div>
                          <div class="dm-cfg-link" onclick="copyAndFlash(this,'<?= esc_js($link) ?>')">
                            <?= esc_html(strlen($link)>90?substr($link,0,90).'…':$link) ?>
                          </div>
                          <button class="dm-btn dm-btn-ghost" style="padding:6px 14px;font-size:.78rem;margin-top:6px"
                                  onclick="copyLink('<?= esc_js($link) ?>', this)">📋 کپی لینک</button>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php elseif ($o['sub_url']): ?>
                  <div class="dm-alert dm-alert-info">
                    ℹ️ کانفیگ‌های جداگانه در حال بارگذاری... لینک ساب بالا را برای همه کانفیگ‌ها استفاده کنید.
                  </div>
                <?php endif; ?>

                <script>
                  document.addEventListener('DOMContentLoaded',function(){
                    if(typeof QRCode==='undefined')return;
                    new QRCode(document.getElementById('sub-qr-<?= $o['id'] ?>'),{
                      text:'<?= esc_js($o['sub_url']) ?>',width:100,height:100,
                      colorDark:'#111d35',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});
                    <?php foreach ($configs as $ci => $link): ?>
                    new QRCode(document.getElementById('cfg-qr-<?= $o['id'] ?>-<?= $ci ?>'),{
                      text:'<?= esc_js($link) ?>',width:88,height:88,
                      colorDark:'#111d35',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});
                    <?php endforeach; ?>
                  });
                </script>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div style="text-align:center;margin-top:20px">
          <a href="<?= home_url('/shop/') ?>" class="dm-btn dm-btn-ghost">🛒 سفارش جدید</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- ── TICKETS ── -->
    <div id="tab-tickets" style="display:none">
      <div class="dm-card" style="margin-bottom:24px">
        <h3 style="margin-bottom:18px;font-size:1.05rem">✉️ ارسال تیکت جدید</h3>
        <div class="dm-field">
          <label class="dm-label">موضوع</label>
          <input class="dm-input" id="t-sub" placeholder="موضوع تیکت">
        </div>
        <div class="dm-field">
          <label class="dm-label">پیام</label>
          <textarea class="dm-input" id="t-msg" rows="4" placeholder="پیام خود را بنویسید..." style="resize:vertical"></textarea>
        </div>
        <div id="t-alert"></div>
        <button class="dm-btn dm-btn-primary" id="t-send">📤 ارسال تیکت</button>
      </div>

      <?php if ($tickets): ?>
        <h3 style="margin-bottom:14px">تیکت‌های قبلی</h3>
        <?php foreach ($tickets as $t):
          $tbadge = match($t['status']){
            'answered'=>'green','closed'=>'gray',default=>'yellow'};
          $tlabel = ['open'=>'باز','answered'=>'پاسخ داده شده','closed'=>'بسته'][$t['status']]??$t['status'];
        ?>
          <div class="dm-ticket-item" onclick="loadTicket(<?= $t['id'] ?>,this)">
            <div>
              <div style="font-weight:700;margin-bottom:4px">#<?= $t['id'] ?> — <?= esc_html($t['subject']) ?></div>
              <div style="font-size:.8rem;color:var(--muted)"><?= substr($t['created_at'],0,10) ?></div>
            </div>
            <span class="dm-badge dm-badge-<?= $tbadge ?>"><?= $tlabel ?></span>
          </div>
          <div id="tmsg-<?= $t['id'] ?>" style="display:none;padding:0 4px 16px"></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- ── RENEWAL ── -->
    <div id="tab-renew" style="display:none">
      <?php $active = array_filter($orders, fn($o) => $o['status']==='active'); ?>
      <?php if (empty($active)): ?>
        <div class="dm-card" style="text-align:center;padding:40px">
          <p style="color:var(--muted)">اشتراک فعالی برای تمدید وجود ندارد.</p>
          <a href="<?= home_url('/shop/') ?>" class="dm-btn dm-btn-primary" style="margin-top:16px">🛒 خرید اشتراک</a>
        </div>
      <?php else: ?>
        <?php foreach ($active as $o): $pn=$plans[$o['plan']]['name']??$o['plan']; ?>
          <div class="dm-card" style="margin-bottom:16px">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:18px">
              <div>
                <strong style="font-size:1rem">سفارش #<?= $o['id'] ?> — <?= esc_html($pn) ?></strong>
                <div style="color:var(--muted);font-size:.82rem;margin-top:3px;direction:ltr"><?= esc_html($o['final_email']??'') ?></div>
              </div>
              <span class="dm-badge dm-badge-green">✅ فعال</span>
            </div>
            <div class="dm-alert dm-alert-info" style="margin-bottom:16px">
              💰 هزینه تمدید: <strong style="color:var(--text)"><?= number_format($plans[$o['plan']]['price']??400000) ?> تومان</strong> — ۳۰ روز اضافه
            </div>
            <div class="dm-upload" style="padding:28px" onclick="document.getElementById('rf-<?= $o['id'] ?>').click()">
              <input type="file" id="rf-<?= $o['id'] ?>" accept="image/*" style="display:none"
                     onchange="previewFile(this,'rp-<?= $o['id'] ?>')">
              <div class="dm-upload-ico" style="font-size:2.2rem">📤</div>
              <strong>آپلود رسید پرداخت</strong>
              <p>کلیک کنید یا فایل را بکشید</p>
              <img id="rp-<?= $o['id'] ?>" style="max-width:140px;border-radius:8px;margin-top:12px;display:none">
            </div>
            <button class="dm-btn dm-btn-primary dm-btn-full" style="margin-top:12px"
                    onclick="sendRenew(<?= $o['id'] ?>,this)">🔄 ارسال درخواست تمدید</button>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
document.querySelectorAll('.dm-tab').forEach(t=>{
  t.addEventListener('click',()=>{
    document.querySelectorAll('.dm-tab').forEach(x=>x.classList.remove('active'));
    t.classList.add('active');
    ['orders','tickets','renew'].forEach(id=>{ const el=document.getElementById('tab-'+id); if(el) el.style.display='none'; });
    const tab=document.getElementById('tab-'+t.dataset.tab);
    if(tab) tab.style.display='block';
  });
});

function toggleVPN(id,btn){
  const el=document.getElementById('vpn-'+id);
  if(!el)return;
  const open=el.style.display!=='none';
  el.style.display=open?'none':'block';
  btn.textContent=open?'🔗 کانفیگ‌ها':'🔼 بستن';
}

function copyAndFlash(el,text){
  navigator.clipboard?.writeText(text).catch(()=>{});
  const oc=el.style.color; el.style.color='#10b981';
  setTimeout(()=>el.style.color=oc,1200);
}

function copyLink(text,btn){
  navigator.clipboard?.writeText(text).catch(()=>{});
  const o=btn.textContent; btn.textContent='✅ کپی شد!';
  setTimeout(()=>btn.textContent=o,1300);
}

function loadTicket(id,row){
  const el=document.getElementById('tmsg-'+id);
  if(!el)return;
  if(el.style.display!=='none'){el.style.display='none';return;}
  el.style.display='block';
  el.innerHTML='<div style="padding:16px;color:var(--muted)"><span class="dm-spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span></div>';
  fetch(DM.rest+'ticket/'+id,{headers:{'X-WP-Nonce':DM.nonce}}).then(r=>r.json()).then(d=>{
    if(d.error){el.innerHTML='<p style="color:var(--red);padding:12px">'+d.error+'</p>';return;}
    let h='<div class="dm-chat" style="padding:0 4px 12px">';
    d.messages.forEach(m=>{
      h+=`<div class="dm-msg dm-msg-${m.sender}">${m.message.replace(/\n/g,'<br>')}
          <div class="dm-msg-time">${m.sender==='user'?'👤 شما':'🔧 پشتیبانی'} · ${m.created_at.slice(0,10)}</div></div>`;
    });
    el.innerHTML=h+'</div>';
  }).catch(()=>el.innerHTML='<p style="color:var(--red)">خطا</p>');
}

document.getElementById('t-send')?.addEventListener('click',function(){
  const sub=document.getElementById('t-sub').value.trim();
  const msg=document.getElementById('t-msg').value.trim();
  if(!sub||!msg){showA('❌ موضوع و پیام الزامی است','error','t-alert');return;}
  const btn=this; btn.disabled=true; btn.innerHTML='<span class="dm-spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span>';
  fetch(DM.rest+'ticket',{method:'POST',headers:{'X-WP-Nonce':DM.nonce,'Content-Type':'application/json'},
    body:JSON.stringify({subject:sub,message:msg})}).then(r=>r.json()).then(d=>{
    if(d.error){showA('❌ '+d.error,'error','t-alert');}
    else{showA('✅ تیکت #'+d.ticket_id+' ثبت شد.','success','t-alert');
      document.getElementById('t-sub').value='';document.getElementById('t-msg').value='';}
  }).finally(()=>{btn.disabled=false;btn.textContent='📤 ارسال تیکت';});
});

function showA(msg,type,id){
  const el=document.getElementById(id);
  if(el){el.innerHTML=`<div class="dm-alert dm-alert-${type}" style="margin-top:10px">${msg}</div>`;
    setTimeout(()=>el.innerHTML='',5000);}
}

function previewFile(input,imgId){
  const f=input.files[0]; if(!f)return;
  const r=new FileReader(); r.onload=e=>{ const img=document.getElementById(imgId); img.src=e.target.result; img.style.display='block'; }; r.readAsDataURL(f);
}

function sendRenew(orderId,btn){
  const f=document.getElementById('rf-'+orderId)?.files[0];
  if(!f){alert('رسید را آپلود کنید');return;}
  btn.disabled=true; btn.innerHTML='<span class="dm-spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span>';
  const fd=new FormData(); fd.append('order_id',orderId); fd.append('receipt',f);
  fetch(DM.rest+'renew',{method:'POST',headers:{'X-WP-Nonce':DM.nonce},body:fd})
    .then(r=>r.json()).then(d=>{
      if(d.error)alert('❌ '+d.error);
      else{btn.textContent='✅ ارسال شد'; btn.style.background='var(--green)';}
    }).catch(()=>alert('❌ خطای شبکه'))
    .finally(()=>{if(btn.textContent!=='✅ ارسال شد'){btn.disabled=false;btn.textContent='🔄 ارسال درخواست تمدید';}});
}
</script>
