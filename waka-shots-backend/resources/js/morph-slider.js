// Vanilla WebGL + rAF-tween port of the reactbits.dev "Morph Slider" component.
// Same shader (melt / ripple / shear / swirl) and drag-to-swipe behaviour as the
// React/OGL/GSAP original, rebuilt on raw WebGL1 with no external dependencies:
// this project has no animation libraries installed, and every other interaction
// in main.js is hand-rolled the same way.
//
// Performance notes (this runs full-screen over the whole site, often on
// integrated GPUs, so it has to be cheap):
//   * It renders on demand. There is no free-running requestAnimationFrame
//     loop: a frame is drawn only while a transition/drag is running, or once
//     after something changes (image loaded, resize). A still image costs no
//     GPU time at all.
//   * The shader skips its noise/warp work and the second texture whenever no
//     transition is running (progress == 0).
//   * The shader is compiled with KHR_parallel_shader_compile where available,
//     so opening the viewer does not block the main thread on the driver.
//   * Only the images around the current slide live on the GPU; the rest of
//     the gallery is not downloaded up front and far-away textures are freed.

const TRANSITIONS = { melt: 0, ripple: 1, shear: 2, swirl: 3 };

const VERTEX_SHADER = `
attribute vec2 position;
attribute vec2 uv;
varying vec2 vUv;
void main() {
  vUv = uv;
  gl_Position = vec4(position, 0.0, 1.0);
}
`;

