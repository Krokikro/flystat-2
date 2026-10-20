<script setup lang="ts">
/** Full-width dark band: a headline, a floating sensor render with a soft light behind it, and large counters that count up when they enter. */
const props = defineProps<{ data: Record<string, any> }>()
const nums = ref<HTMLElement[]>([])
const art = ref<HTMLElement | null>(null)
const root = ref<HTMLElement | null>(null)
const render = computed(() => props.data.image?.src || '/media/renders/applicator-top.webp')
const numeric = (v: string) => /^-?\d+(\.\d+)?$/.test(String(v).trim())

onMounted(() => {
  if (reducedMotion()) return
  const io = new IntersectionObserver((es) => es.forEach((e) => {
    if (!e.isIntersecting) return; io.unobserve(e.target)
    const el = e.target as HTMLElement; const raw = el.dataset.v || ''
    if (!numeric(raw)) return
    const target = parseFloat(raw); const dec = raw.includes('.') ? raw.split('.')[1].length : 0
    const t0 = performance.now(); const dur = 1500 + Math.min(600, target * 8)
    const step = (now: number) => { const k = Math.min(1, (now - t0) / dur); const e2 = 1 - Math.pow(1 - k, 4); el.textContent = (target * e2).toFixed(dec); if (k < 1) requestAnimationFrame(step); else el.textContent = raw }
    requestAnimationFrame(step)
  }), { threshold: 0.6 })
  nums.value.forEach((n) => io.observe(n))
  onBeforeUnmount(() => io.disconnect())
})
useParallax(art, 40, root)
useScrollFx(({ gsap }) => root.value && gsap.fromTo(root.value.querySelector('.glow'), { scale: .7, opacity: 0 }, { scale: 1, opacity: 1, ease: 'none', scrollTrigger: { trigger: root.value, start: 'top 80%', end: 'top 20%', scrub: .5 } }))
</script>
<template>
  <section ref="root" class="section stats band-dark">
    <i class="glow" aria-hidden="true" />
    <div class="container">
      <div class="top">
        <UiSectionHead v-if="data.title" :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" tight class="head" />
        <div ref="art" class="art" aria-hidden="true"><img :src="mediaUrl(render)" alt="" width="1033" height="1355" loading="lazy" decoding="async"></div>
      </div>
      <div class="row" :style="{ '--n': data.items?.length || 4 }">
        <div v-for="(s, i) in data.items" :key="i" class="stat" v-reveal="i * 90">
          <div class="val"><span ref="nums" :data-v="s.value">{{ s.value }}</span><small v-if="s.suffix">{{ s.suffix }}</small></div>
          <h3>{{ s.label }}</h3>
          <p>{{ s.text }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.stats { position: relative; overflow: hidden; background: linear-gradient(180deg, var(--navy-800) 0%, var(--navy-900) 100%); padding-bottom: clamp(104px, 11vw, 190px); isolation: isolate; }
.glow { position: absolute; right: -10%; top: -20%; width: 70vw; height: 70vw; max-width: 1100px; max-height: 1100px; border-radius: 50%; background: radial-gradient(closest-side, rgba(46,144,250,.38) 0%, rgba(122,90,248,.16) 45%, transparent 72%); pointer-events: none; z-index: 0; transform-origin: 60% 40%; }
.container { position: relative; z-index: 1; }
.top { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); align-items: center; gap: clamp(24px, 4vw, 64px); margin-bottom: clamp(40px, 5vw, 88px); }
.head { margin-bottom: 0; }
.head :deep(h2) { color: #fff; } .head :deep(.lead) { color: var(--muted-on-dark); }
.art { justify-self: end; width: min(100%, clamp(260px, 26vw, 420px)); will-change: transform; }
.art img { width: 100%; height: auto; filter: drop-shadow(0 60px 60px rgba(0,0,0,.55)) drop-shadow(0 0 40px rgba(143,183,255,.25)); }
.row { display: grid; grid-template-columns: repeat(var(--n, 4), minmax(0, 1fr)); gap: 0; border-top: 1px solid rgba(255,255,255,.14); }
.stat { display: grid; gap: 12px; align-content: start; padding: clamp(28px, 3vw, 48px) clamp(20px, 2.4vw, 40px) 0 0; position: relative; }
.stat + .stat { padding-left: clamp(20px, 2.4vw, 40px); border-left: 1px solid rgba(255,255,255,.12); }
.val { display: flex; align-items: baseline; gap: .08em; font-weight: 900; font-size: clamp(3.6rem, 1rem + 5.4vw, 7.4rem); letter-spacing: -.045em; line-height: .95; color: #fff; font-variant-numeric: tabular-nums; background: linear-gradient(180deg, #fff 30%, #BFD4FF 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.val small { font-size: .32em; font-weight: 700; letter-spacing: -.01em; -webkit-text-fill-color: #8FB7FF; }
.stat h3 { color: #fff; font-size: clamp(1.15rem, 1rem + .5vw, 1.5rem); }
.stat p { color: var(--muted-on-dark); font-size: 1rem; max-width: 30ch; line-height: 1.5; }
@media (max-width: 1100px) { .row { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 8px; } .stat + .stat { padding-left: 0; border-left: 0; } .stat:nth-child(even) { padding-left: clamp(20px, 3vw, 40px); border-left: 1px solid rgba(255,255,255,.12); } .stat:nth-child(n+3) { border-top: 1px solid rgba(255,255,255,.12); } }
@media (max-width: 860px) { .top { grid-template-columns: 1fr; } .art { justify-self: center; width: min(70%, 340px); margin-top: -8px; } }
@media (max-width: 560px) { .row { grid-template-columns: 1fr; } .stat:nth-child(even) { padding-left: 0; border-left: 0; } .stat:nth-child(n+2) { border-top: 1px solid rgba(255,255,255,.12); } .stat { padding-top: 24px; padding-bottom: 8px; } .val { font-size: 3.6rem; } }
</style>
