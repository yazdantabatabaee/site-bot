<?php get_header(); ?>

<!-- ══════════════ HERO ══════════════ -->
<section class="hero">
  <div class="hero-bg"></div>
  <canvas id="hero-canvas"></canvas>

  <div class="hero-inner">
    <div class="hero-content">
      <span class="hero-badge">🟢 ۵۴۷ کاربر اکنون آنلاین</span>
      <h1>اینترنت آزاد،<br>بدون مرز و محدودیت</h1>
      <p>سرورهای اختصاصی در ۴ کشور با پروتکل VLESS و Reality — سرعت بالا، غیرقابل شناسایی توسط DPI، و پشتیبانی واقعی ۲۴ ساعته.</p>
      <div class="hero-btns">
        <a href="<?= home_url('/shop/') ?>" class="btn btn-primary btn-lg">🚀 همین الان شروع کن</a>
        <a href="#how" class="btn btn-ghost btn-lg">مشاهده نحوه کار</a>
      </div>
      <div class="hero-trust">
        <div class="hero-trust-item">⚡ <strong>زیر ۱۵ ثانیه</strong> فعال‌سازی</div>
        <div class="hero-trust-item">🔒 <strong>بدون لاگ</strong></div>
        <div class="hero-trust-item">💬 <strong>پشتیبانی زنده</strong></div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-shield">
        <div class="hero-shield-bg"></div>
        <div class="hero-shield-ring"></div>
        <div class="hero-shield-ring"></div>
        <div class="hero-shield-ring"></div>
        <div class="hero-shield-icon">🛡️</div>
      </div>
      <div class="hero-float">
        <span class="hero-float-dot" style="background:#10b981"></span>Armenia 🇦🇲 · 12ms
      </div>
      <div class="hero-float">
        <span class="hero-float-dot" style="background:#10b981"></span>Germany 🇩🇪 · 48ms
      </div>
      <div class="hero-float">
        <span class="hero-float-dot" style="background:#f59e0b"></span>Encrypted ✓
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ FEATURES STRIP ══════════════ -->
<section class="features-strip">
  <div class="container">
    <div class="features-strip-grid">
      <?php foreach ([
        ['⚡','سرعت بالا','لینک اختصاصی ۱ گیگابیتی'],
        ['🔒','امنیت کامل','رمزنگاری Reality پیشرفته'],
        ['🌍','۴ کشور','ارمنستان، آلمان، ترکیه، فنلاند'],
        ['📱','همه دستگاه‌ها','Android، iOS، Windows، Mac'],
      ] as [$ico,$t,$d]): ?>
        <div class="feat-strip-item">
          <div class="feat-strip-icon"><?= $ico ?></div>
          <div class="feat-strip-title"><?= $t ?></div>
          <div class="feat-strip-desc"><?= $d ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════ HOW IT WORKS ══════════════ -->
<section class="section" id="how">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">فرآیند ساده</span>
      <h2>در ۳ قدم متصل شوید</h2>
      <p>از انتخاب پلن تا دریافت کانفیگ، کمتر از چند دقیقه طول می‌کشد</p>
    </div>
    <div class="how-grid">
      <div class="how-card">
        <div class="how-num">۱</div>
        <h3>پلن را انتخاب کنید</h3>
        <p>پلن مناسب خودتان را انتخاب کرده و لوکیشن دلخواه را مشخص کنید.</p>
      </div>
      <div class="how-card">
        <div class="how-num">۲</div>
        <h3>پرداخت کنید</h3>
        <p>با کارت به کارت پرداخت کرده و تصویر رسید را آپلود کنید.</p>
      </div>
      <div class="how-card">
        <div class="how-num">۳</div>
        <h3>متصل شوید</h3>
        <p>کانفیگ و QR Code را دریافت کرده و در اپ مورد نظر وارد کنید.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ PRICING ══════════════ -->
<section class="section" id="pricing" style="background:var(--bg2)">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">پلن‌ها</span>
      <h2>یک پلن، دسترسی کامل</h2>
      <p>بدون محدودیت ترافیک، بدون هزینه پنهان</p>
    </div>
    <div class="pricing-grid">
      <div class="price-card">
        <div class="price-icon">👤</div>
        <div class="price-name">پلن ۱ کاربره</div>
        <div class="price-desc">ایده‌آل برای استفاده شخصی</div>
        <div class="price-amount">۴۰۰,۰۰۰ <span>تومان</span></div>
        <div class="price-period">در ماه</div>
        <div class="price-features">
          <div class="price-feature">۱ دستگاه همزمان</div>
          <div class="price-feature">ترافیک نامحدود</div>
          <div class="price-feature">دسترسی به تمام ۴ لوکیشن</div>
          <div class="price-feature">پروتکل VLESS + Reality</div>
          <div class="price-feature">پشتیبانی ۲۴/۷</div>
        </div>
        <a href="<?= home_url('/shop/') ?>" class="btn btn-ghost" style="width:100%">انتخاب پلن</a>
      </div>

      <div class="price-card featured">
        <div class="price-ribbon">⭐ محبوب‌ترین</div>
        <div class="price-icon">👥</div>
        <div class="price-name">پلن ۲ کاربره</div>
        <div class="price-desc">برای خانواده یا دو دستگاه</div>
        <div class="price-amount">۶۰۰,۰۰۰ <span>تومان</span></div>
        <div class="price-period">در ماه</div>
        <div class="price-features">
          <div class="price-feature">۲ دستگاه همزمان</div>
          <div class="price-feature">ترافیک نامحدود</div>
          <div class="price-feature">دسترسی به تمام ۴ لوکیشن</div>
          <div class="price-feature">پروتکل VLESS + Reality</div>
          <div class="price-feature">پشتیبانی اولویت‌دار</div>
        </div>
        <a href="<?= home_url('/shop/') ?>" class="btn btn-primary" style="width:100%">انتخاب پلن</a>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ LOCATIONS ══════════════ -->
