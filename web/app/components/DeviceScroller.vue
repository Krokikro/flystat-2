<script setup lang="ts">
/**
 * The hero "3D": the studio's real device animation (232 frames) scrubbed by scroll, drawn on a canvas,
 * with a Three.js environment behind it (volumetric glow, particles, light ring) that reacts to scroll and pointer.
 * Falls back to the mp4 / a poster when WebGL or motion are unavailable.
 */
const props = withDefaults(defineProps<{ progress: number; scene?: 'rotate' | 'exploded' | 'sensor'; frames?: number; base?: string }>(), { scene: 'rotate', frames: 232, base: '/media/hero/h_' })

const canvas = ref<HTMLCanvasElement | null>(null)
const glCanvas = ref<HTMLCanvasElement | null>(null)
const wrap = ref<HTMLElement | null>(null)
const ready = ref(false)
const reduced = ref(false)

const images: (HTMLImageElement | null)[] = []
let loaded = 0
let ctx: CanvasRenderingContext2D | null = null
let raf = 0
let idleT = 0
let current = -1
let dpr = 1

const src = (i: number) => `${props.base}${String(i).padStart(3, '0')}.webp`

function frameFor(p: number) {
  // scene offsets: start the story at a different moment per page
  const start = props.scene === 'exploded' ? 0.28 : props.scene === 'sensor' ? 0.55 : 0
  const q = Math.min(1, Math.max(0, start + p * (1 - start)))
  return Math.min(props.frames - 1, Math.round(q * (props.frames - 1)))
}

function resize() {
  if (!canvas.value || !wrap.value) return
  dpr = Math.min(1.5, window.devicePixelRatio || 1)
  const r = wrap.value.getBoundingClientRect()
  canvas.value.width = Math.round(r.width * dpr); canvas.value.height = Math.round(r.height * dpr)
  canvas.value.style.width = r.width + 'px'; canvas.value.style.height = r.height + 'px'
  current = -1
  draw(lastIndex)
}

let lastIndex = 0
function draw(i: number) {
  lastIndex = i
  if (!ctx || !canvas.value) return
  // nearest loaded frame
  let j = i; while (j >= 0 && !images[j]) j--
  if (j < 0) { j = i; while (j < props.frames && !images[j]) j++ }
  if (j < 0 || j >= props.frames || !images[j] || j === current) return
  current = j
  const img = images[j]!
  const W = canvas.value.width, H = canvas.value.height
  // cover-fit, bias composition slightly to the right on wide screens so text has room
  const s = Math.max(W / img.width, H / img.height) * (W / H > 1.2 ? 1.0 : 1.15)
  const w = img.width * s, h = img.height * s
  const dx = (W - w) / 2 + (W / H > 1.2 ? W * 0.12 : 0), dy = (H - h) / 2
  ctx.clearRect(0, 0, W, H)
  ctx.drawImage(img, dx, dy, w, h)
}

function load() {
  // priority order: first, last, middle, then fill — so scrubbing works early
  const order: number[] = []
  const n = props.frames
  const push = (i: number) => { if (i >= 0 && i < n && !order.includes(i)) order.push(i) }
  push(frameFor(0)); push(n - 1); push(Math.floor(n / 2)); push(Math.floor(n / 4)); push(Math.floor(3 * n / 4))
  for (let step = 8; step >= 1; step = Math.floor(step / 2)) for (let i = 0; i < n; i += step) push(i)
  let idx = 0
  const next = () => {
    if (idx >= order.length) return
    const i = order[idx++]
    const im = new Image(); im.decoding = 'async'
    im.onload = () => { images[i] = im; loaded++; if (!ready.value) { ready.value = true; draw(lastIndex) } else if (i === lastIndex || current !== lastIndex) draw(lastIndex); next() }
    im.onerror = () => next()
    im.src = src(i)
  }
  // 4 parallel lanes
  for (let k = 0; k < 4; k++) next()
}

