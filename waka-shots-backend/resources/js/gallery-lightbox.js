import { MorphSlider } from './morph-slider';
import { createSliderUI } from './slider-ui';

// ============ CLIENT GALLERY LIGHTBOX ============
// Same WebGL Morph Slider as the public portfolio (see main.js), wired up
// separately here because this page has its own trigger markup
// (.lightbox-trigger / data-full-image) rather than the portfolio's
// .gallery-item.
//
// Slides load from gallery.thumb, a same-origin proxy of Google's
// thumbnailLink (same as the grid, just readable as a WebGL texture) rather
// than the authenticated gallery.preview route, which re-fetches the full
// original from Drive on every request: appropriately slow for a deliberate
// "Download" click, far too slow to gate simply viewing a photo.
//
// Even the thumb proxy is not free: each request costs the server a Drive
// metadata call plus an upstream fetch, and `php artisan serve` handles one
// request at a time. So the slider keeps to its default preload radius (the
// current photo and its immediate neighbours) instead of pulling the whole
// gallery through the proxy the moment the viewer opens.
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
  const ui = createSliderUI({ captionEl, indicatorsEl, getEngine: () => engine });

  const openLightbox = (index) => {
    const slides = buildSlides();
    if (!slides.length) return;

    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    document.body.classList.add('lightbox-open');
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
      onIndexChange: (i) => ui.update(i),
    });

    ui.render(slides, index);
  };

  const closeLightbox = () => {
    if (lightbox.contains(document.activeElement)) document.activeElement.blur();
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    document.body.classList.remove('lightbox-open');
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
