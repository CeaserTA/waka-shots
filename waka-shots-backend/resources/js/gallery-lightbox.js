import { MorphSlider } from './morph-slider';

// ============ CLIENT GALLERY LIGHTBOX — same WebGL Morph Slider as the
// public portfolio (see main.js), wired up separately here because this
// page has its own trigger markup (.lightbox-trigger / data-full-image)
// rather than the portfolio's .gallery-item. Slides use Google's
// thumbnailLink (same as the grid) rather than the authenticated
// gallery.preview proxy — that route re-fetches the full original from
// Drive on every request, which is appropriately slow for a deliberate
// "Download" click but far too slow to gate simply viewing a photo.
document.addEventListener('DOMContentLoaded', () => {
  const lightbox = document.getElementById('galleryLightbox');
  if (!lightbox) return;

  const closeBtn = document.getElementById('galleryLightboxClose');
  const stage = document.getElementById('galleryMorphStage');
  const captionEl = document.getElementById('galleryMorphCaption');
  const indicatorsEl = document.getElementById('galleryMorphIndicators');
  const prevBtn = document.getElementById('galleryMorphPrev');
  const nextBtn = document.getElementById('galleryMorphNext');

  const getTriggers = () => Array.from(document.querySelectorAll('.lightbox-trigger'));

  const buildSlides = () => getTriggers().map((el) => ({
    image: el.dataset.fullImage,
    caption: el.dataset.imageName || '',
  }));

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
        // Slides load Google's own thumbnailLink CDN, same as the grid —
        // fast and not subject to our gallery.preview rate limit, so no
        // need to restrict how much the slider preloads in the background
        // (see MorphSlider's default preloadRadius).
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
    if (lightbox.contains(document.activeElement)) document.activeElement.blur();
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    isOpen = false;
    if (engine) { engine.destroy(); engine = null; }
  };

  getTriggers().forEach((trigger, index) => {
    trigger.addEventListener('click', () => openLightbox(index));
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
});
