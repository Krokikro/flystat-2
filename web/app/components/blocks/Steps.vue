<script setup lang="ts">
/** Numbered steps on a timeline: horizontal on desktop, vertical on narrow screens. The connector line is drawn as you scroll and each node lights up when the line reaches it. */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const n = computed(() => (props.data.items || []).length || 1)
useScrollFx(({ gsap }) => {
  if (!root.value) return
  const fill = root.value.querySelector('.fill'); const lis = Array.from(root.value.querySelectorAll<HTMLElement>('.step'))
  const horizontal = window.matchMedia('(min-width: 1100px)').matches
  const tl = gsap.timeline({ scrollTrigger: { trigger: root.value.querySelector('.list'), start: horizontal ? 'top 75%' : 'top 70%', end: horizontal ? 'bottom 60%' : 'bottom 55%', scrub: .6 } })
  tl.fromTo(fill, horizontal ? { scaleX: 0 } : { scaleY: 0 }, horizontal ? { scaleX: 1, ease: 'none' } : { scaleY: 1, ease: 'none' }, 0)
  tl.eventCallback('onUpdate', () => { const p = tl.progress(); lis.forEach((li, i) => li.classList.toggle('lit', p > (i + .4) / lis.length)) })
  return tl
})
</script>
<template>
  <section ref="root" class="section steps band-alt">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" />
      <ol class="list" :style="{ '--n': n }">
        <i class="track" aria-hidden="true"><i class="fill" /></i>
        <li v-for="(it, i) in data.items" :key="i" class="step" v-reveal="i * 70">
          <div class="node"><UiIcon :name="it.icon" :size="24" /></div>
          <span class="n mono">{{ String(i + 1).padStart(2, '0') }}</span>
          <h3>{{ it.title }}</h3>
          <p>{{ it.text }}</p>
        </li>
      </ol>
      <div v-if="data.cta?.label" class="more" v-reveal><UiCtaLink :cta="data.cta" variant="btn-navy" /></div>
    </div>
  </section>
</template>
<style scoped>
.steps { overflow: hidden; }
.list { list-style: none; margin: 0; padding: 0; position: relative; display: grid; grid-template-columns: repeat(var(--n), minmax(0, 1fr)); gap: clamp(16px, 2vw, 32px); }
.track { position: absolute; left: 32px; right: calc(100% / var(--n) - 32px); top: 32px; height: 2px; background: var(--line); border-radius: 2px; }
.fill { position: absolute; inset: 0; background: linear-gradient(90deg, var(--blue), var(--purple)); transform-origin: left center; transform: scaleX(0); }
.step { position: relative; display: grid; gap: 10px; align-content: start; padding-top: 0; }
.node { width: 64px; height: 64px; border-radius: 50%; display: grid; place-items: center; background: #fff; color: var(--muted); border: 2px solid var(--line); box-shadow: 0 0 0 10px var(--bg-alt); transition: color .5s, border-color .5s, transform .6s var(--ease), box-shadow .6s var(--ease); margin-bottom: 12px; }
.step.lit .node { color: var(--blue-d); border-color: var(--blue); transform: scale(1.06); box-shadow: 0 0 0 10px var(--bg-alt), 0 14px 30px -10px rgba(46,144,250,.5); }
.n { font-size: .75rem; color: var(--muted); letter-spacing: .16em; } .step.lit .n { color: var(--blue-d); }
.step h3 { font-size: clamp(1.1rem, 1rem + .35vw, 1.4rem); letter-spacing: -.015em; }
.step p { color: var(--ink-2); font-size: clamp(.95rem, 1rem + .05vw, 1.05rem); line-height: 1.5; }
.more { margin-top: clamp(40px, 4vw, 64px); }
@media (max-width: 1099px) {
  .list { grid-template-columns: 1fr; gap: 0; padding-left: 0; }
  .track { left: 31px; right: auto; top: 0; bottom: 0; width: 2px; height: auto; }
  .fill { transform-origin: center top; transform: scaleY(0); background: linear-gradient(180deg, var(--blue), var(--purple)); }
  .step { grid-template-columns: 64px 1fr; grid-template-rows: auto auto auto; column-gap: 24px; row-gap: 6px; padding-bottom: clamp(28px, 5vw, 48px); }
  .node { grid-row: 1 / span 3; margin: 0; }
  .n { grid-column: 2; } .step h3 { grid-column: 2; } .step p { grid-column: 2; }
  .step:last-child { padding-bottom: 0; }
  .step.lit .node { transform: none; }
}
@media (prefers-reduced-motion: reduce) { .fill { transform: none !important; } .node { color: var(--blue-d); border-color: var(--blue); } }
</style>