const FRAGMENT_SHADER = `
precision highp float;

uniform sampler2D tCurrent;
uniform sampler2D tNext;
uniform vec2 uResolution;
uniform vec2 uCurrentSize;
uniform vec2 uNextSize;
uniform float uProgress;
uniform float uDir;
uniform int uMode;
uniform float uIntensity;
uniform float uScale;
uniform float uAberration;
uniform float uDrift;
uniform float uTime;
uniform float uReduce;
uniform vec2 uPointer;
uniform vec3 uOverlay;

varying vec2 vUv;

const float PI = 3.14159265359;

float hash11(float p) {
  p = fract(p * 0.1031);
  p *= p + 33.33;
  p *= p + p;
  return fract(p);
}

float hash21(vec2 p) {
  vec3 p3 = fract(vec3(p.xyx) * 0.1031);
  p3 += dot(p3, p3.yzx + 33.33);
  return fract((p3.x + p3.y) * p3.z);
}

float noise(vec2 p) {
  vec2 i = floor(p);
  vec2 f = fract(p);
  vec2 u = f * f * (3.0 - 2.0 * f);
  float a = hash21(i);
  float b = hash21(i + vec2(1.0, 0.0));
  float c = hash21(i + vec2(0.0, 1.0));
  float d = hash21(i + vec2(1.0, 1.0));
  return mix(mix(a, b, u.x), mix(c, d, u.x), u.y);
}

// 4 octaves rather than the original 5: the 5th adds ~3% to the noise
// amplitude (invisible in a 1s warp) but is ~36% of the per-frame GPU cost
// of the melt transition on an integrated GPU at 1.5x pixel density.
float fbm(vec2 p) {
  float v = 0.0;
  float a = 0.5;
  for (int i = 0; i < 4; i++) {
    v += a * noise(p);
    p *= 2.0;
    a *= 0.5;
  }
  return v;
}

mat2 rot(float a) {
  float s = sin(a);
  float c = cos(a);
  return mat2(c, -s, s, c);
}

// "Contain" fit (like CSS object-fit: contain): scales UV so the whole image
// is always visible, letterboxing/pillarboxing whatever doesn't fill the
// mismatched aspect ratio instead of cropping it away. A portrait photo in a
// landscape-shaped stage gets bars on the sides, not its top/bottom cut off.
vec2 containUV(vec2 uv, vec2 res, vec2 img) {
  float rA = res.x / max(res.y, 1.0);
  float iA = img.x / max(img.y, 1.0);
  vec2 s = vec2(1.0);
  float ratio = rA / max(iA, 0.0001);
  if (ratio > 1.0) {
    s.x = ratio;
  } else {
    s.y = 1.0 / ratio;
  }
  return (uv - 0.5) * s + 0.5;
}

void main() {
  float p = clamp(uProgress, 0.0, 1.0);
  float env = sin(p * PI);

  vec2 uv = vUv;

  uv += vec2(sin(uTime * 0.25 + uv.y * 4.0), cos(uTime * 0.22 + uv.x * 4.0)) * uDrift * 0.008;
  uv = (uv - 0.5) * (1.0 - uDrift * 0.02 * sin(uTime * 0.4)) + 0.5;

  vec2 uvC = uv;
  vec2 uvN = uv;
  float m = smoothstep(0.0, 1.0, p);

  // The warp below is the expensive part (the melt mode alone is two 5-octave
  // noise loops per pixel). With no transition running (p == 0) it has no
  // visible effect, so a still image skips it entirely. uProgress is a
  // uniform, so every pixel takes the same branch.
  if (uReduce < 0.5 && p > 0.0) {
    if (uMode == 3) {
      vec2 c = uv - 0.5;
      float r = length(c);
      float ang = env * uIntensity * 3.5 * (1.0 - r);
      uvC = rot(ang) * c + 0.5;
      uvN = rot(-ang) * c + 0.5;
      m = smoothstep(0.0, 1.0, p);
    } else if (uMode == 1) {
      float d = distance(uv, uPointer);
      float ring = p * 1.6;
      float wave = sin((d - ring) * 30.0) * env;
      vec2 dir = normalize(uv - uPointer + 1e-4);
      vec2 disp = dir * wave * uIntensity * 0.25;
      uvC = uv + disp;
      uvN = uv + disp * 0.6;
      m = 1.0 - smoothstep(ring - 0.03, ring + 0.03, d);
    } else if (uMode == 2) {
      float slices = 14.0;
      float row = floor(uv.y * slices);
      float rnd = hash11(row);
      vec2 disp = vec2((rnd - 0.5) * env * uIntensity * 0.6, 0.0);
      uvC = uv + disp;
      uvN = uv + disp;
      float localX = uDir > 0.0 ? uv.x : 1.0 - uv.x;
      float th = p * 1.5 - 0.25 + (rnd - 0.5) * 0.25;
      m = 1.0 - smoothstep(th - 0.06, th + 0.06, localX);
    } else {
      float nn = fbm(uv * uScale + uTime * 0.03);
      float warp = fbm(uv * uScale * 1.7 - uTime * 0.02);
      vec2 g = vec2(nn, warp) - 0.5;
      uvC = uv + g * uIntensity * 0.5 * p;
      uvN = uv - g * uIntensity * 0.5 * (1.0 - p);
      m = smoothstep(nn - 0.15, nn + 0.15, p);
    }
  }

  vec2 sC = containUV(uvC, uResolution, uCurrentSize);

  float ca = uReduce < 0.5 ? uAberration * env * 0.03 : 0.0;

  vec3 colC = vec3(
    texture2D(tCurrent, sC + vec2(ca, 0.0)).r,
    texture2D(tCurrent, sC).g,
    texture2D(tCurrent, sC - vec2(ca, 0.0)).b
  );

  // Outside the letterboxed image bounds there's nothing to show: fall back
  // to the background colour rather than the smeared, clamped-to-edge pixel
  // texture2D() would otherwise return for out-of-range UVs.
  float inC = step(0.0, sC.x) * step(sC.x, 1.0) * step(0.0, sC.y) * step(sC.y, 1.0);
  vec3 col = mix(uOverlay, colC, inC);

  // The next slide is only sampled while a transition is running.
  if (p > 0.0) {
    vec2 sN = containUV(uvN, uResolution, uNextSize);
    vec3 colN = vec3(
      texture2D(tNext, sN + vec2(ca, 0.0)).r,
      texture2D(tNext, sN).g,
      texture2D(tNext, sN - vec2(ca, 0.0)).b
    );
    float inN = step(0.0, sN.x) * step(sN.x, 1.0) * step(0.0, sN.y) * step(sN.y, 1.0);
    colN = mix(uOverlay, colN, inN);
    col = mix(col, colN, m);
  }

  float vig = smoothstep(1.25, 0.25, length(uv - 0.5));
  col = mix(col, uOverlay, (1.0 - vig) * 0.28);

  gl_FragColor = vec4(col, 1.0);
}
`;

