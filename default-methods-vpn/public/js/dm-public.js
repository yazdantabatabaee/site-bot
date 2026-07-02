/* Default Methods VPN - Public JS */
(function ($) {
  'use strict';

  /* ── State ─────────────────────────────────── */
  const state = {
    step: 1,
    plan: null,
    country: null,
    account_email: null,
    order_id: null,
    final_email: null,
  };

  /* ── Helpers ─────────────────────────────────*/
  const api = (endpoint, data, file) => {
    const headers = { 'X-WP-Nonce': DM.nonce };
    if (file) {
      const fd = new FormData();
      Object.entries(data).forEach(([k, v]) => fd.append(k, v));
      if (file) fd.append('receipt', file);
      return $.ajax({ url: DM.rest + endpoint, method: 'POST', data: fd,
        headers, processData: false, contentType: false });
    }
    return $.ajax({ url: DM.rest + endpoint, method: 'POST',
      contentType: 'application/json', headers,
      data: JSON.stringify(data) });
  };

  const apiGet = (endpoint) =>
    $.ajax({ url: DM.rest + endpoint, method: 'GET',
      headers: { 'X-WP-Nonce': DM.nonce } });

  const showStep = (n) => {
    state.step = n;
    $('.dm-panel').removeClass('active');
    $(`#dm-step-${n}`).addClass('active');
    $('.dm-step').removeClass('active done');
    for (let i = 1; i < n; i++) $(`#dm-stepnum-${i}`).addClass('done');
    $(`#dm-stepnum-${n}`).addClass('active');
    window.scrollTo({ top: $('.dm-wrap').offset().top - 20, behavior: 'smooth' });
  };

  const setLoading = (btn, loading) => {
    if (loading) {
      $(btn).data('orig', $(btn).html()).prop('disabled', true)
        .html('<span class="dm-spinner" style="display:inline-block;width:18px;height:18px;border-width:2px;"></span>');
    } else {
      $(btn).prop('disabled', false).html($(btn).data('orig'));
    }
  };

  const alert = (type, msg, container = '#dm-alert') => {
    $(container).html(`<div class="dm-alert dm-alert-${type}">${msg}</div>`);
    setTimeout(() => $(container).html(''), 6000);
  };

  const copyText = (text) => {
    navigator.clipboard.writeText(text).catch(() => {
      const el = document.createElement('textarea');
      el.value = text; document.body.appendChild(el);
      el.select(); document.execCommand('copy');
      document.body.removeChild(el);
    });
  };

  /* ── QR Generator ───────────────────────────── */
  const makeQR = (container, text, size = 100) => {
    $(container).empty();
    new QRCode($(container)[0], {
      text, width: size, height: size,
      colorDark: '#1a2235', colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.M,
    });
  };

  /* ══════════════════════════════════════════════
     SHOP FLOW
  ══════════════════════════════════════════════ */

  /* Step 1: Plan selection */
  $(document).on('click', '.dm-plan-card', function () {
    $('.dm-plan-card').removeClass('selected');
    $(this).addClass('selected');
    state.plan = $(this).data('plan');
    setTimeout(() => showStep(2), 300);
  });

  /* Step 2: Location selection */
  $(document).on('click', '.dm-loc-btn', function () {
    $('.dm-loc-btn').removeClass('selected');
    $(this).addClass('selected');
    state.country = $(this).data('country');
    setTimeout(() => showStep(3), 300);
  });

  /* Step 3: Email input */
  $(document).on('click', '#dm-btn-email', function () {
    const email = $('#dm-account-email').val().trim().toLowerCase();
    if (!/^[a-z0-9][a-z0-9._-]{2,29}$/.test(email)) {
      alert('error', '❌ نام اکانت نامعتبر است. فقط حروف انگلیسی کوچک، عدد، نقطه و خط تیره مجاز است.');
      return;
    }
    state.account_email = email;

    // Summary
    $('#dm-sum-plan').text($('.dm-plan-card.selected .dm-plan-name').text());
    $('#dm-sum-country').text($('.dm-loc-btn.selected .dm-loc-name').text());
    $('#dm-sum-email').text(email);
    $('#dm-sum-price').text($('.dm-plan-card.selected .dm-plan-price').text());
    showStep(4);
  });

  /* Step 4: Confirm & create order */
  $(document).on('click', '#dm-btn-confirm', function () {
    if (!DM.logged) {
      window.location.href = DM.loginUrl; return;
    }
    const btn = this;
    setLoading(btn, true);
    api('order', { plan: state.plan, country: state.country, account_email: state.account_email })
      .done(r => {
        state.order_id   = r.order_id;
        state.final_email = r.final_email;
        $('#dm-pay-plan').text(r.plan_name);
        $('#dm-pay-price').text(Number(r.price).toLocaleString('fa-IR') + ' تومان');
        $('#dm-pay-email').text(r.final_email);
        $('#dm-card-num').text(r.card_number);
        $('#dm-card-owner').text(r.card_owner);
        $('#dm-order-id').text('#' + r.order_id);
        showStep(5);
      })
      .fail(() => alert('error', '❌ خطا در ثبت سفارش. دوباره تلاش کنید.'))
      .always(() => setLoading(btn, false));
  });

  /* Copy card number */
  $(document).on('click', '#dm-card-num', function () {
    copyText($(this).text().replace(/\s/g, ''));
    $(this).text('✅ کپی شد!');
    setTimeout(() => $(this).text($(this).data('num')), 1500);
  });

  /* Step 5: Upload receipt */
  $(document).on('change', '#dm-receipt-file', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      $('.dm-upload-preview').attr('src', e.target.result).show();
    };
    reader.readAsDataURL(file);
  });

  $(document).on('click', '#dm-btn-receipt', function () {
    const file = $('#dm-receipt-file')[0].files[0];
    if (!file) { alert('error', '❌ لطفاً رسید را آپلود کنید'); return; }

    const btn = this;
    setLoading(btn, true);
    api('receipt', { order_id: state.order_id }, file)
      .done(() => {
        showStep(6);
      })
      .fail(() => alert('error', '❌ خطا در آپلود رسید'))
      .always(() => setLoading(btn, false));
  });

  /* ══════════════════════════════════════════════
     DASHBOARD
  ══════════════════════════════════════════════ */

  if ($('.dm-dashboard').length) {
    loadOrders();
  }

  function loadOrders() {
    $('#dm-orders-loading').show();
    $('#dm-orders-body').html('');
    apiGet('orders').done(orders => {
      $('#dm-orders-loading').hide();
      if (!orders.length) {
        $('#dm-orders-body').html('<tr><td colspan="5" style="text-align:center;color:var(--dm-muted);padding:32px">هیچ سفارشی یافت نشد</td></tr>');
        return;
      }
      orders.forEach(o => {
        const badgeCls = {
          active: 'green', pending_payment: 'yellow', pending_review: 'yellow',
          rejected: 'red', expired: 'gray',
        }[o.status] || 'gray';
        $('#dm-orders-body').append(`
          <tr>
            <td>#${o.id}</td>
            <td>${o.plan_name}</td>
            <td>${o.country || '-'}</td>
            <td><span class="dm-badge dm-badge-${badgeCls}">${o.status_label}</span></td>
            <td>${o.status === 'active' ? `<button class="dm-btn dm-btn-outline" style="padding:6px 14px;font-size:.82rem" onclick="showOrderDetail(${o.id})">مشاهده</button>` : '-'}</td>
          </tr>
        `);
      });
    });
  }

  window.showOrderDetail = function (orderId) {
    $('#dm-order-detail').html('<div class="dm-loading"><div class="dm-spinner"></div> در حال بارگذاری...</div>').show();
    apiGet('orders').done(orders => {
      const o = orders.find(x => x.id == orderId);
      if (!o || !o.sub_url) {
        $('#dm-order-detail').html('<div class="dm-alert dm-alert-info">اطلاعات اتصال هنوز آماده نشده.</div>');
        return;
      }

      let html = `
        <div class="dm-sub-box">
          <h3>📡 لینک اشتراک</h3>
          <div class="dm-sub-url" onclick="copyAndToast(this, '${o.sub_url}')">${o.sub_url}</div>
          <div class="dm-sub-qr" id="qr-sub-${orderId}"></div>
        </div>
      `;

      if (o.configs && o.configs.length) {
        html += '<div class="dm-configs">';
        o.configs.forEach((cfg, i) => {
          html += `
            <div class="dm-config-card">
              <div class="dm-config-qr" id="qr-cfg-${orderId}-${i}"></div>
              <div class="dm-config-info">
                <div class="dm-config-name">${cfg.name}</div>
                <div class="dm-config-link" onclick="copyAndToast(this, \`${cfg.link}\`)">${cfg.link}</div>
                <span class="dm-config-copy">📋 کپی لینک</span>
              </div>
            </div>`;
        });
        html += '</div>';
      }

      $('#dm-order-detail').html(html);

      // Generate QR codes
      setTimeout(() => {
        makeQR(`#qr-sub-${orderId}`, o.sub_url, 120);
        if (o.configs) {
          o.configs.forEach((cfg, i) => makeQR(`#qr-cfg-${orderId}-${i}`, cfg.link, 90));
        }
      }, 100);
    });
  };

  window.copyAndToast = function (el, text) {
    copyText(text);
    const orig = $(el).css('color');
    $(el).css('color', '#10b981').prepend('✅ کپی شد! ');
    setTimeout(() => { $(el).css('color', orig); $(el).find('✅ کپی شد! ').remove(); }, 1500);
  };

  /* ── Tabs ─────────────────────────────────── */
  $(document).on('click', '.dm-tab', function () {
    $('.dm-tab').removeClass('active');
    $(this).addClass('active');
    const target = $(this).data('tab');
    $('.dm-tab-content').hide();
    $(`#tab-${target}`).show();
    if (target === 'orders') loadOrders();
    if (target === 'tickets') loadTickets();
  });

  /* ── Tickets ─────────────────────────────── */
  function loadTickets() {
    // Handled server-side in template
  }

  $(document).on('click', '#dm-btn-ticket', function () {
    const subject = $('#dm-ticket-subject').val().trim();
    const message = $('#dm-ticket-message').val().trim();
    if (!subject || !message) {
      alert('error', '❌ موضوع و پیام الزامی است', '#dm-ticket-alert');
      return;
    }
    const btn = this;
    setLoading(btn, true);
    api('ticket', { subject, message })
      .done(r => {
        alert('success', `✅ تیکت #${r.ticket_id} ثبت شد.`, '#dm-ticket-alert');
        $('#dm-ticket-subject, #dm-ticket-message').val('');
      })
      .fail(() => alert('error', '❌ خطا', '#dm-ticket-alert'))
      .always(() => setLoading(btn, false));
  });

  /* ── Renewal ─────────────────────────────── */
  $(document).on('click', '.dm-btn-renew', function () {
    const orderId = $(this).data('order');
    const file    = $(`#renew-file-${orderId}`)[0]?.files[0];
    if (!file) { alert('error', '❌ رسید آپلود کنید'); return; }
    const btn = this;
    setLoading(btn, true);
    api('renew', { order_id: orderId }, file)
      .done(() => alert('success', '✅ درخواست تمدید ارسال شد'))
      .fail(() => alert('error', '❌ خطا'))
      .always(() => setLoading(btn, false));
  });

  /* ── Drag & drop upload ───────────────────── */
  $(document).on('dragover', '.dm-upload', function (e) {
    e.preventDefault(); $(this).addClass('drag');
  }).on('dragleave drop', '.dm-upload', function (e) {
    e.preventDefault(); $(this).removeClass('drag');
    if (e.type === 'drop') {
      const file = e.originalEvent.dataTransfer.files[0];
      if (file) {
        const dt = new DataTransfer(); dt.items.add(file);
        $(this).find('input')[0].files = dt.files;
        $(this).find('input').trigger('change');
      }
    }
  });

})(jQuery);
