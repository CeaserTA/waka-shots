import { MorphSlider } from './morph-slider';

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
    // ---- LIQUID MENU WIPE (zeustheagency.com-inspired) ----
    // Four stacked SVG layers cascade down like liquid to cover the screen
    // (bright gold leads, black lands last). On close the wipe keeps moving
    // the same direction and exits off the bottom — a wipe-through, not a rewind.
    const ns = 'http://www.w3.org/2000/svg';
    const wipe = document.createElementNS(ns, 'svg');
    wipe.setAttribute('viewBox', '0 0 100 100');
    wipe.setAttribute('preserveAspectRatio', 'none');
    wipe.setAttribute('aria-hidden', 'true');
    wipe.classList.add('menu-wipe');
    const layers = ['#e6c887', '#c6a15b', '#8a7140', '#0a0908'].map((c) => {
      const p = document.createElementNS(ns, 'path');
      p.setAttribute('fill', c);
      wipe.appendChild(p);
      return p;
    });
    document.body.appendChild(wipe);

    const XS = [0, 25, 50, 75, 100];
    const cubicInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
    // Wavy edge through 5 points; shape anchored to top (open) or bottom (close)
    const edgePath = (ys, open) => {
      let d = `M 0 ${open ? 0 : 100} L 0 ${ys[0]}`;
      for (let i = 1; i < ys.length; i++) {
        const mx = (XS[i - 1] + XS[i]) / 2;
        d += ` C ${mx} ${ys[i - 1]} ${mx} ${ys[i]} ${XS[i]} ${ys[i]}`;
      }
      return d + ` L 100 ${ys[4]} L 100 ${open ? 0 : 100} Z`;
    };
    layers.forEach((p) => p.setAttribute('d', edgePath([0, 0, 0, 0, 0], true))); // flat = invisible

    let wipeRAF = 0;
    const runWipe = (open) => {
      cancelAnimationFrame(wipeRAF);
      const jitter = XS.map(() => Math.random() * 180); // liquid wobble, fresh each toggle
      const start = performance.now();
      const tick = (now) => {
        const t = now - start;
        let done = true;
        layers.forEach((p, li) => {
          const delay = open ? li * 70 : (3 - li) * 70; // black leads on exit
          if (t < delay + 1380) done = false;
          const ys = XS.map((x, i) =>
            cubicInOut(Math.max(0, Math.min(1, (t - delay - jitter[i]) / 1200))) * 100
          );
          p.setAttribute('d', edgePath(ys, open));
        });
        if (!done) wipeRAF = requestAnimationFrame(tick);
      };
      wipeRAF = requestAnimationFrame(tick);
    };

    const closeMenu = () => {
      if (!mobileMenu.classList.contains('open')) return;
      mobileMenu.classList.remove('open');
      burger.classList.remove('is-open');
      document.body.classList.remove('menu-open');
      document.body.style.overflow = '';
      runWipe(false);
    };
    burger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      burger.classList.toggle('is-open', isOpen);
      // lift the header (and its burger) above the open menu overlay
      document.body.classList.toggle('menu-open', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
      runWipe(isOpen);
    });
    // Nav links navigate immediately on click — no close animation;
    // the wipe/stagger choreography is reserved for the burger and Escape.
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
  // (Awwwards "horizontal scroll gallery"-inspired, with a zeustheagency.com-style
  //  rotating-wheel orbit: items sit on a drum, tilting away from center stage)
  const filmstripWrapper = document.getElementById('filmstripWrapper');
  const filmstripTrack = document.getElementById('filmstripTrack');
  if (filmstripWrapper && filmstripTrack) {
    const orbitItems = filmstripTrack.querySelectorAll('.filmstrip-item');
    const MAX_TILT = 38;      // deg of rotateY at the screen edges
    const MAX_DEPTH = 140;    // px an edge item recedes into the drum
    const MIN_SCALE = 0.82;   // scale of an item at the far edge
    const MIN_OPACITY = 0.32; // opacity of an item at the far edge
    const SMOOTHING = 0.12;   // lerp factor — lower = silkier, more lag

    let smoothShift = null;
    let rafId = 0;

    const filmstripTick = () => {
      const rect = filmstripWrapper.getBoundingClientRect();
      const total = rect.height - window.innerHeight;
      let progress = total > 0 ? -rect.top / total : 0;
      progress = Math.max(0, Math.min(1, progress));
      const maxTranslate = Math.max(filmstripTrack.scrollWidth - window.innerWidth, 0);
      const targetShift = progress * maxTranslate;
      // Ease the raw scroll position toward its target instead of snapping
      // 1:1 to it — turns the mechanical scrollbar-linked motion into a
      // fluid, slightly-trailing drift (zeustheagency.com's carousels never
      // move in lockstep with the scrollbar).
      smoothShift = smoothShift === null ? targetShift : smoothShift + (targetShift - smoothShift) * SMOOTHING;
      filmstripTrack.style.transform = `translateX(${-smoothShift}px)`;

      // Rotating orbit: each item tilts, recedes, shrinks and dims on the
      // wheel according to its distance from center stage (computed from
      // layout, not live rects, so there's no feedback loop with the
      // transforms we set). The scale/opacity falloff is what actually
      // sells the drum illusion — tilt alone reads as flat cards leaning.
      const vw = window.innerWidth;
      const flat = vw < 768;
      orbitItems.forEach((item) => {
        const center = item.offsetLeft + item.offsetWidth / 2 - smoothShift;
        const t = Math.max(-1, Math.min(1, (center - vw / 2) / (vw / 2)));
        const falloff = Math.abs(t);
        const opacity = 1 - falloff * (1 - MIN_OPACITY);

        if (flat) {
          // Mobile: no 3D tilt (perspective looks janky on small/low-power
          // screens), but keep the scale + opacity falloff for depth.
          const scale = 1 - falloff * (1 - MIN_SCALE) * 0.6;
          item.style.transform = `scale(${scale})`;
          item.style.opacity = String(opacity);
          return;
        }

        const tilt = -t * MAX_TILT;
        const depth = -falloff * MAX_DEPTH;
        const scale = 1 - falloff * (1 - MIN_SCALE);
        item.style.transform = `rotateY(${tilt}deg) translateZ(${depth}px) scale(${scale})`;
        item.style.opacity = String(opacity);
      });

      rafId = requestAnimationFrame(filmstripTick);
    };

    // Only run the loop while the section is actually on screen — it was
    // previously ticking forever, every frame, for the entire page lifetime.
    const filmstripIo = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          smoothShift = null; // re-snap to the correct position, don't drift in from the last spot
          cancelAnimationFrame(rafId);
          rafId = requestAnimationFrame(filmstripTick);
        } else {
          cancelAnimationFrame(rafId);
        }
      });
    });
    filmstripIo.observe(filmstripWrapper);
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

  // ============ LIGHTBOX — Morph Slider viewer for gallery items ============
  // Makes the custom cursor's "View" promise real: click opens a WebGL Morph
  // Slider (see morph-slider.js) showing every currently-filtered portfolio
  // image, with a shader crossfade between frames on prev/next/drag.
  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    const closeBtn = document.getElementById('lightboxClose');
    const stage = document.getElementById('morphSliderStage');
    const captionEl = document.getElementById('morphSliderCaption');
    const indicatorsEl = document.getElementById('morphSliderIndicators');
    const prevBtn = document.getElementById('morphSliderPrev');
    const nextBtn = document.getElementById('morphSliderNext');

    const getVisibleItems = () =>
      Array.from(document.querySelectorAll('.gallery-item')).filter(el => !el.classList.contains('hidden-item'));

    const buildSlides = () => getVisibleItems().map((el) => {
      const img = el.querySelector('img');
      const overlay = el.querySelector('.absolute');
      const caption = overlay?.children[1]?.textContent?.trim() || img.alt || '';
      return { image: img.src.replace(/w=\d+/, 'w=1800'), caption };
    });

    let engine = null;
    let isOpen = false;

    const renderCaption = (slides, index) => {
      captionEl.innerHTML = slides.map((slide, i) => slide.caption
        ? `<span class="morph-slider-caption-text${i === index ? ' is-active' : ''}">${slide.caption}</span>`
        : '').join('');
    };

    const renderIndicators = (slides, index) => {
      indicatorsEl.innerHTML = slides.map((_, i) =>
        `<button type="button" class="morph-slider-dot${i === index ? ' is-active' : ''}" data-index="${i}" aria-label="Go to image ${i + 1}"></button>`
      ).join('');
      indicatorsEl.querySelectorAll('.morph-slider-dot').forEach((dot) => {
        dot.addEventListener('click', () => {
          if (!engine) return;
          const target = parseInt(dot.dataset.index, 10);
          if (target === engine.current) return;
          engine.goTo(target > engine.current ? 1 : -1);
        });
      });
    };

    const openLightbox = (index) => {
      const slides = buildSlides();
      if (!slides.length) return;

      lightbox.classList.add('is-open');
      lightbox.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      isOpen = true;

      if (engine) engine.destroy();
      stage.innerHTML = '';

      engine = new MorphSlider(stage, {
        items: slides,
        startIndex: index,
        opts: {
          transition: 'melt',
          duration: 1.1,
          intensity: 0.55,
          scale: 2.4,
          aberration: 0.35,
          drift: 0.4,
          overlayColor: '#0a0908',
          loop: true,
        },
        onIndexChange: (i) => {
          renderCaption(slides, i);
          renderIndicators(slides, i);
        },
      });

      renderCaption(slides, index);
      renderIndicators(slides, index);
    };

    const closeLightbox = () => {
      // Move focus out before hiding — aria-hidden on an ancestor of the
      // still-focused close/nav button is itself an accessibility violation.
      if (lightbox.contains(document.activeElement)) document.activeElement.blur();
      lightbox.classList.remove('is-open');
      lightbox.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      isOpen = false;
      if (engine) { engine.destroy(); engine = null; }
    };

    document.querySelectorAll('.gallery-item').forEach((item) => {
      item.addEventListener('click', () => {
        openLightbox(getVisibleItems().indexOf(item));
      });
    });

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    prevBtn.addEventListener('click', () => engine?.prev());
    nextBtn.addEventListener('click', () => engine?.next());

    document.addEventListener('keydown', (e) => {
      if (!isOpen) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') engine?.prev();
      if (e.key === 'ArrowRight') engine?.next();
    });
  }

  // ============ BACK TO TOP ============
  // Injected on every page so no HTML edits are needed anywhere.
  const backToTop = document.createElement('button');
  backToTop.className = 'back-to-top';
  backToTop.setAttribute('aria-label', 'Back to top');
  backToTop.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>';
  document.body.appendChild(backToTop);
  window.addEventListener('scroll', () => {
    backToTop.classList.toggle('is-visible', window.scrollY > 600);
  });
  backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});
