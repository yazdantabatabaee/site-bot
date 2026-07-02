<?php defined('ABSPATH') || exit; ?>
<div class="dm">
  <div style="max-width:420px;margin:60px auto;padding:0 16px">
    <div class="dm-card" style="text-align:center">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--p,#7c3aed),#a855f7);
                  border-radius:16px;display:flex;align-items:center;justify-content:center;
                  font-size:1.6rem;margin:0 auto 20px">🔐</div>
      <h2 style="margin-bottom:8px">ورود به حساب کاربری</h2>
      <p style="color:var(--muted);font-size:.9rem;margin-bottom:28px">
        برای مشاهده سفارش‌ها و کانفیگ‌های خود وارد شوید
      </p>

      <div class="dm-field" style="text-align:right">
        <label class="dm-label">ایمیل یا شماره موبایل</label>
        <input class="dm-input" id="li-id" type="text" placeholder="example@mail.com یا 09xxxxxxxxx" autocomplete="username">
      </div>
      <div class="dm-field" style="text-align:right">
        <label class="dm-label">رمز عبور</label>
        <input class="dm-input" id="li-pass" type="password" placeholder="••••••••" autocomplete="current-password">
      </div>

      <div id="li-alert"></div>

      <button class="dm-btn dm-btn-primary dm-btn-full" id="li-submit" style="margin-top:8px">
        ورود
      </button>

      <p style="margin-top:22px;font-size:.88rem;color:var(--muted)">
        حساب کاربری ندارید؟
        <a href="<?= home_url('/register/') ?>" style="color:var(--acc,#818cf8);font-weight:700">ثبت‌نام کنید</a>
      </p>
    </div>
  </div>
</div>

<script>
(function(){
  const btn = document.getElementById('li-submit');
  if(!btn) return;
  btn.addEventListener('click', function(){
    const identity = document.getElementById('li-id').value.trim();
    const password = document.getElementById('li-pass').value;
    const alertBox = document.getElementById('li-alert');
    if(!identity || !password){
      alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ همه فیلدها الزامی است</div>';
      return;
    }
    btn.disabled = true; const orig = btn.innerHTML; btn.innerHTML = '<span class="dm-spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span>';

    fetch(DM.rest + 'login', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-WP-Nonce':DM.nonce},
      body: JSON.stringify({identity, password})
    }).then(r=>r.json()).then(d=>{
      if(d.error){
        alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ '+d.error+'</div>';
        btn.disabled=false; btn.innerHTML=orig;
      } else {
        window.location.href = DM.dashboardUrl || '<?= home_url('/dashboard/') ?>';
      }
    }).catch(()=>{
      alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ خطای شبکه</div>';
      btn.disabled=false; btn.innerHTML=orig;
    });
  });

  document.getElementById('li-pass').addEventListener('keydown', e => {
    if(e.key==='Enter') btn.click();
  });
})();
</script>
