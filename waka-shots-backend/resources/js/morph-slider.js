// Vanilla WebGL + rAF-tween port of the reactbits.dev "Morph Slider" component.
// Same shader (melt / ripple / shear / swirl) and drag-to-swipe behaviour as the
// React/OGL/GSAP original, rebuilt on raw WebGL1 with no external dependencies —
// this project has no animation libraries installed, and every other interaction
// in main.js is hand-rolled the same way.

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

float fbm(vec2 p) {
  float v = 0.0;
  float a = 0.5;
  for (int i = 0; i < 5; i++) {
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

// "Cover" fit (like CSS object-fit: cover): scales UV so the image always fills
// the whole frame, cropping whatever overflows the mismatched aspect ratio.
vec2 coverUV(vec2 uv, vec2 res, vec2 img) {
  float rA = res.x / max(res.y, 1.0);
  float iA = img.x / max(img.y, 1.0);
  vec2 s = vec2(1.0);
  float ratio = rA / max(iA, 0.0001);
  if (ratio > 1.0) {
    s.y = 1.0 / ratio;
  } else {
    s.x = ratio;
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

  if (uReduce < 0.5) {
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

  vec2 sC = coverUV(uvC, uResolution, uCurrentSize);
  vec2 sN = coverUV(uvN, uResolution, uNextSize);

  float ca = uReduce < 0.5 ? uAberration * env * 0.03 : 0.0;

  vec3 colC = vec3(
    texture2D(tCurrent, sC + vec2(ca, 0.0)).r,
    texture2D(tCurrent, sC).g,
    texture2D(tCurrent, sC - vec2(ca, 0.0)).b
  );
  vec3 colN = vec3(
    texture2D(tNext, sN + vec2(ca, 0.0)).r,
    texture2D(tNext, sN).g,
    texture2D(tNext, sN - vec2(ca, 0.0)).b
  );

  vec3 col = mix(colC, colN, m);

  float vig = smoothstep(1.25, 0.25, length(uv - 0.5));
  col = mix(col, uOverlay, (1.0 - vig) * 0.28);

  gl_FragColor = vec4(col, 1.0);
}
`;

function compileShader(gl, type, source) {
  const shader = gl.createShader(type);
  gl.shaderSource(shader, source);
  gl.compileShader(shader);
  if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
    const info = gl.getShaderInfoLog(shader);
    gl.deleteShader(shader);
    throw new Error('MorphSlider shader compile error: ' + info);
  }
  return shader;
}

function createProgram(gl, vertexSource, fragmentSource) {
  const program = gl.createProgram();
  gl.attachShader(program, compileShader(gl, gl.VERTEX_SHADER, vertexSource));
  gl.attachShader(program, compileShader(gl, gl.FRAGMENT_SHADER, fragmentSource));
  gl.linkProgram(program);
  if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
    const info = gl.getProgramInfoLog(program);
    gl.deleteProgram(program);
    throw new Error('MorphSlider program link error: ' + info);
  }
  return program;
}

function setTextureParams(gl) {
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
}

function makeFallbackTexture(gl) {
  const texture = gl.createTexture();
  gl.bindTexture(gl.TEXTURE_2D, texture);
  gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1, 1, 0, gl.RGBA, gl.UNSIGNED_BYTE, new Uint8Array([24, 24, 28, 255]));
  setTextureParams(gl);
  return texture;
}

function uploadTexture(gl, texture, image) {
  gl.bindTexture(gl.TEXTURE_2D, texture);
  gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
  gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, image);
  setTextureParams(gl);
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
    }, opts);

    this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    this.current = startIndex;
    this.shownIndex = startIndex;
    this.animating = false;
    this.dragging = false;
    this.dragDir = 0;
    this.tweenRAF = null;
    this.dprCap = 2;

    const canvas = document.createElement('canvas');
    canvas.className = 'morph-slider-canvas';
    container.appendChild(canvas);
    this.canvas = canvas;

    const gl = canvas.getContext('webgl', { alpha: false, antialias: true })
      || canvas.getContext('experimental-webgl', { alpha: false, antialias: true });
    if (!gl) throw new Error('WebGL is not supported in this browser');
    this.gl = gl;
    gl.clearColor(0.02, 0.02, 0.024, 1);

    this.program = createProgram(gl, VERTEX_SHADER, FRAGMENT_SHADER);
    gl.useProgram(this.program);

    // Fullscreen triangle (avoids a quad's diagonal seam) — clipped to the
    // viewport, its UVs land exactly on 0..1 across the visible area.
    const positionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    const positionLoc = gl.getAttribLocation(this.program, 'position');
    gl.enableVertexAttribArray(positionLoc);
    gl.vertexAttribPointer(positionLoc, 2, gl.FLOAT, false, 0, 0);

    const uvBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, uvBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0, 0, 2, 0, 0, 2]), gl.STATIC_DRAW);
    const uvLoc = gl.getAttribLocation(this.program, 'uv');
    gl.enableVertexAttribArray(uvLoc);
    gl.vertexAttribPointer(uvLoc, 2, gl.FLOAT, false, 0, 0);

    this.uniforms = {};
    UNIFORM_NAMES.forEach((name) => {
      this.uniforms[name] = gl.getUniformLocation(this.program, name);
    });

    this.textures = this.items.map(() => makeFallbackTexture(gl));
    this.sizes = this.items.map(() => [1, 1]);
    this.nextTexture = this.textures[this.current];
    this.loadTextures();

    gl.uniform1i(this.uniforms.tCurrent, 0);
    gl.uniform1i(this.uniforms.tNext, 1);
    gl.uniform2fv(this.uniforms.uCurrentSize, this.sizes[this.current]);
    gl.uniform2fv(this.uniforms.uNextSize, this.sizes[this.current]);
    gl.uniform1f(this.uniforms.uProgress, 0);
    gl.uniform1f(this.uniforms.uDir, 1);
    gl.uniform1i(this.uniforms.uMode, TRANSITIONS[this.opts.transition] ?? 0);
    gl.uniform1f(this.uniforms.uIntensity, this.opts.intensity);
    gl.uniform1f(this.uniforms.uScale, this.opts.scale);
    gl.uniform1f(this.uniforms.uAberration, this.opts.aberration);
    gl.uniform1f(this.uniforms.uDrift, this.opts.drift);
    gl.uniform1f(this.uniforms.uReduce, this.reducedMotion ? 1 : 0);
    gl.uniform2fv(this.uniforms.uPointer, [0.5, 0.5]);
    gl.uniform3fv(this.uniforms.uOverlay, hexToRgb(this.opts.overlayColor));

    this.resizeObserver = new ResizeObserver(() => this.resize());
    this.resizeObserver.observe(container);
    this.resize();

    this.startTime = performance.now();
    this.raf = requestAnimationFrame((t) => this.loop(t));

    this.bindPointerEvents();
  }

  loadTextures() {
    const gl = this.gl;
    this.items.forEach((item, index) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.src = item.image;
      img.onload = () => {
        const texture = gl.createTexture();
        uploadTexture(gl, texture, img);
        this.textures[index] = texture;
        this.sizes[index] = [img.naturalWidth || 1, img.naturalHeight || 1];
        if (index === this.current) {
          gl.uniform2fv(this.uniforms.uCurrentSize, this.sizes[index]);
          this.nextTexture = this.textures[index];
        }
      };
      img.onerror = () => {};
    });
  }

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
    this.gl.uniform2f(this.uniforms.uResolution, w, h);
  }

  loop(t) {
    const gl = this.gl;
    gl.uniform1f(this.uniforms.uTime, (t - this.startTime) * 0.001);

    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, this.textures[this.current]);
    gl.activeTexture(gl.TEXTURE1);
    gl.bindTexture(gl.TEXTURE_2D, this.nextTexture || this.textures[this.current]);

    gl.clear(gl.COLOR_BUFFER_BIT);
    gl.drawArrays(gl.TRIANGLES, 0, 3);

    this.raf = requestAnimationFrame((nt) => this.loop(nt));
  }

  wrap(i) {
    const n = this.items.length;
    return ((i % n) + n) % n;
  }

  prepareNext(dir) {
    const target = this.wrap(this.current + dir);
    this.nextTexture = this.textures[target];
    this.gl.uniform2fv(this.uniforms.uCurrentSize, this.sizes[this.current]);
    this.gl.uniform2fv(this.uniforms.uNextSize, this.sizes[target]);
    this.gl.uniform1f(this.uniforms.uDir, dir);
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
    const gl = this.gl;
    const step = (now) => {
      const t = Math.min((now - start) / 1000 / duration, 1);
      gl.uniform1f(this.uniforms.uProgress, from + (to - from) * power2InOut(t));
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
    this.gl.uniform2fv(this.uniforms.uCurrentSize, this.sizes[target]);
    this.gl.uniform1f(this.uniforms.uProgress, 0);
    this.animating = false;
    this.announce(target);
  }

  next() { this.goTo(1); }
  prev() { this.goTo(-1); }

  setPointer(x, y) {
    this.gl.uniform2f(this.uniforms.uPointer, x, y);
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
        this.gl.uniform1f(this.uniforms.uProgress, 0);
        return;
      }
    }
    if (dir !== this.dragDir) {
      this.dragDir = dir;
      this.prepareNext(dir);
    }
    const progress = Math.min(Math.abs(ndx), 1);
    this.gl.uniform1f(this.uniforms.uProgress, progress);
    this.announce(progress > 0.5 ? this.wrap(this.current + dir) : this.current);
  }

  endDrag() {
    if (!this.dragging) return;
    this.dragging = false;
    if (this.dragDir === 0) return;
    const target = this.wrap(this.current + this.dragDir);
    const duration = this.reducedMotion ? 0.3 : 0.5;
    this.animating = true;
    const p = this.gl.getUniform(this.program, this.uniforms.uProgress) || 0;

    if (p > 0.4) {
      this.announce(target);
      this.tween(p, 1, duration, () => this.commit(target));
    } else {
      this.announce(this.current);
      this.tween(p, 0, duration, () => { this.animating = false; });
    }
  }

  destroy() {
    cancelAnimationFrame(this.raf);
    if (this.tweenRAF) cancelAnimationFrame(this.tweenRAF);
    this.resizeObserver.disconnect();

    const el = this.canvas;
    el.removeEventListener('pointerdown', this.onDown);
    el.removeEventListener('pointermove', this.onMove);
    el.removeEventListener('pointerup', this.onUp);
    el.removeEventListener('pointercancel', this.onUp);

    const gl = this.gl;
    this.textures.forEach((tex) => gl.deleteTexture(tex));
    gl.deleteProgram(this.program);
    const ext = gl.getExtension('WEBGL_lose_context');
    if (ext) ext.loseContext();
    if (this.canvas.parentNode) this.canvas.parentNode.removeChild(this.canvas);
  }
}
