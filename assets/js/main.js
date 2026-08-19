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
    const closeMenu = () => {
      mobileMenu.classList.remove('open');
      burger.classList.remove('is-open');
    };
    burger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      burger.classList.toggle('is-open', isOpen);
    });
    mobileMenu.querySelectorAll('a').forEach(a =>
      a.addEventListener('click', closeMenu)
    );
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });
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

  const revealEls = document.querySelectorAll('.reveal, .mask-reveal');
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

  // ============ THE CONTACT SHEET — scroll parallax on scattered photos ============
  // (Awwwards "Parallax collage"-inspired: each photo drifts at its own depth)
  const scatterSection = document.getElementById('contactSheet');
  if (scatterSection) {
    const scatterPhotos = scatterSection.querySelectorAll('.scatter-photo');
    const parallaxTick = () => {
      const rect = scatterSection.getBoundingClientRect();
      const progress = (window.innerHeight - rect.top) / (window.innerHeight + rect.height);
      scatterPhotos.forEach((el) => {
        const depth = parseFloat(el.dataset.depth) || 0.12;
        const rot = el.dataset.rot || '0';
        const shift = (progress - 0.5) * depth * 340;
        el.style.transform = `translateY(${shift}px) rotate(${rot}deg)`;
      });
      requestAnimationFrame(parallaxTick);
    };
    requestAnimationFrame(parallaxTick);

    // Cursor image trail — leaves a trail of small floating photos while
    // the mouse moves across the section (Awwwards "cursor trail"-inspired).
    const trailSources = Array.from(scatterPhotos).map(el => el.dataset.trailSrc || el.querySelector('img')?.src).filter(Boolean);
    let lastSpawn = 0;
    scatterSection.addEventListener('mousemove', (e) => {
      const now = performance.now();
      if (now - lastSpawn < 110 || !trailSources.length) return;
      lastSpawn = now;
      const img = document.createElement('img');
      img.src = trailSources[Math.floor(Math.random() * trailSources.length)];
      img.className = 'trail-thumb';
      img.style.setProperty('--rot', (Math.random() * 16 - 8).toFixed(1) + 'deg');
      img.style.left = e.clientX + 'px';
      img.style.top = e.clientY + 'px';
      document.body.appendChild(img);
      requestAnimationFrame(() => img.classList.add('trail-show'));
      setTimeout(() => {
        img.classList.remove('trail-show');
        img.classList.add('trail-hide');
        setTimeout(() => img.remove(), 550);
      }, 500);
    });
  }

  // ============ FRAMES IN MOTION — vertical scroll drives horizontal filmstrip ============
  // (Awwwards "horizontal scroll gallery"-inspired)
  const filmstripWrapper = document.getElementById('filmstripWrapper');
  const filmstripTrack = document.getElementById('filmstripTrack');
  if (filmstripWrapper && filmstripTrack) {
    const filmstripTick = () => {
      const rect = filmstripWrapper.getBoundingClientRect();
      const total = rect.height - window.innerHeight;
      let progress = total > 0 ? -rect.top / total : 0;
      progress = Math.max(0, Math.min(1, progress));
      const maxTranslate = Math.max(filmstripTrack.scrollWidth - window.innerWidth, 0);
      filmstripTrack.style.transform = `translateX(${-progress * maxTranslate}px)`;
      requestAnimationFrame(filmstripTick);
    };
    requestAnimationFrame(filmstripTick);
  }

  // ============ SPOTLIGHT HOVER — cursor-tracking glow on cards (React Bits "SpotlightCard"-inspired) ============
  document.querySelectorAll('.spotlight-card').forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const r = card.getBoundingClientRect();
      card.style.setProperty('--sx', ((e.clientX - r.left) / r.width) * 100 + '%');
      card.style.setProperty('--sy', ((e.clientY - r.top) / r.height) * 100 + '%');
    });
  });

  // ============ ROTATING TAGLINE (lewahouse.com-inspired) ============
  const rotator = document.querySelector('.tagline-rotator');
  if (rotator) {
    const lines = rotator.querySelectorAll('span');
    let idx = 0;
    setInterval(() => {
      lines[idx].classList.remove('tagline-active');
      idx = (idx + 1) % lines.length;
      lines[idx].classList.add('tagline-active');
    }, 2600);
  }

  // ============ VIDEO EMBEDS — click to load & play inline (greenlensug.org-inspired) ============
  document.querySelectorAll('.video-card').forEach((card) => {
    card.addEventListener('click', () => {
      if (card.querySelector('iframe')) return;
      const id = card.dataset.videoId;
      if (!id) return;
      const iframe = document.createElement('iframe');
      iframe.src = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
      iframe.title = 'YouTube video player';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
      iframe.allowFullscreen = true;
      card.querySelectorAll('img, .play-btn, .video-label').forEach(el => el.remove());
      card.appendChild(iframe);
    });
  });

  // ============ LIGHTBOX — full-size view for gallery items ============
  // Makes the custom cursor's "View" promise real: click opens the full image,
  // with caption/EXIF, prev/next through the currently-filtered set, and
  // close via the X, backdrop click, or Escape.
  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    const lbImg = document.getElementById('lightboxImg');
    const lbCat = document.getElementById('lightboxCat');
    const lbTitle = document.getElementById('lightboxTitle');
    const lbExif = document.getElementById('lightboxExif');
    const closeBtn = document.getElementById('lightboxClose');
    const prevBtn = document.getElementById('lightboxPrev');
    const nextBtn = document.getElementById('lightboxNext');

    const getVisibleItems = () =>
      Array.from(document.querySelectorAll('.gallery-item')).filter(el => !el.classList.contains('hidden-item'));

    let currentIndex = 0;

    const openLightbox = (index) => {
      const items = getVisibleItems();
      if (!items.length) return;
      currentIndex = (index + items.length) % items.length;
      const item = items[currentIndex];
      const img = item.querySelector('img');
      const overlay = item.querySelector('.absolute');
      const cat = overlay?.children[0]?.textContent || '';
      const title = overlay?.children[1]?.textContent || '';
      const exifSpans = overlay?.children[2]
        ? Array.from(overlay.children[2].querySelectorAll('span')).map(s => s.textContent)
        : [];

      lbImg.src = img.src.replace(/w=\d+/, 'w=1800');
      lbImg.alt = img.alt;
      lbCat.textContent = cat;
      lbTitle.textContent = title;
      lbExif.innerHTML = exifSpans.map(t => `<span>${t}</span>`).join('');

      lightbox.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    };

    const closeLightbox = () => {
      lightbox.classList.remove('is-open');
      document.body.style.overflow = '';
    };

    document.querySelectorAll('.gallery-item').forEach((item) => {
      item.addEventListener('click', () => {
        openLightbox(getVisibleItems().indexOf(item));
      });
    });

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    prevBtn.addEventListener('click', () => openLightbox(currentIndex - 1));
    nextBtn.addEventListener('click', () => openLightbox(currentIndex + 1));

    document.addEventListener('keydown', (e) => {
      if (!lightbox.classList.contains('is-open')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') openLightbox(currentIndex - 1);
      if (e.key === 'ArrowRight') openLightbox(currentIndex + 1);
    });
  }
});
