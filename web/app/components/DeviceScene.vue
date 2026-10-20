<script setup lang="ts">
/**
 * Hero visual: the real Flystat applicator + sensor (engineering GLB) rendered live with Three.js and
 * driven by scroll progress — assembled → applicator lifts → sensor exploded → sensor close-up.
 * Phones, reduced-motion and no-WebGL get the same story as a baked frame sequence.
 */
const props = withDefaults(defineProps<{ progress: number }>(), {})
const emit = defineEmits<{ (e: 'anchors', v: Record<string, { x: number; y: number; z: number }>): void; (e: 'mode', v: 'live' | 'frames'): void }>()

const wrap = ref<HTMLElement | null>(null)
const canvas = ref<HTMLCanvasElement | null>(null)
const frameCanvas = ref<HTMLCanvasElement | null>(null)
const ready = ref(false)
const loadPct = ref(0)
const mode = ref<'live' | 'frames'>('live')

let scene: any = null
let raf = 0
let visible = true
let alive = true
let pointer = { x: 0, y: 0 }, smooth = { x: 0, y: 0 }
let lastP = -1, dirty = true

function chooseMode() {
  const small = window.matchMedia('(max-width: 760px)').matches
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  const save = (navigator as any).connection?.saveData
  let gl = false
  try { const c = document.createElement('canvas'); gl = !!(c.getContext('webgl2') || c.getContext('webgl')) } catch {}
  mode.value = (small || reduced || save || !gl) ? 'frames' : 'live'
  emit('mode', mode.value)
}

async function initLive() {
  if (!canvas.value || !wrap.value) return
  try {
    const { createDeviceScene } = await import('~/utils/device-scene.js')
    scene = await createDeviceScene(canvas.value, { url: '/models/flystat.glb', onProgress: (f: number) => { loadPct.value = Math.round(f * 100) } })
    if (import.meta.dev || (window as any).__fsDebug) (window as any).__fs = scene
    if (!alive) { scene.dispose(); return }
    fit(); ready.value = true
    const loop = () => {
      if (!alive) return
      raf = requestAnimationFrame(loop)
      if (!visible) return
      smooth.x += (pointer.x - smooth.x) * 0.06; smooth.y += (pointer.y - smooth.y) * 0.06
      const idle = props.progress < 0.02
      const moved = Math.abs(props.progress - lastP) > 0.0005 || Math.abs(pointer.x - smooth.x) > 0.002 || Math.abs(pointer.y - smooth.y) > 0.002
      if (!idle && !moved && !dirty) return
      dirty = false; lastP = props.progress
      scene.setPointer(smooth.x, smooth.y); scene.setProgress(props.progress)
      scene.render(performance.now())
      if (props.progress > 0.45) emit('anchors', scene.anchors())
    }
    raf = requestAnimationFrame(loop)
  } catch (e) {
    // WebGL failed after all — fall back to the frames
    mode.value = 'frames'; emit('mode', 'frames'); await nextTick(); initFrames()
  }
}

function fit() {
  if (!wrap.value) return
  const r = wrap.value.getBoundingClientRect()
  if (scene) { scene.resize(Math.round(r.width), Math.round(r.height)); dirty = true }
  if (frameCanvas.value) { const dpr = Math.min(1.5, window.devicePixelRatio || 1); frameCanvas.value.width = Math.round(r.width * dpr); frameCanvas.value.height = Math.round(r.height * dpr); drawFrame(frameFor(props.progress), true) }
}