// idle autoplay before the user scrolls: a gentle loop over the first third
let idleFrom = performance.now()
function idle(now: number) {
  idleT = requestAnimationFrame(idle)
  if (props.progress > 0.01 || reduced.value) return
  if (wrap.value && wrap.value.getBoundingClientRect().bottom < 0) return
  const t = ((now - idleFrom) / 1000) % 9
  const f = t < 4.5 ? t / 4.5 : 1 - (t - 4.5) / 4.5
  draw(Math.round(f * 60))
}

watch(() => props.progress, (p) => { if (p > 0.005) draw(frameFor(p)) })

// ---------------- Three.js environment
let three: { dispose: () => void; setProgress: (p: number) => void; setPointer: (x: number, y: number) => void } | null = null
async function initThree() {
  if (!glCanvas.value || reduced.value) return
  try {
    const THREE = await import('three')
    const renderer = new THREE.WebGLRenderer({ canvas: glCanvas.value, antialias: true, alpha: true, powerPreference: 'low-power' })
    renderer.setPixelRatio(Math.min(1.5, window.devicePixelRatio || 1))
    const scene = new THREE.Scene()
    const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100); camera.position.z = 10
    // volumetric glow: layered soft sprites
    const glowTex = (() => { const c = document.createElement('canvas'); c.width = c.height = 256; const x = c.getContext('2d')!; const g = x.createRadialGradient(128, 128, 0, 128, 128, 128); g.addColorStop(0, 'rgba(70,160,255,0.55)'); g.addColorStop(0.35, 'rgba(46,144,250,0.22)'); g.addColorStop(1, 'rgba(46,144,250,0)'); x.fillStyle = g; x.fillRect(0, 0, 256, 256); return new THREE.CanvasTexture(c) })()
    const glows: any[] = []
    for (let i = 0; i < 3; i++) { const m = new THREE.Sprite(new THREE.SpriteMaterial({ map: glowTex, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, opacity: 0.9 - i * 0.25 })); m.scale.setScalar(7 + i * 4); m.position.set(1.5 + i * 0.3, 0.2 - i * 0.2, -2 - i); scene.add(m); glows.push(m) }
    // particles with depth
    const N = 420; const pos = new Float32Array(N * 3); const sz = new Float32Array(N)
    for (let i = 0; i < N; i++) { pos[i * 3] = (Math.random() - 0.5) * 24; pos[i * 3 + 1] = (Math.random() - 0.5) * 14; pos[i * 3 + 2] = -Math.random() * 12; sz[i] = Math.random() }
    const pg = new THREE.BufferGeometry(); pg.setAttribute('position', new THREE.BufferAttribute(pos, 3)); pg.setAttribute('aSize', new THREE.BufferAttribute(sz, 1))
    const pm = new THREE.ShaderMaterial({ transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, uniforms: { uTime: { value: 0 }, uPix: { value: renderer.getPixelRatio() } },
      vertexShader: `attribute float aSize; uniform float uTime; uniform float uPix; varying float vA; void main(){ vec3 p = position; p.y += sin(uTime*0.25 + position.x*0.5)*0.25; p.x += cos(uTime*0.18 + position.y*0.7)*0.2; vec4 mv = modelViewMatrix*vec4(p,1.0); gl_Position = projectionMatrix*mv; float s = (0.6 + aSize*2.2) * uPix; gl_PointSize = s * (9.0 / -mv.z); vA = 0.25 + aSize*0.55; }`,
      fragmentShader: `varying float vA; void main(){ float d = length(gl_PointCoord - 0.5); float a = smoothstep(0.5, 0.05, d) * vA; gl_FragColor = vec4(0.62, 0.78, 1.0, a); }` })
    const points = new THREE.Points(pg, pm); scene.add(points)
    // light ring
    const ring = new THREE.Mesh(new THREE.TorusGeometry(3.6, 0.012, 8, 160), new THREE.MeshBasicMaterial({ color: 0x8fb7ff, transparent: true, opacity: 0.35 }))
    ring.position.set(1.6, 0, -1); ring.rotation.x = Math.PI / 2.4; scene.add(ring)
    const ring2 = ring.clone(); ring2.scale.setScalar(1.45); (ring2.material as any) = ring.material.clone(); (ring2.material as any).opacity = 0.15; scene.add(ring2)

    let px = 0, py = 0, prog = 0, t0 = performance.now(), alive = true, visible = true
    const vio = new IntersectionObserver((es) => { visible = es[0]?.isIntersecting ?? true; if (visible && alive) { cancelAnimationFrame(raf); raf = requestAnimationFrame(loop) } }, { threshold: 0 })
    if (wrap.value) vio.observe(wrap.value)
    const fit = () => { const r = glCanvas.value!.getBoundingClientRect(); renderer.setSize(r.width, r.height, false); camera.aspect = r.width / r.height; camera.updateProjectionMatrix() }
    fit()
    const loop = (now: number) => {
      if (!alive || !visible) return
      raf = requestAnimationFrame(loop)
      const t = (now - t0) / 1000
      pm.uniforms.uTime.value = t
      camera.position.x += (px * 0.6 - camera.position.x) * 0.04; camera.position.y += (-py * 0.4 - camera.position.y) * 0.04; camera.lookAt(0, 0, 0)
      ring.rotation.z = t * 0.08; ring2.rotation.z = -t * 0.05
      const k = 1 + prog * 0.9; ring.scale.setScalar(k); ring2.scale.setScalar(1.45 * k)
      glows.forEach((g, i) => { g.material.opacity = (0.9 - i * 0.25) * (1 - prog * 0.55); g.position.x = 1.5 + i * 0.3 - prog * 1.5 })
      points.position.y = prog * 2.2
      renderer.render(scene, camera)
    }
    raf = requestAnimationFrame(loop)
    const onResize = () => fit(); window.addEventListener('resize', onResize)
    three = { setProgress: (p) => (prog = p), setPointer: (x, y) => { px = x; py = y }, dispose: () => { alive = false; cancelAnimationFrame(raf); window.removeEventListener('resize', onResize); vio.disconnect(); renderer.dispose(); pg.dispose(); pm.dispose() } }
  } catch (e) { /* no WebGL — fine, the frames still work */ }
}
watch(() => props.progress, (p) => three?.setProgress(p))
function onPointer(e: PointerEvent) { if (!wrap.value) return; const r = wrap.value.getBoundingClientRect(); three?.setPointer(((e.clientX - r.left) / r.width - 0.5) * 2, ((e.clientY - r.top) / r.height - 0.5) * 2) }

