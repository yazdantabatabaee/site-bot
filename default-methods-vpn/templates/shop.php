<?php defined('ABSPATH') || exit; $plans = DM_Orders::plans(); $locs = DM_Orders::locations(); ?>
<div class="dm">

  <!-- HERO -->
  <div class="dm-hero">
    <div class="dm-logo">🛡️</div>
    <h1>اینترنت آزاد<br>بدون محدودیت</h1>
    <p>سریع‌ترین سرورها در ارمنستان، آلمان، ترکیه و فنلاند<br>با پروتکل VLESS و Reality — غیرقابل شناسایی</p>
    <div class="dm-hero-btns">
      <a href="#plans" class="dm-btn dm-btn-primary dm-btn-lg" onclick="showStep(1);return false">🚀 همین الان شروع کن</a>
      <a href="<?= home_url('/dashboard/') ?>" class="dm-btn dm-btn-ghost dm-btn-lg">داشبورد من</a>
    </div>
  </div>

  <!-- STATS -->
  <div class="dm-stats">
    <div class="dm-stat"><div class="dm-stat-num">+500</div><div class="dm-stat-label">کاربر فعال</div></div>
    <div class="dm-stat"><div class="dm-stat-num">4</div><div class="dm-stat-label">کشور</div></div>
    <div class="dm-stat"><div class="dm-stat-num">99.9%</div><div class="dm-stat-label">آپتایم</div></div>
    <div class="dm-stat"><div class="dm-stat-num">24/7</div><div class="dm-stat-label">پشتیبانی</div></div>
  </div>

  <!-- STEPS BAR -->
  <div class="dm-steps">
    <?php $steps = ['انتخاب پلن','لوکیشن','نام اکانت','تایید','پرداخت','تکمیل']; ?>
    <?php foreach ($steps as $i => $s): $n = $i+1; ?>
      <div class="dm-step-item <?= $n===1?'active':'' ?>" id="ds-<?= $n ?>">
        <div class="dm-step-dot"><?= $n ?></div>
        <span class="dm-step-lbl"><?= $s ?></span>
      </div>
      <?php if ($n < count($steps)): ?><span class="dm-step-sep">›</span><?php endif; ?>
    <?php endforeach; ?>
  </div>

  <div id="dm-alert-top" style="max-width:860px;margin:0 auto;padding:0 16px"></div>

  <!-- ═══ STEP 1: Plans ═══ -->
  <div class="dm-panel active" id="dp-1">
    <div class="dm-sec">
      <span class="dm-sec-tag">پلن‌ها</span>
      <h2>پلن مناسب خود را انتخاب کنید</h2>
      <p>همه پلن‌ها شامل ترافیک نامحدود و تمام لوکیشن‌ها می‌شوند</p>
    </div>
    <div class="dm-plans">
      <div class="dm-plan" data-plan="1user">
        <div class="dm-plan-icon">👤</div>
        <div class="dm-plan-title">پلن ۱ کاربره</div>
        <div class="dm-plan-desc">ایده‌آل برای استفاده شخصی</div>
        <div class="dm-plan-price">۴۰۰,۰۰۰ <span class="unit">تومان / ماه</span></div>
        <ul class="dm-plan-feats">
          <li>۱ دستگاه همزمان</li>
          <li>ترافیک نامحدود</li>
          <li>تمام ۴ لوکیشن</li>
          <li>پروتکل VLESS + Reality</li>
          <li>پشتیبانی ۲۴/۷</li>
        </ul>
      </div>
      <div class="dm-plan popular" data-plan="2user">
        <div class="dm-plan-ribbon">⭐ محبوب‌ترین</div>
        <div class="dm-plan-icon">👥</div>
        <div class="dm-plan-title">پلن ۲ کاربره</div>
        <div class="dm-plan-desc">برای خانواده یا ۲ دستگاه</div>
        <div class="dm-plan-price">۶۰۰,۰۰۰ <span class="unit">تومان / ماه</span></div>
        <ul class="dm-plan-feats">
          <li>۲ دستگاه همزمان</li>
          <li>ترافیک نامحدود</li>
          <li>تمام ۴ لوکیشن</li>
          <li>پروتکل VLESS + Reality</li>
          <li>پشتیبانی اولویت‌دار</li>
        </ul>
      </div>
    </div>
    <div style="margin-top:36px">
      <div class="dm-sec"><span class="dm-sec-tag">چرا Default Methods؟</span></div>
      <div class="dm-features">
        <?php foreach ([
          ['⚡','سرعت بالا','سرورهای اختصاصی با لینک ۱ گیگابیتی'],
          ['🔒','امنیت کامل','رمزنگاری پیشرفته با Reality'],
          ['🌍','چند لوکیشن','ارمنستان، آلمان، ترکیه، فنلاند'],
          ['📱','همه دستگاه‌ها','V2rayNG، Hiddify، Nekobox و...'],
        ] as [$ico,$t,$d]): ?>
          <div class="dm-feat"><div class="dm-feat-ico"><?= $ico ?></div><div class="dm-feat-title"><?= $t ?></div><div class="dm-feat-text"><?= $d ?></div></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ═══ STEP 2: Location ═══ -->
  <div class="dm-panel" id="dp-2">
    <div class="dm-sec">
      <span class="dm-sec-tag">لوکیشن</span>
      <h2>سرور مورد نظر را انتخاب کنید</h2>
      <p>All = دریافت کانفیگ همه لوکیشن‌ها به صورت یکجا</p>
    </div>
    <?php $flags = ['armenia'=>'🇦🇲','germany'=>'🇩🇪','turkey'=>'🇹🇷','finland'=>'🇫🇮','all'=>'🌍'];
    $pings = ['armenia'=>'12ms','germany'=>'48ms','turkey'=>'22ms','finland'=>'55ms','all'=>'—']; ?>
    <div class="dm-locs">
      <?php foreach ($locs as $k => $n): ?>
        <button class="dm-loc" data-country="<?= $k ?>">
          <span class="dm-loc-flag"><?= $flags[$k] ?></span>
          <span class="dm-loc-name"><?= $n ?></span>
          <div class="dm-loc-ping"><?= $pings[$k] ?></div>
        </button>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:24px;text-align:center">
      <button class="dm-btn dm-btn-ghost" onclick="showStep(1)">← بازگشت</button>
    </div>
  </div>

  <!-- ═══ STEP 3: Email ═══ -->
  <div class="dm-panel" id="dp-3">
    <div class="dm-card" style="max-width:520px;margin:0 auto">
      <div class="dm-sec" style="margin-bottom:24px">
        <span class="dm-sec-tag">نام اکانت</span>
        <h2>یک نام برای VPN خود انتخاب کنید</h2>
      </div>
      <div class="dm-field">
        <label class="dm-label">نام دلخواه (به انگلیسی)</label>
        <input class="dm-input" id="dm-em" type="text" placeholder="مثال: ali.phone یا myvpn1" autocomplete="off" spellcheck="false">
        <p class="dm-hint">⚠️ فقط حروف کوچک انگلیسی، عدد، نقطه و خط تیره — ۳ تا ۳۰ کاراکتر</p>
      </div>
      <div style="display:flex;gap:12px">
        <button class="dm-btn dm-btn-ghost" onclick="showStep(2)">← بازگشت</button>
        <button class="dm-btn dm-btn-primary" id="dm-btn-em" style="flex:1">ادامه ←</button>
      </div>
    </div>
  </div>

  <!-- ═══ STEP 4: Confirm ═══ -->
  <div class="dm-panel" id="dp-4">
    <div class="dm-card" style="max-width:520px;margin:0 auto">
      <div class="dm-sec" style="margin-bottom:24px">
        <span class="dm-sec-tag">تایید</span>
        <h2>خلاصه سفارش</h2>
      </div>
      <div class="dm-sum">
        <div class="dm-sum-row"><span class="k">پلن</span><span id="s-plan"></span></div>
        <div class="dm-sum-row"><span class="k">لوکیشن</span><span id="s-loc"></span></div>
        <div class="dm-sum-row"><span class="k">Email اکانت</span><span id="s-em" style="direction:ltr;font-size:.9rem"></span></div>
        <div class="dm-sum-row"><span class="k">مبلغ</span><span id="s-price" style="color:var(--p);font-size:1.1rem"></span></div>
      </div>
      <?php if (!DM_Auth::is_logged_in()): ?>
        <div class="dm-alert dm-alert-info">برای ادامه باید <a href="<?= home_url('/login/') ?>" style="color:var(--acc);font-weight:700">وارد حساب کاربری شوید</a> یا <a href="<?= home_url('/register/') ?>" style="color:var(--acc);font-weight:700">ثبت‌نام کنید</a>.</div>
      <?php endif; ?>
      <div style="display:flex;gap:12px">
        <button class="dm-btn dm-btn-ghost" onclick="showStep(3)">← بازگشت</button>
        <button class="dm-btn dm-btn-primary" id="dm-btn-conf" style="flex:1">
          <?= DM_Auth::is_logged_in() ? '✅ ثبت سفارش' : '🔐 ورود و ثبت' ?>
        </button>
      </div>
    </div>
  </div>

  <!-- ═══ STEP 5: Payment ═══ -->
  <div class="dm-panel" id="dp-5">
    <div class="dm-card" style="max-width:560px;margin:0 auto">
      <div class="dm-sec" style="margin-bottom:24px">
        <span class="dm-sec-tag">پرداخت</span>
        <h2>واریز کارت به کارت</h2>
      </div>
      <div class="dm-pay-box">
        <div class="dm-pay-row"><span class="k">پلن</span><span id="p-plan"></span></div>
        <div class="dm-pay-row"><span class="k">لوکیشن</span><span id="p-loc"></span></div>
        <div class="dm-pay-row"><span class="k">ایمیل اکانت</span><span id="p-em" style="direction:ltr;font-size:.85rem"></span></div>
        <div class="dm-pay-row"><span class="k">شماره سفارش</span><strong id="p-oid"></strong></div>
        <div class="dm-pay-row"><span class="k">مبلغ</span><strong id="p-price" style="color:var(--p);font-size:1.1rem"></strong></div>
      </div>
      <p style="color:var(--muted);font-size:.88rem;margin:16px 0 6px">شماره کارت (کلیک برای کپی):</p>
      <div class="dm-card-num" id="p-card">—</div>
      <p style="text-align:center;color:var(--muted);font-size:.88rem">به نام: <strong id="p-owner" style="color:var(--text)"></strong></p>
      <div class="dm-alert dm-alert-warn" style="margin:16px 0">
        ⚠️ پس از واریز، تصویر رسید را آپلود کنید.
      </div>
      <div class="dm-upload" id="dm-upload">
        <input type="file" id="dm-rcpt" accept="image/*">
        <div class="dm-upload-ico">📄</div>
        <strong>آپلود رسید پرداخت</strong>
        <p>کلیک کنید یا فایل را بکشید</p>
        <img class="dm-upload-preview" id="dm-prev">
      </div>
      <div id="dm-rcpt-alert"></div>
      <button class="dm-btn dm-btn-primary dm-btn-full" id="dm-btn-rcpt" style="margin-top:16px">
        📤 ارسال رسید
      </button>
    </div>
  </div>

  <!-- ═══ STEP 6: Done ═══ -->
  <div class="dm-panel" id="dp-6">
    <div class="dm-card" style="max-width:520px;margin:0 auto;text-align:center;padding:48px 32px">
      <div style="font-size:4.5rem;margin-bottom:20px;animation:float 3s ease-in-out infinite">✅</div>
      <h2 style="font-size:1.6rem">رسید دریافت شد!</h2>
      <p style="color:var(--muted);line-height:1.9;margin:14px 0 24px">
        رسید شما با موفقیت ارسال شد.<br>
        معمولاً تا <strong style="color:var(--text)">۳۰ دقیقه</strong> VPN شما فعال می‌شود.<br>
        لینک اشتراک و کانفیگ‌ها در داشبورد قابل مشاهده است.
      </p>
      <div class="dm-alert dm-alert-info">📬 اطلاعات اتصال به ایمیل شما هم ارسال می‌شود.</div>
      <a href="<?= home_url('/dashboard/') ?>" class="dm-btn dm-btn-primary dm-btn-full dm-btn-lg" style="margin-top:20px">
        📊 مشاهده داشبورد
      </a>
    </div>
  </div>