// ---------------- frames fallback
const FRAMES = 80
const images: (HTMLImageElement | null)[] = []
let current = -1
const frameFor = (p: number) => Math.min(FRAMES - 1, Math.max(0, Math.round(p * (FRAMES - 1))))
function drawFrame(i: number, force = false) {
  const c = frameCanvas.value; if (!c) return
  let j = i; while (j >= 0 && !images[j]) j--
  if (j < 0) { j = i; while (j < FRAMES && !images[j]) j++ }
  if (j < 0 || j >= FRAMES || !images[j] || (j === current && !force)) return
  current = j
  const ctx = c.getContext('2d')!; const img = images[j]!
  const W = c.width, H = c.height, s = Math.max(W / img.width, H / img.height), w = img.width * s, h = img.height * s
  ctx.drawImage(img, (W - w) / 2, (H - h) / 2, w, h)
}
function initFrames() {
  const order: number[] = []; const push = (i: number) => { if (i >= 0 && i < FRAMES && !order.includes(i)) order.push(i) }
  push(0); push(FRAMES - 1); push(Math.floor(FRAMES / 2))
  for (let step = 8; step >= 1; step = Math.floor(step / 2)) for (let i = 0; i < FRAMES; i += step) push(i)
  let idx = 0, done = 0
  const next = () => {
    if (idx >= order.length) return
    const i = order[idx++]; const im = new Image(); im.decoding = 'async'
    im.onload = () => { images[i] = im; done++; loadPct.value = Math.round(done / FRAMES * 100); if (!ready.value) { ready.value = true; fit() } drawFrame(frameFor(props.progress)); next() }
    im.onerror = () => next()
    im.src = `/media/hero-m/m_${String(i).padStart(3, '0')}.webp`
  }
  for (let k = 0; k < 4; k++) next()
}
watch(() => props.progress, (p) => { if (mode.value === 'frames') drawFrame(frameFor(p)) })

function onPointer(e: PointerEvent) {
  if (!wrap.value || mode.value !== 'live') return
  const r = wrap.value.getBoundingClientRect()
  pointer = { x: ((e.clientX - r.left) / r.width - 0.5) * 2, y: ((e.clientY - r.top) / r.height - 0.5) * 2 }
}

let io: IntersectionObserver | null = null
onMounted(() => {
  chooseMode()
  io = new IntersectionObserver((es) => { visible = es[0]?.isIntersecting ?? true; dirty = true }, { threshold: 0 })
  if (wrap.value) io.observe(wrap.value)
  window.addEventListener('resize', fit)
  if (mode.value === 'live') initLive(); else initFrames()
})
onBeforeUnmount(() => { alive = false; cancelAnimationFrame(raf); window.removeEventListener('resize', fit); io?.disconnect(); scene?.dispose() })
defineExpose({ ready, mode })
</script>
<template>
  <div ref="wrap" class="dev" :class="{ ready, live: mode === 'live' }" @pointermove="onPointer">
    <img class="poster" src="/media/hero/poster.webp" alt="" aria-hidden="true" decoding="async" fetchpriority="high">
    <canvas v-if="mode === 'live'" ref="canvas" class="gl" role="img" aria-label="Flystat applicator and sensor, 3D" />
    <canvas v-else ref="frameCanvas" class="gl" role="img" aria-label="Flystat applicator and sensor" />
    <div v-if="!ready" class="load" aria-hidden="true"><i :style="{ width: loadPct + '%' }" /></div>
    <div class="vignette" aria-hidden="true" />
  </div>
</template>
<style scoped>
.dev { position: absolute; inset: 0; overflow: hidden; background: radial-gradient(70% 70% at 68% 48%, #0C2F63 0%, #06264D 55%, #041C3B 100%); }
.gl, .poster { position: absolute; inset: 0; width: 100%; height: 100%; }
.poster { object-fit: cover; object-position: 68% 50%; opacity: 1; transition: opacity .35s ease .05s; }
.gl { opacity: 0; transition: opacity .35s ease; display: block; }
.dev.ready .gl { opacity: 1; } .dev.ready .poster { opacity: 0; }
.load { position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: rgba(255,255,255,.08); } .load i { display: block; height: 100%; background: #8FB7FF; transition: width .3s ease; }
.vignette { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(6,38,77,.9) 0%, rgba(6,38,77,.45) 30%, rgba(6,38,77,0) 52%), linear-gradient(180deg, rgba(6,38,77,.25) 0%, rgba(6,38,77,0) 22%, rgba(6,38,77,0) 72%, rgba(6,38,77,1) 100%); pointer-events: none; }
@media (max-width: 760px) { .poster { object-position: 50% 40%; } .vignette { background: linear-gradient(180deg, rgba(6,38,77,.15) 0%, rgba(6,38,77,0) 25%, rgba(6,38,77,.7) 68%, rgba(6,38,77,1) 100%); } }
</style>