onMounted(() => {
  reduced.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  ctx = canvas.value?.getContext('2d', { alpha: true }) || null
  resize(); load(); initThree()
  window.addEventListener('resize', resize)
  if (!reduced.value) idleT = requestAnimationFrame(idle)
})
onBeforeUnmount(() => { window.removeEventListener('resize', resize); cancelAnimationFrame(idleT); three?.dispose() })
defineExpose({ ready })
</script>
<template>
  <div ref="wrap" class="dev" :class="{ ready }" @pointermove="onPointer">
    <canvas ref="glCanvas" class="gl" aria-hidden="true" />
    <canvas ref="canvas" class="frames" role="img" aria-label="Flystat device animation" />
    <div class="vignette" aria-hidden="true" />
  </div>
</template>
<style scoped>
.dev { position: absolute; inset: 0; overflow: hidden; background: radial-gradient(60% 70% at 65% 45%, #0E2659 0%, #06264D 45%, #06264D 100%); }
.gl, .frames { position: absolute; inset: 0; width: 100%; height: 100%; }
.frames { opacity: 0; transition: opacity .9s ease; mix-blend-mode: screen; }
.dev.ready .frames { opacity: 1; }
.vignette { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(6,38,77,.92) 0%, rgba(6,38,77,.55) 32%, rgba(6,38,77,0) 55%), linear-gradient(180deg, rgba(6,38,77,.35) 0%, rgba(6,38,77,0) 25%, rgba(6,38,77,0) 70%, rgba(6,38,77,1) 100%); pointer-events: none; }
@media (max-width: 760px) { .vignette { background: linear-gradient(180deg, rgba(6,38,77,.2) 0%, rgba(6,38,77,0) 30%, rgba(6,38,77,.75) 70%, rgba(6,38,77,1) 100%); } }
</style>
