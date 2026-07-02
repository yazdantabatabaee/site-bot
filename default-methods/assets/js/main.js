/* ══════════════════════════════════════════
   Default Methods — Main JS
   Signature: animated network-mesh hero canvas
══════════════════════════════════════════ */

/* ── Header scroll state ─────────────────── */
(function () {
  const header = document.getElementById('site-header');
  if (!header) return;
  const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 20);
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
})();

/* ── Mobile nav toggle ────────────────────── */
(function () {
  const btn  = document.getElementById('navToggle');
  const menu = document.getElementById('navMenu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('open'));
  menu.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => menu.classList.remove('open')));
})();

/* ── FAQ accordion ────────────────────────── */
window.toggleFaq = function (btn) {
  const answer = btn.nextElementSibling;
  const isOpen = btn.classList.contains('open');

  // Close all
  document.querySelectorAll('.faq-question.open').forEach(q => {
    q.classList.remove('open');
    q.nextElementSibling.style.maxHeight = '0px';
  });

  if (!isOpen) {
    btn.classList.add('open');
    answer.style.maxHeight = answer.scrollHeight + 'px';
  }
};

/* ── Animated stat counters ──────────────── */
(function () {
  const nums = document.querySelectorAll('.stat-num[data-count]');
  if (!nums.length) return;

  const animate = (el) => {
    const target = parseInt(el.dataset.count, 10);
    const dur = 1400;
    const start = performance.now();
    const step = (now) => {
      const p = Math.min((now - start) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(eased * target).toLocaleString('fa-IR') +
        (el.dataset.count === '99' ? '%' : el.dataset.count === '500' ? '+' : '');
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { animate(e.target); io.unobserve(e.target); }
    });
  }, { threshold: 0.5 });

  nums.forEach(n => io.observe(n));
})();

/* ══════════════════════════════════════════
   SIGNATURE: Network Mesh Hero Canvas
   Nodes drift and connect with lines when close —
   visualizes an encrypted mesh network (the product itself)
══════════════════════════════════════════ */
(function () {
  const canvas = document.getElementById('hero-canvas');
  if (!canvas || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const ctx = canvas.getContext('2d');
  let W, H, nodes = [];
  const NODE_COUNT = window.innerWidth < 768 ? 26 : 46;
  const LINK_DIST  = 150;
  const MOUSE_R    = 160;

  let mouse = { x: -9999, y: -9999 };

  function resize() {
    const rect = canvas.parentElement.getBoundingClientRect();
    W = canvas.width  = rect.width;
    H = canvas.height = rect.height;
  }

  function initNodes() {
    nodes = Array.from({ length: NODE_COUNT }, () => ({
      x: Math.random() * W,
      y: Math.random() * H,
      vx: (Math.random() - 0.5) * 0.35,
      vy: (Math.random() - 0.5) * 0.35,
      r: Math.random() * 1.6 + 1,
    }));
  }

  function step() {
    ctx.clearRect(0, 0, W, H);

    // Update positions
    nodes.forEach(n => {
      n.x += n.vx; n.y += n.vy;
      if (n.x < 0 || n.x > W) n.vx *= -1;
      if (n.y < 0 || n.y > H) n.vy *= -1;

      // gentle mouse repulsion
      const dx = n.x - mouse.x, dy = n.y - mouse.y;
      const d  = Math.sqrt(dx*dx + dy*dy);
      if (d < MOUSE_R) {
        const f = (MOUSE_R - d) / MOUSE_R * 0.6;
        n.x += (dx / (d||1)) * f;
        n.y += (dy / (d||1)) * f;
      }
    });

    // Draw links
    for (let i = 0; i < nodes.length; i++) {
      for (let j = i + 1; j < nodes.length; j++) {
        const a = nodes[i], b = nodes[j];
        const dx = a.x - b.x, dy = a.y - b.y;
        const dist = Math.sqrt(dx*dx + dy*dy);
        if (dist < LINK_DIST) {
          const alpha = (1 - dist / LINK_DIST) * 0.35;
          ctx.strokeStyle = `rgba(124,58,237,${alpha})`;
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.stroke();
        }
      }
    }

    // Draw nodes
    nodes.forEach(n => {
      ctx.beginPath();
      ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(196,181,253,0.7)';
      ctx.fill();
    });

    requestAnimationFrame(step);
  }

  window.addEventListener('resize', () => { resize(); initNodes(); });
  canvas.parentElement.addEventListener('mousemove', (e) => {
    const rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left;
    mouse.y = e.clientY - rect.top;
  });
  canvas.parentElement.addEventListener('mouseleave', () => {
    mouse.x = -9999; mouse.y = -9999;
  });

  resize();
  initNodes();
  requestAnimationFrame(step);
})();