// Kicks off compile + link WITHOUT reading any status back. Reading
// COMPILE_STATUS / LINK_STATUS (or asking for uniform/attribute locations)
// blocks the main thread until the GPU driver has finished, which on Windows
// (ANGLE/D3D11) on an integrated GPU is hundreds of milliseconds. The caller
// polls for completion instead; see MorphSlider#tryFinishProgram.
function startProgram(gl, vertexSource, fragmentSource) {
  const vertex = gl.createShader(gl.VERTEX_SHADER);
  gl.shaderSource(vertex, vertexSource);
  gl.compileShader(vertex);

  const fragment = gl.createShader(gl.FRAGMENT_SHADER);
  gl.shaderSource(fragment, fragmentSource);
  gl.compileShader(fragment);

  const program = gl.createProgram();
  gl.attachShader(program, vertex);
  gl.attachShader(program, fragment);
  // Fixed attribute slots so the vertex buffers can be wired up without
  // getAttribLocation(), which would force the same blocking wait.
  gl.bindAttribLocation(program, 0, 'position');
  gl.bindAttribLocation(program, 1, 'uv');
  gl.linkProgram(program);

  return { program, vertex, fragment };
}

function assertProgramLinked(gl, { program, vertex, fragment }) {
  if (gl.getProgramParameter(program, gl.LINK_STATUS)) return;
  const log = [
    gl.getShaderInfoLog(vertex),
    gl.getShaderInfoLog(fragment),
    gl.getProgramInfoLog(program),
  ].filter(Boolean).join('\n');
  throw new Error('MorphSlider shader failed to build: ' + log);
}

// `mipmap` is only ever true on a WebGL2 context, where a non-power-of-two
// texture can carry a full mip chain. Trilinear minification is what stops a
// downscaled photo from aliasing into sparkle; anisotropy (when the driver
// offers it) keeps that from over-blurring the axis that is not minified.
function setTextureParams(gl, mipmap) {
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);

  if (!mipmap) {
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    return;
  }

  gl.generateMipmap(gl.TEXTURE_2D);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR_MIPMAP_LINEAR);

  const aniso = gl.getExtension('EXT_texture_filter_anisotropic')
    || gl.getExtension('WEBKIT_EXT_texture_filter_anisotropic');
  if (aniso) {
    const max = gl.getParameter(aniso.MAX_TEXTURE_MAX_ANISOTROPY_EXT);
    gl.texParameterf(gl.TEXTURE_2D, aniso.TEXTURE_MAX_ANISOTROPY_EXT, Math.min(8, max));
  }
}

function makeFallbackTexture(gl) {
  const texture = gl.createTexture();
  gl.bindTexture(gl.TEXTURE_2D, texture);
  gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1, 1, 0, gl.RGBA, gl.UNSIGNED_BYTE, new Uint8Array([24, 24, 28, 255]));
  setTextureParams(gl, false); // a 1x1 placeholder is never minified
  return texture;
}

function uploadTexture(gl, texture, image, mipmap) {
  gl.bindTexture(gl.TEXTURE_2D, texture);
  gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
  gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, image);
  setTextureParams(gl, mipmap);
}

