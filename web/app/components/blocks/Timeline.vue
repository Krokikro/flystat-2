<script setup lang="ts">
/** Horizontal timeline on desktop (progress line drawn on scroll), vertical on phones. Data: { title, items[{date,title,text}] }. */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const progress = ref(0)
const items = computed(() => (props.data.items || []) as { date: string; title: string; text: string }[])
const year = new Date().getFullYear()
const future = (d: string) => { const m = String(d || '').match(/(20\d{2})/); return m ? Number(m[1]) > year : false }
onMounted(() => {
  const el = root.value; if (!el) return
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { progress.value = 1; return }
  let raf = 0, active = false
  const update = () => {
    raf = 0
    const r = el.getBoundingClientRect(); const vh = window.innerHeight
    // 0 when the list enters the lower third of the viewport, 1 when its bottom reaches the upper third
    const p = (vh * 0.78 - r.top) / (r.height + vh * 0.3)
    progress.value = Math.max(0, Math.min(1, p))
  }
  const onScroll = () => { if (active && !raf) raf = requestAnimationFrame(update) }
  const io = new IntersectionObserver((en) => { active = en[0]?.isIntersecting ?? false; if (active) update() }, { rootMargin: '20% 0px 20% 0px' })
  io.observe(el); window.addEventListener('scroll', onScroll, { passive: true }); window.addEventListener('resize', onScroll)
  onBeforeUnmount(() => { io.disconnect(); window.removeEventListener('scroll', onScroll); window.removeEventListener('resize', onScroll); if (raf) cancelAnimationFrame(raf) })
})
</script>
<template>
  <section class="section tl">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <ol ref="root" class="line" :style="{ '--n': items.length, '--p': progress }">
        <li v-for="(it, i) in items" :key="i" :class="{ planned: future(it.date), lit: progress >= (i + 0.5) / items.length }" v-reveal="i * 90">
          <span class="date">{{ it.date }}</span>
          <span class="node"><span class="core" /></span>
          <div class="body"><h3>{{ it.title }}</h3><p>{{ it.text }}</p></div>
        </li>
        <span class="track" aria-hidden="true"><span class="fill" /></span>
      </ol>
    </div>
  </section>
</template>
<style scoped>
.line { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(var(--n), minmax(0, 1fr)); gap: clamp(16px, 2vw, 32px); position: relative; }
.line li { display: grid; grid-template-rows: auto auto 1fr; gap: 14px; position: relative; min-width: 0; }
.date { font-family: var(--mono); font-size: .8rem; letter-spacing: .14em; text-transform: uppercase; color: var(--blue-d); font-weight: 500; padding-left: 2px; }
.node { width: 28px; height: 28px; border-radius: 50%; background: #fff; border: 2px solid var(--line); display: grid; place-items: center; position: relative; z-index: 1; transition: border-color .5s var(--ease), box-shadow .5s var(--ease), transform .5s var(--ease); }
.core { width: 10px; height: 10px; border-radius: 50%; background: var(--line); transition: background .5s, transform .5s var(--ease); transform: scale(.6); }
li.lit .node { border-color: var(--blue); box-shadow: 0 0 0 6px rgba(46, 144, 250, .14); }
li.lit .core { background: var(--blue); transform: scale(1); }
li.planned .node { border-style: dashed; }
li.planned.lit .node { border-color: var(--purple); box-shadow: 0 0 0 6px rgba(122, 90, 248, .14); } li.planned.lit .core { background: var(--purple); }
li.planned .date { color: var(--purple-d); }
.body { display: grid; gap: 8px; align-content: start; padding: clamp(18px, 2vw, 26px); border-radius: var(--radius); background: var(--bg-alt); border: 1px solid transparent; transition: transform .6s var(--ease), background .4s, box-shadow .6s var(--ease); }
.body h3 { font-size: clamp(1.1rem, 1rem + .35vw, 1.35rem); }
.body p { color: var(--ink-2); font-size: .95rem; line-height: 1.5; }
li.lit .body { background: #fff; border-color: var(--line); box-shadow: 0 16px 40px rgba(16, 42, 86, .08); }
li.planned .body { background: linear-gradient(160deg, #F6F3FF, #F0F4FE); }
/* track behind the nodes (date row ≈ 1.2rem + 14px gap, node 28px → centre) */
.track { position: absolute; left: 14px; right: 14px; top: calc(1.2rem + 14px + 13px); height: 2px; background: var(--line); border-radius: 2px; overflow: hidden; }
.fill { position: absolute; inset: 0; background: linear-gradient(90deg, var(--blue), var(--purple)); transform-origin: left center; transform: scaleX(var(--p)); transition: transform .15s linear; }
@media (max-width: 860px) {
  .line { grid-template-columns: 1fr; gap: 0; padding-left: 44px; }
  .line li { grid-template-rows: none; grid-template-columns: 1fr; gap: 10px; padding-bottom: 28px; }
  .line li:last-child { padding-bottom: 0; }
  .node { position: absolute; left: -44px; top: 0; }
  .track { left: 13px; right: auto; top: 14px; bottom: 14px; width: 2px; height: auto; }
  .fill { transform-origin: top center; transform: scaleY(var(--p)); background: linear-gradient(180deg, var(--blue), var(--purple)); }
  .body { padding: 18px 20px; }
}
</style>
