<?php defined('ABSPATH') || exit; ?>
<div class="dm">
  <div style="max-width:440px;margin:60px auto;padding:0 16px">
    <div class="dm-card" style="text-align:center">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--p,#7c3aed),#a855f7);
                  border-radius:16px;display:flex;align-items:center;justify-content:center;
                  font-size:1.6rem;margin:0 auto 20px">✨</div>
      <h2 style="margin-bottom:8px">ساخت حساب کاربری</h2>
      <p style="color:var(--muted);font-size:.9rem;margin-bottom:28px">
        در چند ثانیه عضو Default Methods شوید
      </p>

      <div class="dm-field" style="text-align:right">
        <label class="dm-label">نام و نام خانوادگی</label>
        <input class="dm-input" id="rg-name" type="text" placeholder="نام شما" autocomplete="name" style="direction:rtl">
      </div>
      <div class="dm-field" style="text-align:right">
        <label class="dm-label">ایمیل <span style="color:var(--muted);font-weight:400">(اختیاری)</span></label>
        <input class="dm-input" id="rg-email" type="email" placeholder="example@mail.com" autocomplete="email">
      </div>
      <div class="dm-field" style="text-align:right">
        <label class="dm-label">شماره موبایل <span style="color:var(--muted);font-weight:400">(اختیاری)</span></label>
        <input class="dm-input" id="rg-mobile" type="tel" placeholder="09xxxxxxxxx" autocomplete="tel">
      </div>
      <div class="dm-field" style="text-align:right">
        <label class="dm-label">رمز عبور</label>
        <input class="dm-input" id="rg-pass" type="password" placeholder="حداقل ۶ کاراکتر" autocomplete="new-password">
      </div>

      <p class="dm-hint" style="text-align:right;margin-bottom:16px">
        ⚠️ حداقل یکی از ایمیل یا موبایل را وارد کنید — برای ورود بعدی استفاده می‌شود.
      </p>

      <div id="rg-alert"></div>

      <button class="dm-btn dm-btn-primary dm-btn-full" id="rg-submit">
        ساخت حساب کاربری
      </button>

      <p style="margin-top:22px;font-size:.88rem;color:var(--muted)">
        قبلاً ثبت‌نام کرده‌اید؟
        <a href="<?= home_url('/login/') ?>" style="color:var(--acc,#818cf8);font-weight:700">وارد شوید</a>
      </p>
    </div>
  </div>
</div>

<script>
(function(){
  const btn = document.getElementById('rg-submit');
  if(!btn) return;
  btn.addEventListener('click', function(){
    const name   = document.getElementById('rg-name').value.trim();
    const email  = document.getElementById('rg-email').value.trim();
    const mobile = document.getElementById('rg-mobile').value.trim();
    const pass   = document.getElementById('rg-pass').value;
    const alertBox = document.getElementById('rg-alert');

    if(!name || name.length < 2){
      alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ نام را کامل وارد کنید</div>'; return;
    }
    if(!email && !mobile){
      alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ ایمیل یا موبایل را وارد کنید</div>'; return;
    }
    if(!pass || pass.length < 6){
      alertBox.innerHTML = '<div class="dm-alert dm-alert-error">❌ رمز عبور باید حداقل ۶ کاراکتر باشد</div>'; return;
    }

    btn.disabled = true; const orig = btn.innerHTML; btn.innerHTML = '<span class="dm-spinner" style="width:18px;height:18px;border-width:2px;display:inline-block"></span>';

    fetch(DM.rest + 'register', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-WP-Nonce':DM.nonce},
      body: JSON.stringify({name, email, mobile, password: pass})
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
})();
</script>
