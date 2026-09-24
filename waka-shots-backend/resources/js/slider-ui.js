// Caption + dot indicators shared by the public portfolio lightbox (main.js)
// and the private client-gallery lightbox (gallery-lightbox.js).
//
// The previous version of both rebuilt innerHTML on every slide change: one
// caption <span> per slide (each with its own backdrop-filter, all stacked
// over the animating WebGL canvas) plus one dot <button> with its own click
// listener per slide. For a 60-photo gallery that is 60 blurred layers the
// compositor had to re-blend every frame. Now there is exactly one caption
// element at a time, and the dots are built once and only toggled.
export function createSliderUI({ captionEl, indicatorsEl, getEngine }) {
  let slides = [];
  let dots = [];

  const setCaption = (index) => {
    captionEl.textContent = '';
    const text = slides[index]?.caption;
    if (!text) return;
    const span = document.createElement('span');
    span.className = 'morph-slider-caption-text is-active';
    span.textContent = text; // textContent, not innerHTML: file names are not markup
    captionEl.appendChild(span);
  };

  const setActiveDot = (index) => {
    dots.forEach((dot, i) => dot.classList.toggle('is-active', i === index));
  };

  // One delegated listener instead of one per dot.
  indicatorsEl.addEventListener('click', (e) => {
    const dot = e.target.closest('.morph-slider-dot');
    const engine = getEngine();
    if (!dot || !engine) return;
    const target = parseInt(dot.dataset.index, 10);
    if (target === engine.current) return;
    engine.goTo(target > engine.current ? 1 : -1);
  });

  return {
    // Call once when the lightbox opens.
    render(newSlides, index) {
      slides = newSlides;
      const fragment = document.createDocumentFragment();
      dots = slides.map((_, i) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'morph-slider-dot';
        dot.dataset.index = String(i);
        dot.setAttribute('aria-label', `Go to image ${i + 1}`);
        fragment.appendChild(dot);
        return dot;
      });
      indicatorsEl.replaceChildren(fragment);
      setCaption(index);
      setActiveDot(index);
    },
    // Call from the slider's onIndexChange.
    update(index) {
      setCaption(index);
      setActiveDot(index);
    },
  };
}
