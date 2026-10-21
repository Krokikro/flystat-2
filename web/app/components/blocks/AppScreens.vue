<script setup lang="ts">
/** App screens: on desktop the phone is pinned on the right while the feature list scrolls on the left; the screen in the phone
 *  follows whichever feature is in the middle of the viewport (click also jumps). On phones: chassis on top, swipeable tabs below.
 *  Screens are keyed by their image slug (home / graph / events / alerts / reports / assistant) so Figma exports can replace the files 1:1. */
const props = defineProps<{ data: Record<string, any> }>()
const active = ref(0)
const screens = computed(() => (props.data.screens || []).map((s: any) => ({ ...s, key: (s.image?.src || '').split('/').pop()?.replace(/\.\w+$/, '') || s.title })))
const root = ref<HTMLElement | null>(null)
const feats = ref<HTMLElement[]>([])
let io: IntersectionObserver | null = null
const desktop = () => window.matchMedia('(min-width: 1024px)').matches

onMounted(() => {
  if (!desktop()) return
  io = new IntersectionObserver((es) => { es.forEach((e) => { if (e.isIntersecting) active.value = Number((e.target as HTMLElement).dataset.i) }) }, { rootMargin: '-40% 0px -40% 0px', threshold: 0 })
  feats.value.forEach((el) => io!.observe(el))
})
onBeforeUnmount(() => io?.disconnect())
function pick(i: number) {
  active.value = i
  if (desktop()) feats.value[i]?.scrollIntoView({ behavior: reducedMotion() ? 'auto' : 'smooth', block: 'center' })
}
const current = computed(() => screens.value[active.value] || {})
</script>
<template>
  <section ref="root" class="section app">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" />
      <div class="layout">
        <ol class="feats">
          <li v-for="(s, i) in screens" :key="s.key" ref="feats" :data-i="i" :class="{ on: active === i }">
            <button type="button" @click="pick(i)">
              <span class="n mono">{{ String(i + 1).padStart(2, '0') }}</span>
              <span class="t"><b>{{ s.title }}</b><small>{{ s.text }}</small></span>
              <i class="bar" aria-hidden="true" />
            </button>
          </li>
        </ol>
        <div class="stage">
          <div class="pin">
            <i class="halo" aria-hidden="true" />
            <UiPhoneFrame class="ph">
              <img v-for="(s, i) in screens" :key="s.key" :src="mediaUrl(s.image?.src)" :alt="s.image?.alt || s.title" :class="{ on: active === i }" width="780" height="1688" :loading="i === 0 ? 'eager' : 'lazy'" decoding="async">
            </UiPhoneFrame>
          </div>
        </div>
        <p class="mob-text" aria-live="polite">{{ current.text }}</p>
      </div>
    </div>
  </section>
</template>
<style scoped>
.app { background: linear-gradient(180deg, #fff 0%, #F3F0FF 45%, #EEF2FA 100%); overflow: clip; }
.app :deep(.eyebrow) { color: var(--purple-d); }
.layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.feats { list-style: none; margin: 0; padding: 0; display: grid; }
.feats { padding-block: 10vh 38vh; } /* bottom padding keeps the phone pinned until the last feature has been read */
.feats li { min-height: 60vh; display: grid; align-items: center; }
.feats button { position: relative; width: 100%; text-align: left; display: grid; grid-template-columns: 48px 1fr; gap: 16px; padding: 22px 26px; border-radius: var(--radius); transition: background .5s var(--ease), box-shadow .5s var(--ease), transform .5s var(--ease); }
.feats li.on button { background: #fff; box-shadow: 0 30px 60px -24px rgba(89,37,220,.28), 0 2px 0 rgba(255,255,255,.8) inset; }
.n { color: var(--purple-d); padding-top: 6px; font-size: .8rem; }
.t { display: grid; gap: 8px; } .t b { font-size: clamp(1.3rem, 1rem + .9vw, 2rem); letter-spacing: -.02em; line-height: 1.15; color: var(--ink); } .t small { font-size: clamp(1rem, 1rem + .15vw, 1.15rem); color: var(--ink-2); line-height: 1.5; }
.feats li:not(.on) .t b { color: var(--ink-2); } .feats li:not(.on) .t small { opacity: .7; }
.bar { position: absolute; left: 0; top: 26px; bottom: 26px; width: 3px; border-radius: 3px; background: var(--purple); transform: scaleY(0); transform-origin: top; transition: transform .6s var(--ease); }
.feats li.on .bar { transform: scaleY(1); }
.stage { position: relative; align-self: stretch; }
.pin { position: sticky; top: max(calc(var(--header-h) + 24px), calc(50vh - 380px)); display: grid; justify-items: center; padding-block: 20px; }
.halo { position: absolute; inset: 0; margin: auto; width: min(120%, 720px); aspect-ratio: 1; border-radius: 50%; background: radial-gradient(closest-side, rgba(122,90,248,.22) 0%, rgba(46,144,250,.12) 50%, transparent 72%); pointer-events: none; }
.ph { --pw: clamp(260px, 22vw, 340px); width: var(--pw); position: relative; z-index: 1; }
.ph :deep(img) { opacity: 0; transform: translateY(18px) scale(.985); transition: opacity .9s var(--ease), transform .9s var(--ease); }
.ph :deep(img.on) { opacity: 1; transform: none; }
.mob-text { display: none; }
@media (max-width: 1023px) {
  .layout { grid-template-columns: 1fr; gap: 24px; }
  .stage { order: -1; } .pin { position: static; padding-block: 0 8px; } .ph { --pw: clamp(220px, 60vw, 280px); }
  .halo { width: 120vw; }
  .feats { display: flex; gap: 8px; overflow-x: auto; scroll-snap-type: x proximity; margin-inline: calc(-1 * var(--gutter)); padding: 8px var(--gutter) 12px; scrollbar-width: none; }
  .feats::-webkit-scrollbar { display: none; }
  .feats li, .feats li:first-child, .feats li:last-child { min-height: 0; flex: 0 0 auto; scroll-snap-align: center; }
  .feats button { padding: 12px 16px; grid-template-columns: auto; gap: 0; border-radius: 999px; border: 1px solid var(--line); background: #fff; }
  .feats li.on button { background: var(--ink); color: #fff; box-shadow: none; border-color: var(--ink); }
  .n, .t small, .bar { display: none; } .t b { font-size: .95rem; color: inherit !important; }
  .mob-text { display: block; margin-top: 0; color: var(--ink-2); font-size: 1.05rem; line-height: 1.5; max-width: 52ch; text-align: center; min-height: 4.5em; margin-inline: auto; }
}
</style>