function hexToRgb(hex) {
  let h = (hex || '#000000').replace('#', '');
  if (h.length === 3) h = h.split('').map((c) => c + c).join('');
  const n = parseInt(h, 16);
  return [((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
}

// Matches GSAP's default "power2.inOut" curve used by the reference component.
function power2InOut(t) {
  return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
}

const UNIFORM_NAMES = [
  'tCurrent', 'tNext', 'uResolution', 'uCurrentSize', 'uNextSize', 'uProgress',
  'uDir', 'uMode', 'uIntensity', 'uScale', 'uAberration', 'uDrift', 'uTime',
  'uReduce', 'uPointer', 'uOverlay',
];

export class MorphSlider {
  // Downloading every gallery image the instant the slider opens saturates the
  // browser's per-origin connection pool and fires a burst of GPU texture
  // uploads (each one a main-thread stall, and each a full-resolution texture
  // held in GPU memory). Instead: load the image actually being viewed first,
  // then its neighbours, capped to a few concurrent downloads so the current
  // image never has to compete with a dozen others for bandwidth.
  static LOAD_CONCURRENCY = 3;

  // Every image is fetched under a URL that differs from the plain <img> one
  // on the page (e.g. the portfolio grid thumbnail). Some CDNs (R2's public
  // *.r2.dev domain included) cache by URL only and ignore Vary: Origin, so
  // whichever request hits a given URL first "wins" the cache for everyone
  // after it, CORS headers and all. Requesting a distinct URL here guarantees
  // this fetch can never be served that stale, header-less cache entry.
  static corsUrl(url) {
    return url + (url.includes('?') ? '&' : '?') + 'cors=1';
  }

  static warmed = new Set();

  // Starts downloading an image into the HTTP cache ahead of time (e.g. while
  // the pointer rests on a grid thumbnail) so that opening it is near-instant.
  // Uses exactly the request the slider itself will make.
  static prefetch(url) {
    if (!url || MorphSlider.warmed.has(url)) return;
    if (navigator.connection?.saveData) return;
    MorphSlider.warmed.add(url);
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.decoding = 'async';
    img.src = MorphSlider.corsUrl(url);
  }

  constructor(container, { items, startIndex = 0, opts = {}, onIndexChange = () => {} }) {
    this.container = container;
    this.items = items;
    this.onIndexChange = onIndexChange;
    this.opts = Object.assign({
      transition: 'melt',
      duration: 1.1,
      intensity: 0.55,
      scale: 2.4,
      aberration: 0.35,
      drift: 0.4,
      overlayColor: '#0a0908',
      loop: true,
      // How many neighbours on each side of the current slide are kept loaded
      // (queued for background loading, and protected from eviction). Anything
      // further away loads on demand the moment the viewer navigates to it
      // (see prioritizeLoad) and its texture is freed once they move on.
      // Pass Infinity to load and keep the whole set.
      preloadRadius: 2,
    }, opts);

    const radius = this.opts.preloadRadius;
    this.keepRadius = Number.isFinite(radius) ? Math.max(1, radius) + 1 : Infinity;

    this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    this.current = startIndex;
    this.nextIndex = startIndex;
    this.shownIndex = startIndex;
    this.animating = false;
    this.dragging = false;
    this.dragDir = 0;
    this.tweenRAF = null;
    this.raf = 0;
    // 1.5 rather than 2: the fragment shader is per-pixel work, and the source
    // images are at most ~2500px wide anyway, so rendering above 1.5x buys
    // nothing visible but costs ~45% more pixels on a HiDPI screen.
    this.dprCap = 1.5;

    // All shader inputs live in JS and are pushed to the GPU in render(), so
    // they can be set at any time, even before the program has finished
    // compiling.
    this.progress = 0;
    this.dir = 1;
    this.pointer = [0.5, 0.5];
    this.width = 1;
    this.height = 1;
    this.time = 0;
    this.lastFrame = 0;
    this.programReady = false;
    this.programFailed = false;

    const canvas = document.createElement('canvas');
    canvas.className = 'morph-slider-canvas';
    container.appendChild(canvas);
    this.canvas = canvas;

    // No MSAA: this draws one full-screen quad of a photo, there are no
    // geometry edges to smooth, and a multisampled back buffer is pure cost.
    //
    // WebGL2 is tried first purely for texture filtering: WebGL1 cannot mipmap
    // a non-power-of-two texture, and photos never are, so it is stuck with
    // plain LINEAR. Minifying a 2500px photo into a ~1100px stage that way
    // samples 4 texels per pixel and aliases — fine detail (hair, fabric,
    // foliage) breaks into sparkling speckles. WebGL2 mipmaps NPOT textures
    // normally, so the same draw resolves cleanly. GLSL ES 1.00 shaders with
    // no #version directive compile unchanged on both.
    const attrs = { alpha: false, antialias: false };
    const gl = canvas.getContext('webgl2', attrs)
      || canvas.getContext('webgl', attrs)
      || canvas.getContext('experimental-webgl', attrs);
    if (!gl) throw new Error('WebGL is not supported in this browser');
    this.gl = gl;
    this.canMipmap = typeof WebGL2RenderingContext !== 'undefined'
      && gl instanceof WebGL2RenderingContext;
    gl.clearColor(0.02, 0.02, 0.024, 1);

    this.parallelCompile = gl.getExtension('KHR_parallel_shader_compile');
    this.build = startProgram(gl, VERTEX_SHADER, FRAGMENT_SHADER);
    this.program = this.build.program;

    // Fullscreen triangle (avoids a quad's diagonal seam), clipped to the
    // viewport, so its UVs land exactly on 0..1 across the visible area.
    const positionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    gl.enableVertexAttribArray(0);
    gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 0, 0);

    const uvBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, uvBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0, 0, 2, 0, 0, 2]), gl.STATIC_DRAW);
    gl.enableVertexAttribArray(1);
    gl.vertexAttribPointer(1, 2, gl.FLOAT, false, 0, 0);

    // One shared placeholder until a slide's real image has loaded.
    this.fallback = makeFallbackTexture(gl);
    this.textures = this.items.map(() => this.fallback);
    this.sizes = this.items.map(() => [1, 1]);
    this.loaded = this.items.map(() => false);
    this.loading = new Set();
    this.failed = new Set();
    this.deferred = new Map(); // index -> decoded image waiting for a quiet moment to upload
    this.pendingImages = new Set();
    this.loadQueue = [];
    this.loadActive = 0;
    this.refreshLoading();
    this.queueAround();

    this.resizeObserver = new ResizeObserver(() => this.resize());
    this.resizeObserver.observe(container);
    this.resize();

    this.requestRender();
    this.bindPointerEvents();
  }

  // ---------- program ----------

  // Called each frame until the program is usable. Returns true once it is.
  tryFinishProgram() {
    if (this.programReady) return true;
    if (this.programFailed) return false;
    const gl = this.gl;

    if (this.parallelCompile
      && !gl.getProgramParameter(this.program, this.parallelCompile.COMPLETION_STATUS_KHR)) {
      return false; // driver still compiling on its own thread; check next frame
    }

    try {
      assertProgramLinked(gl, this.build);
    } catch (err) {
      this.programFailed = true;
      console.error(err);
      return false;
    }

    gl.useProgram(this.program);
    this.uniforms = {};
    UNIFORM_NAMES.forEach((name) => {
      this.uniforms[name] = gl.getUniformLocation(this.program, name);
    });

    // Settings that never change after construction.
    gl.uniform1i(this.uniforms.tCurrent, 0);
    gl.uniform1i(this.uniforms.tNext, 1);
    gl.uniform1i(this.uniforms.uMode, TRANSITIONS[this.opts.transition] ?? 0);
    gl.uniform1f(this.uniforms.uIntensity, this.opts.intensity);
    gl.uniform1f(this.uniforms.uScale, this.opts.scale);
    gl.uniform1f(this.uniforms.uAberration, this.opts.aberration);
    gl.uniform1f(this.uniforms.uDrift, this.opts.drift);
    gl.uniform1f(this.uniforms.uReduce, this.reducedMotion ? 1 : 0);
    gl.uniform3fv(this.uniforms.uOverlay, hexToRgb(this.opts.overlayColor));

    this.programReady = true;
    this.refreshLoading();
    return true;
  }

  // ---------- loading ----------

  // Shown while the current slide's texture (or the shader) is still being
  // prepared; without it the frozen placeholder frame during a slow fetch
  // looks indistinguishable from the UI having hung.
  refreshLoading() {
    const busy = !this.programFailed && (!this.programReady || !this.loaded[this.current]);
    this.container.classList.toggle('is-loading', busy);
  }

  distance(a, b) {
    const d = Math.abs(a - b);
    return this.opts.loop ? Math.min(d, this.items.length - d) : d;
  }

  priorityOrder(start) {
    const n = this.items.length;
    const order = [start];
    for (let d = 1; d < n && d <= this.opts.preloadRadius; d++) {
      order.push(this.wrap(start + d));
      order.push(this.wrap(start - d));
    }
    return [...new Set(order)];
  }

  // Loads the current slide first, then outward through its neighbours.
  queueAround() {
    this.loadQueue = this.priorityOrder(this.current)
      .filter((i) => !this.loaded[i] && !this.loading.has(i) && !this.deferred.has(i));
    this.pumpQueue();
  }

  loadOne(index) {
    if (this.loaded[index] || this.loading.has(index) || this.deferred.has(index)) return;
    this.loading.add(index);
    this.loadActive++;

    const gl = this.gl;
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.decoding = 'async';
    this.pendingImages.add(img);
    img.src = MorphSlider.corsUrl(this.items[index].image);

    const finish = () => {
      this.pendingImages.delete(img);
      this.loading.delete(index);
      this.loadActive--;
      if (!this.destroyed) this.pumpQueue();
    };

    img.onload = () => {
      if (this.destroyed) { finish(); return; }
      // Uploading a full-size photo to the GPU blocks the main thread for
      // ~100-200ms on an integrated GPU. If that lands in the middle of a
      // transition or drag it shows up as a visible hitch, so a background
      // slide that arrives mid-motion waits until the motion settles.
      if ((this.animating || this.dragging) && index !== this.current && index !== this.nextIndex) {
        this.deferred.set(index, img);
      } else {
        this.uploadLoaded(index, img);
      }
      finish();
    };
    img.onerror = () => {
      this.failed.add(index); // lets the queue move on instead of waiting on it forever
      finish();
    };
  }

  uploadLoaded(index, img) {
    this.deferred.delete(index);
    // The viewer has moved well away while this was downloading: don't spend
    // a main-thread GPU upload (and GPU memory) on a slide they can't see.
    if (index !== this.current && index !== this.nextIndex
      && this.distance(index, this.current) > this.keepRadius) {
      return;
    }
    const gl = this.gl;
    const texture = gl.createTexture();
    uploadTexture(gl, texture, img, this.canMipmap);
    this.textures[index] = texture;
    this.sizes[index] = [img.naturalWidth || 1, img.naturalHeight || 1];
    this.loaded[index] = true;
    if (index === this.current) this.refreshLoading();
    if (index === this.current || index === this.nextIndex) this.requestRender();
    this.evictFar();
  }

  // Called once a transition/drag has settled.
  flushDeferred() {
    if (this.destroyed || this.animating || this.dragging) return;
    this.deferred.forEach((img, index) => this.uploadLoaded(index, img));
  }

  pumpQueue() {
    while (this.loadActive < MorphSlider.LOAD_CONCURRENCY && this.loadQueue.length) {
      const next = this.loadQueue[0];
      // Background neighbours must not share bandwidth with the slide being
      // looked at: three parallel downloads each get a third of the pipe, so
      // the photo the viewer actually clicked would arrive up to 3x later.
      // They wait until the current slide has loaded (or definitively failed).
      const urgent = next === this.current || next === this.nextIndex;
      const currentSettled = this.loaded[this.current] || this.failed.has(this.current);
      if (!urgent && !currentSettled) break;
      this.loadQueue.shift();
      this.loadOne(next);
    }
  }

  // Jumps an index to the front of the line: used when the viewer navigates
  // to a slide that hasn't loaded yet, so it doesn't wait behind whatever was
  // already queued.
  prioritizeLoad(index) {
    if (this.loaded[index]) return;
    const waiting = this.deferred.get(index);
    if (waiting) { this.uploadLoaded(index, waiting); return; } // already downloaded, needed now
    this.loadQueue = this.loadQueue.filter((i) => i !== index);
    this.loadQueue.unshift(index);
    if (!this.loading.has(index)) this.pumpQueue();
  }

  // Frees the GPU memory of textures the viewer has moved away from. A
  // full-resolution photo is ~15-20MB as a texture; keeping a whole gallery of
  // them resident is what makes a low-end GPU crawl.
  evictFar() {
    if (!Number.isFinite(this.keepRadius)) return;
    const gl = this.gl;
    for (let i = 0; i < this.items.length; i++) {
      if (!this.loaded[i] || i === this.current || i === this.nextIndex) continue;
      if (this.distance(i, this.current) <= this.keepRadius) continue;
      gl.deleteTexture(this.textures[i]);
      this.textures[i] = this.fallback;
      this.sizes[i] = [1, 1];
      this.loaded[i] = false;
    }
  }

  // ---------- rendering ----------

  resize() {
    const rect = this.container.getBoundingClientRect();
    const dpr = Math.min(window.devicePixelRatio || 1, this.dprCap);
    const w = Math.max(Math.round(rect.width * dpr), 1);
    const h = Math.max(Math.round(rect.height * dpr), 1);
    this.canvas.width = w;
    this.canvas.height = h;
    this.canvas.style.width = rect.width + 'px';
    this.canvas.style.height = rect.height + 'px';
    this.gl.viewport(0, 0, w, h);
    this.width = w;
    this.height = h;
    this.requestRender(); // resizing clears the canvas
  }

  // Schedules at most one frame; further calls before it runs are no-ops.
  requestRender() {
    if (this.raf || this.destroyed) return;
    this.raf = requestAnimationFrame((t) => {
      this.raf = 0;
      this.render(t);
    });
  }

  // Draws right now (used from inside an animation frame callback).
  renderNow(t) {
    if (this.raf) {
      cancelAnimationFrame(this.raf);
      this.raf = 0;
    }
    this.render(t);
  }

  render(now) {
    if (this.destroyed) return;
    if (!this.tryFinishProgram()) {
      if (!this.programFailed) this.requestRender(); // still compiling: poll next frame
      return;
    }

    const gl = this.gl;
    const u = this.uniforms;

    // A clock that only advances while frames are being drawn (and never by
    // more than one long frame at a time), so the ambient drift picks up
    // where it left off after an idle stretch instead of jumping.
    if (this.lastFrame) this.time += Math.min((now - this.lastFrame) / 1000, 0.05);
    this.lastFrame = now;

    gl.uniform1f(u.uTime, this.time);
    gl.uniform1f(u.uProgress, this.progress);
    gl.uniform1f(u.uDir, this.dir);
    gl.uniform2f(u.uResolution, this.width, this.height);
    gl.uniform2fv(u.uCurrentSize, this.sizes[this.current]);
    gl.uniform2fv(u.uNextSize, this.sizes[this.nextIndex]);
    gl.uniform2fv(u.uPointer, this.pointer);

    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, this.textures[this.current]);
    gl.activeTexture(gl.TEXTURE1);
    gl.bindTexture(gl.TEXTURE_2D, this.textures[this.nextIndex]);

    gl.clear(gl.COLOR_BUFFER_BIT);
    gl.drawArrays(gl.TRIANGLES, 0, 3);
  }

  wrap(i) {
    const n = this.items.length;
    return ((i % n) + n) % n;
  }

  prepareNext(dir) {
    const target = this.wrap(this.current + dir);
    this.nextIndex = target;
    this.dir = dir;
    this.prioritizeLoad(target);
    return target;
  }

  announce(index) {
    if (index === this.shownIndex) return;
    this.shownIndex = index;
    this.onIndexChange(index);
  }

  goTo(dir) {
    if (this.animating || this.dragging || this.items.length < 2) return;
    if (!this.opts.loop) {
      const raw = this.current + dir;
      if (raw < 0 || raw > this.items.length - 1) return;
    }
    const target = this.prepareNext(dir);
    this.animating = true;
    this.announce(target);

    const duration = this.reducedMotion ? Math.min(this.opts.duration, 0.4) : this.opts.duration;
    this.tween(0, 1, duration, () => this.commit(target));
  }

  tween(from, to, duration, onComplete) {
    if (this.tweenRAF) cancelAnimationFrame(this.tweenRAF);
    const start = performance.now();
    const step = (now) => {
      const t = Math.min(Math.max((now - start) / 1000 / duration, 0), 1);
      this.progress = from + (to - from) * power2InOut(t);
      this.renderNow(now);
      if (t < 1) {
        this.tweenRAF = requestAnimationFrame(step);
      } else {
        this.tweenRAF = null;
        if (onComplete) onComplete();
      }
    };
    this.tweenRAF = requestAnimationFrame(step);
  }

  commit(target) {
    this.current = target;
    this.nextIndex = target;
    this.progress = 0;
    this.animating = false;
    this.announce(target);
    this.refreshLoading();
    this.queueAround();
    this.evictFar();
    this.flushDeferred();
    this.requestRender(); // settle on the cheap still-image frame
  }

  next() { this.goTo(1); }
  prev() { this.goTo(-1); }

  setPointer(x, y) {
    this.pointer = [x, y];
  }

  bindPointerEvents() {
    const el = this.canvas;
    let startX = 0;
    let width = 1;
    let active = false;

    this.onDown = (e) => {
      const rect = el.getBoundingClientRect();
      width = rect.width || 1;
      startX = e.clientX;
      const px = (e.clientX - rect.left) / rect.width;
      const py = (e.clientY - rect.top) / rect.height;
      this.setPointer(px, 1 - py);
      active = this.beginDrag();
      if (active && el.setPointerCapture) {
        try { el.setPointerCapture(e.pointerId); } catch (err) { /* ignore */ }
      }
    };
    this.onMove = (e) => {
      if (!active) return;
      this.drag((e.clientX - startX) / width);
    };
    this.onUp = () => {
      if (!active) return;
      active = false;
      this.endDrag();
    };

    el.addEventListener('pointerdown', this.onDown);
    el.addEventListener('pointermove', this.onMove);
    el.addEventListener('pointerup', this.onUp);
    el.addEventListener('pointercancel', this.onUp);
  }

  beginDrag() {
    if (this.animating || this.items.length < 2) return false;
    this.dragging = true;
    this.dragDir = 0;
    return true;
  }

  drag(ndx) {
    if (!this.dragging) return;
    const dir = ndx < 0 ? 1 : -1;
    if (!this.opts.loop) {
      const raw = this.current + dir;
      if (raw < 0 || raw > this.items.length - 1) {
        this.progress = 0;
        this.requestRender();
        return;
      }
    }
    if (dir !== this.dragDir) {
      this.dragDir = dir;
      this.prepareNext(dir);
    }
    this.progress = Math.min(Math.abs(ndx), 1);
    this.announce(this.progress > 0.5 ? this.wrap(this.current + dir) : this.current);
    this.requestRender(); // pointermove can fire faster than frames: coalesced to one draw per frame
  }

  endDrag() {
    if (!this.dragging) return;
    this.dragging = false;
    if (this.dragDir === 0) { this.flushDeferred(); return; }
    const target = this.wrap(this.current + this.dragDir);
    const duration = this.reducedMotion ? 0.3 : 0.5;
    this.animating = true;
    const p = this.progress;

    if (p > 0.4) {
      this.announce(target);
      this.tween(p, 1, duration, () => this.commit(target));
    } else {
      this.announce(this.current);
      this.tween(p, 0, duration, () => {
        this.animating = false;
        this.flushDeferred();
        this.requestRender();
      });
    }
  }

  destroy() {
    this.destroyed = true;
    if (this.raf) cancelAnimationFrame(this.raf);
    if (this.tweenRAF) cancelAnimationFrame(this.tweenRAF);
    this.raf = 0;
    this.tweenRAF = null;
    this.resizeObserver.disconnect();

    // Stop downloads for slides the viewer never got to (matters most when
    // each one is a proxied request to our own server).
    this.pendingImages.forEach((img) => {
      img.onload = null;
      img.onerror = null;
      img.removeAttribute('src');
    });
    this.pendingImages.clear();
    this.deferred.clear();

    const el = this.canvas;
    el.removeEventListener('pointerdown', this.onDown);
    el.removeEventListener('pointermove', this.onMove);
    el.removeEventListener('pointerup', this.onUp);
    el.removeEventListener('pointercancel', this.onUp);

    const gl = this.gl;
    new Set(this.textures).forEach((tex) => gl.deleteTexture(tex));
    gl.deleteProgram(this.program);
    const ext = gl.getExtension('WEBGL_lose_context');
    if (ext) ext.loseContext();
    if (this.canvas.parentNode) this.canvas.parentNode.removeChild(this.canvas);
  }
}