<section class="section" id="locations">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">شبکه سرورها</span>
      <h2>سرورهایی نزدیک به شما</h2>
      <p>بهترین لوکیشن را بر اساس نیاز خود انتخاب کنید</p>
    </div>
    <div class="locations-grid">
      <?php foreach ([
        ['🇦🇲','Armenia','12ms'],['🇩🇪','Germany','48ms'],
        ['🇹🇷','Turkey','22ms'],['🇫🇮','Finland','55ms'],
        ['🌍','All','—'],
      ] as [$f,$n,$p]): ?>
        <div class="location-card">
          <div class="location-flag"><?= $f ?></div>
          <div class="location-name"><?= $n ?></div>
          <div class="location-ping">⚡ <?= $p ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════ STATS ══════════════ -->
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item"><div class="stat-num" data-count="500">0</div><div class="stat-label">کاربر فعال</div></div>
      <div class="stat-item"><div class="stat-num" data-count="4">0</div><div class="stat-label">کشور</div></div>
      <div class="stat-item"><div class="stat-num" data-count="99">0</div><div class="stat-label">درصد آپتایم</div></div>
      <div class="stat-item"><div class="stat-num" data-count="24">0</div><div class="stat-label">ساعت پشتیبانی</div></div>
    </div>
  </div>
</section>

<!-- ══════════════ TESTIMONIALS ══════════════ -->
<section class="section">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">نظرات کاربران</span>
      <h2>مشتریان ما چه می‌گویند</h2>
    </div>
    <div class="testimonials-grid">
      <?php foreach ([
        ['علی رضایی','کاربر ۶ ماهه','سرعتش فوق‌العاده است، هیچ‌وقت قطع نمی‌شود. برای گیمینگ هم عالیه.'],
        ['سارا محمدی','کاربر ۳ ماهه','پشتیبانیشون واقعا سریع جواب میدن. کمتر از ۵ دقیقه مشکلمو حل کردن.'],
        ['حسین کریمی','کاربر ۱ ساله','از وقتی مشترک شدم دیگه سراغ هیچ سرویس دیگه‌ای نرفتم. قیمتش هم منصفانه‌ست.'],
      ] as [$name,$meta,$text]): ?>
        <div class="testimonial">
          <div class="stars">★★★★★</div>
          <p class="testimonial-text"><?= $text ?></p>
          <div class="testimonial-author">
            <div class="testimonial-avatar">👤</div>
            <div>
              <div class="testimonial-name"><?= $name ?></div>
              <div class="testimonial-meta"><?= $meta ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════ FAQ ══════════════ -->
<section class="section" id="faq" style="background:var(--bg2)">
  <div class="container">
    <div class="section-title">
      <span class="eyebrow">سوالات متداول</span>
      <h2>پاسخ سوالات شما</h2>
    </div>
    <div class="faq-list">
      <?php foreach ([
        ['چطور می‌توانم اشتراک تهیه کنم؟','کافیست پلن، لوکیشن و نام اکانت دلخواه را انتخاب کرده، پرداخت را انجام دهید و رسید را آپلود کنید. پس از تایید (معمولا زیر ۳۰ دقیقه)، کانفیگ برای شما ارسال می‌شود.'],
        ['چند دستگاه می‌توانم همزمان استفاده کنم؟','بستگی به پلن شما دارد. پلن ۱ کاربره یک دستگاه و پلن ۲ کاربره دو دستگاه همزمان را پشتیبانی می‌کند.'],
        ['آیا محدودیت ترافیک دارد؟','خیر، تمام پلن‌های ما ترافیک کاملا نامحدود دارند.'],
        ['چطور می‌توانم اشتراکم را تمدید کنم؟','از داشبورد خود، بخش «تمدید اشتراک» را انتخاب کرده و رسید پرداخت را ارسال کنید.'],
        ['پشتیبانی چطور کار می‌کند؟','از طریق بخش تیکت در داشبورد یا ربات تلگرام می‌توانید با تیم پشتیبانی در تماس باشید.'],
      ] as $i => [$q,$a]): ?>
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span><?= $q ?></span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer"><p><?= $a ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════ CTA ══════════════ -->
<section class="cta-section">
  <div class="container-sm">
    <span class="eyebrow">همین حالا شروع کنید</span>
    <h2>آماده‌اید برای اینترنت آزاد؟</h2>
    <p>در کمتر از ۵ دقیقه به شبکه امن Default Methods بپیوندید.</p>
    <div class="cta-btns">
      <a href="<?= home_url('/shop/') ?>" class="btn btn-gold btn-lg">🚀 خرید اشتراک</a>
      <a href="<?= home_url('/tickets/') ?>" class="btn btn-ghost btn-lg">💬 صحبت با پشتیبانی</a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