</div>

<script>
const _st = { step:1, plan:null, loc:null, email:null, oid:null };

function showStep(n) {
  _st.step = n;
  document.querySelectorAll('.dm-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('dp-'+n)?.classList.add('active');
  document.querySelectorAll('.dm-step-item').forEach(s => s.classList.remove('active','done'));
  for (let i=1;i<n;i++) document.getElementById('ds-'+i)?.classList.add('done');
  document.getElementById('ds-'+n)?.classList.add('active');
  document.querySelector('.dm')?.scrollIntoView({behavior:'smooth',block:'start'});
}

function showAlert(msg, type='info', id='dm-alert-top') {
  const el = document.getElementById(id);
  if(el) { el.innerHTML=`<div class="dm-alert dm-alert-${type}">${msg}</div>`; setTimeout(()=>el.innerHTML='',5000); }
}

function spin(btn, on) {
  if(on){ btn.dataset.orig=btn.innerHTML; btn.disabled=true; btn.innerHTML='<span class="dm-spin"></span>'; }
  else { btn.disabled=false; btn.innerHTML=btn.dataset.orig||btn.innerHTML; }
}

function copyText(t) {
  navigator.clipboard?.writeText(t).catch(()=>{ const x=document.createElement('textarea'); x.value=t; document.body.appendChild(x); x.select(); document.execCommand('copy'); x.remove(); });
}

function api(ep, data, file) {
  const h = {'X-WP-Nonce': DM.nonce};
  if(file){ const fd=new FormData(); Object.entries(data).forEach(([k,v])=>fd.append(k,v)); fd.append('receipt',file);
    return fetch(DM.rest+ep, {method:'POST', headers:h, body:fd}).then(r=>r.json()); }
  return fetch(DM.rest+ep, {method:'POST', headers:{...h,'Content-Type':'application/json'}, body:JSON.stringify(data)}).then(r=>r.json());
}

// Plan select
document.querySelectorAll('.dm-plan').forEach(c => {
  c.addEventListener('click', () => {
    document.querySelectorAll('.dm-plan').forEach(x=>x.classList.remove('selected'));
    c.classList.add('selected'); _st.plan = c.dataset.plan;
    setTimeout(()=>showStep(2), 280);
  });
});

// Location select
document.querySelectorAll('.dm-loc').forEach(b => {
  b.addEventListener('click', () => {
    document.querySelectorAll('.dm-loc').forEach(x=>x.classList.remove('selected'));
    b.classList.add('selected'); _st.loc = b.dataset.country;
    setTimeout(()=>showStep(3), 280);
  });
});

// Email continue
document.getElementById('dm-btn-em')?.addEventListener('click', () => {
  const v = document.getElementById('dm-em').value.trim().toLowerCase();
  if(!/^[a-z0-9][a-z0-9._-]{2,29}$/.test(v)){ showAlert('❌ نام اکانت نامعتبر است.','err'); return; }
  _st.email = v;
  document.getElementById('s-plan').textContent = document.querySelector('.dm-plan.selected .dm-plan-title')?.textContent || _st.plan;
  document.getElementById('s-loc').textContent  = document.querySelector('.dm-loc.selected .dm-loc-name')?.textContent  || _st.loc;
  document.getElementById('s-em').textContent   = v;
  document.getElementById('s-price').textContent= document.querySelector('.dm-plan.selected .dm-plan-price')?.textContent || '';
  showStep(4);
});

// Confirm order
document.getElementById('dm-btn-conf')?.addEventListener('click', async function() {
  if(!DM.logged){ location.href=DM.loginUrl; return; }
  spin(this, true);
  try {
    const r = await api('order', { plan:_st.plan, country:_st.loc, account_email:_st.email });
    if(r.error){ showAlert('❌ '+r.error,'err'); return; }
    _st.oid = r.order_id;
    document.getElementById('p-plan').textContent  = r.plan_name;
    document.getElementById('p-loc').textContent   = document.querySelector('.dm-loc.selected .dm-loc-name')?.textContent || _st.loc;
    document.getElementById('p-em').textContent    = r.final_email;
    document.getElementById('p-oid').textContent   = '#'+r.order_id;
    document.getElementById('p-price').textContent = Number(r.price).toLocaleString('fa-IR')+' تومان';
    const cn = document.getElementById('p-card');
    cn.textContent = r.card_number; cn.dataset.num = r.card_number;
    document.getElementById('p-owner').textContent = r.card_owner;
    showStep(5);
  } catch(e){ showAlert('❌ خطای شبکه. دوباره تلاش کنید.','err'); }
  finally{ spin(this, false); }
});

// Copy card
document.getElementById('p-card')?.addEventListener('click', function() {
  copyText(this.dataset.num||this.textContent);
  const orig=this.textContent; this.textContent='✅ کپی شد!';
  setTimeout(()=>this.textContent=orig,1400);
});

// Preview receipt
document.getElementById('dm-rcpt')?.addEventListener('change', function() {
  const f=this.files[0]; if(!f)return;
  const r=new FileReader(); r.onload=e=>{ const img=document.getElementById('dm-prev'); img.src=e.target.result; img.style.display='block'; }; r.readAsDataURL(f);
});

// Upload receipt
document.getElementById('dm-btn-rcpt')?.addEventListener('click', async function() {
  const f=document.getElementById('dm-rcpt').files[0];
  if(!f){ showAlert('❌ رسید را آپلود کنید','err','dm-rcpt-alert'); return; }
  spin(this,true);
  try {
    const r=await api('receipt',{order_id:_st.oid},f);
    if(r.error){ showAlert('❌ '+r.error,'err','dm-rcpt-alert'); return; }
    showStep(6);
  } catch(e){ showAlert('❌ خطای آپلود','err','dm-rcpt-alert'); }
  finally{ spin(this,false); }
});

// Drag & drop
const uz=document.getElementById('dm-upload');
if(uz){ uz.addEventListener('dragover',e=>{e.preventDefault();uz.classList.add('drag');}); uz.addEventListener('dragleave',()=>uz.classList.remove('drag')); uz.addEventListener('drop',e=>{e.preventDefault();uz.classList.remove('drag');const f=e.dataTransfer.files[0];if(f){const dt=new DataTransfer();dt.items.add(f);document.getElementById('dm-rcpt').files=dt.files;document.getElementById('dm-rcpt').dispatchEvent(new Event('change'));}}); }
</script>
