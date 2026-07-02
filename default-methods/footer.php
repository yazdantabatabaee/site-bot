  <footer id="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="nav-logo">
            <span class="nav-logo-mark">🛡️</span>
            <span>Default Methods</span>
          </div>
          <p>اینترنت آزاد، امن و بدون محدودیت. اتصال سریع با پروتکل‌های پیشرفته و پشتیبانی ۲۴ ساعته.</p>
          <div class="footer-social">
            <a href="#" aria-label="Telegram">✈️</a>
            <a href="#" aria-label="Instagram">📷</a>
            <a href="#" aria-label="Twitter">🐦</a>
          </div>
        </div>

        <div class="footer-col">
          <h4>محصول</h4>
          <ul>
            <li><a href="<?= home_url('/shop/') ?>">پلن‌ها</a></li>
            <li><a href="<?= home_url('/#locations') ?>">لوکیشن‌ها</a></li>
            <li><a href="<?= home_url('/#faq') ?>">سوالات متداول</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>حساب کاربری</h4>
          <ul>
            <li><a href="<?= home_url('/dashboard/') ?>">داشبورد</a></li>
            <li><a href="<?= home_url('/tickets/') ?>">پشتیبانی</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <?php if (is_active_sidebar('footer-widgets')): dynamic_sidebar('footer-widgets'); else: ?>
          <h4>تماس با ما</h4>
          <ul>
            <li><a href="#">تلگرام پشتیبانی</a></li>
            <li><a href="mailto:support@defaultmethods.ir">support@defaultmethods.ir</a></li>
          </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="footer-bottom">
        <span>© <?= date('Y') ?> <strong>Default Methods</strong> — تمامی حقوق محفوظ است.</span>
        <span>ساخته‌شده با 🔒 برای آزادی اینترنت</span>
      </div>
    </div>
  </footer>

<?php wp_footer(); ?>
</body>
</html>
