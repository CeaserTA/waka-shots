document.addEventListener('DOMContentLoaded', () => {
  const header = document.getElementById('siteHeader');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('is-scrolled', window.scrollY > 40);
    });
  }

  const burger = document.getElementById('burgerBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (burger && mobileMenu) {
    burger.addEventListener('click', () => mobileMenu.classList.toggle('open'));
    mobileMenu.querySelectorAll('a').forEach(a =>
      a.addEventListener('click', () => mobileMenu.classList.remove('open'))
    );
  }

  // Scattered grid settle-in: give each work/gallery item a random tilt + offset
  // before it's revealed, staggered by position within its parent (Awwwards-inspired).
  document.querySelectorAll('.work-item, .gallery-item').forEach((el, i) => {
    const rot = (Math.random() * 10 - 5).toFixed(2);
    const ty = 28 + Math.random() * 22;
    el.style.opacity = '0';
    el.style.transform = `translateY(${ty}px) rotate(${rot}deg) scale(0.96)`;
    el.style.transitionDelay = Math.min(i * 70, 560) + 'ms';
  });

  const revealEls = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => io.observe(el));

  // Portfolio category filter (only present on portfolio.html)
  const filterBtns = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-item');
  if (filterBtns.length && galleryItems.length) {
    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.filter;
        galleryItems.forEach(item => {
          const match = cat === 'all' || item.dataset.category === cat;
          item.classList.toggle('hidden-item', !match);
        });
      });
    });
  }

  // Custom viewfinder cursor (desktop / fine-pointer only, Awwwards "Project Aperture"-inspired)
  if (window.matchMedia('(pointer: fine)').matches) {
    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    const ring = document.createElement('div');
    ring.className = 'cursor-ring';
    const label = document.createElement('span');
    label.className = 'cursor-label';
    ring.appendChild(label);
    document.body.appendChild(dot);
    document.body.appendChild(ring);
    document.body.classList.add('custom-cursor-active');

    let mx = 0, my = 0, rx = 0, ry = 0;
    window.addEventListener('mousemove', (e) => {
      mx = e.clientX; my = e.clientY;
      dot.style.left = mx + 'px'; dot.style.top = my + 'px';
      dot.classList.add('is-visible'); ring.classList.add('is-visible');
    });
    document.addEventListener('mouseleave', () => {
      dot.classList.remove('is-visible'); ring.classList.remove('is-visible');
    });
    (function loop() {
      rx += (mx - rx) * 0.15; ry += (my - ry) * 0.15;
      ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
      requestAnimationFrame(loop);
    })();

    const hoverTargets = document.querySelectorAll(
      '.work-item, .gallery-item, .bg-gold, [data-cursor]'
    );
    hoverTargets.forEach((el) => {
      const text = el.dataset.cursor || (el.classList.contains('bg-gold') ? 'Book' : 'View');
      el.addEventListener('mouseenter', () => {
        ring.classList.add('cursor-hover');
        label.textContent = text;
      });
      el.addEventListener('mouseleave', () => {
        ring.classList.remove('cursor-hover');
        label.textContent = '';
      });
    });

    // Magnetic pull on primary gold buttons (Awwwards-inspired)
    document.querySelectorAll('.bg-gold').forEach((btn) => {
      btn.addEventListener('mousemove', (e) => {
        const r = btn.getBoundingClientRect();
        const x = e.clientX - r.left - r.width / 2;
        const y = e.clientY - r.top - r.height / 2;
        btn.style.transform = `translate(${x * 0.25}px, ${y * 0.35}px)`;
      });
      btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
    });
  }

  // Count-up stats (React Bits "CountUp"-inspired)
  const statEls = document.querySelectorAll('.text-3xl.text-gold-bright');
  if (statEls.length) {
    const statIo = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const raw = el.textContent.trim();
        const target = parseInt(raw.replace(/[^\d]/g, ''), 10);
        const suffix = raw.replace(/[\d]/g, '');
        if (!isNaN(target)) {
          el.classList.add('stat-counting');
          const duration = 1400;
          const start = performance.now();
          const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target) + suffix;
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = target + suffix;
          };
          requestAnimationFrame(tick);
        }
        statIo.unobserve(el);
      });
    }, { threshold: 0.5 });
    statEls.forEach((el) => statIo.observe(el));
  }

  // Page loader (only present on index.html, Awwwards "Load In"-inspired)
  const loader = document.getElementById('pageLoader');
  if (loader) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        loader.classList.add('is-loaded');
        setTimeout(() => loader.remove(), 800);
      }, 400);
    });
  }
});
