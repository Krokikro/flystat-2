<script setup lang="ts">
import { story } from '~/utils/device-story.js'
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const progress = ref(0)
const anchors = ref<Record<string, { x: number; y: number; z: number }>>({})
const mode = ref<'live' | 'frames'>('live')
const { t } = useI18n()

// pages that open on the sensor (app page) start the story near its end
const start = computed(() => (props.data.scene === 'sensor' ? 0.86 : 0))
const p = computed(() => start.value + progress.value * (1 - start.value))
const variant = computed<'rotate' | 'device'>(() => (props.data.scene === 'device' ? 'device' : 'rotate'))
const st = computed(() => story(p.value, variant.value))

onMounted(() => {
  const onScroll = () => {
    if (!root.value) return
    const r = root.value.getBoundingClientRect(); const total = r.height - window.innerHeight
    progress.value = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 0
  }
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true }); window.addEventListener('resize', onScroll)
  // keep the fixed header dark while the dark hero is still on screen
  const stop = watch(progress, (v) => document.body.classList.toggle('in-dark-hero', v < 0.985), { immediate: true })
  onBeforeUnmount(() => { stop(); document.body.classList.remove('in-dark-hero'); window.removeEventListener('scroll', onScroll); window.removeEventListener('resize', onScroll) })
})
const fade = (o: number, dy = 24) => ({ opacity: String(o), transform: `translateY(${(1 - o) * dy}px)`, pointerEvents: o > 0.5 ? 'auto' : 'none' })
const headStyle = computed(() => fade(start.value > 0 ? 1 - Math.min(1, progress.value * 2.5) : st.value.text.headline, -40))
const stage2Style = computed(() => fade(st.value.text.stage2))
const stage4Style = computed(() => fade(st.value.text.stage4))
const labelsOn = computed(() => st.value.text.labels)
const labelKeys = ['housing', 'internals', 'patch', 'cap'] as const
const labelStyle = (k: string, i: number) => {
  const a = anchors.value[k]; if (!a) return { opacity: '0' }
  return { left: a.x + 'px', top: a.y + 'px', opacity: String(labelsOn.value), transitionDelay: `${i * 60}ms` }
}
</script>
<template>
  <section ref="root" class="hero" :class="{ short: start > 0 }">
    <div class="sticky">
      <ClientOnly><DeviceScene :progress="p" :variant="variant" @anchors="anchors = $event" @mode="mode = $event" /></ClientOnly>
      <div class="container inner">
        <div class="copy" :style="headStyle">
          <p v-if="data.eyebrow" class="tag mono">{{ data.eyebrow }}</p>
          <h1>{{ data.title }}</h1>
          <p v-if="data.text" class="lead">{{ data.text }}</p>
          <div class="ctas"><UiCtaLink :cta="data.cta" /><UiCtaLink :cta="data.cta2" variant="btn-ghost" /></div>
        </div>

        <template v-if="data.stage2_title">
          <div class="stage s2" :style="stage2Style">
            <p class="tag mono">01</p>
            <h2>{{ data.stage2_title }}</h2>
            <p class="lead">{{ data.stage2_text }}</p>
          </div>
          <div class="stage s4" :style="stage4Style">
            <p class="tag mono">03</p>
            <h2>{{ data.stage4_title }}</h2>
            <p class="lead">{{ data.stage4_text }}</p>
          </div>
        </template>
        <div class="scroll-hint mono" :style="{ opacity: String(1 - progress * 4) }"><span>{{ t('common.scroll') }}</span><i /></div>
      </div>
      <!-- callouts follow the exploded parts (live mode only; frames mode shows them as a list) -->
      <div v-if="data.labels && mode === 'live'" class="callouts" aria-hidden="true">
        <div v-for="(k, i) in labelKeys" :key="k" class="callout" :style="labelStyle(k, i)"><i /><span>{{ data.labels[k] }}</span></div>
      </div>
      <ul v-else-if="data.labels" class="callout-list" :style="{ opacity: String(labelsOn) }">
        <li v-for="k in labelKeys" :key="k">{{ data.labels[k] }}</li>
      </ul>
    </div>
  </section>
</template>
<style scoped>
.hero { position: relative; height: 720svh; background: var(--navy-800); color: var(--fg-on-dark); }
.hero.short { height: 160svh; }
.sticky { position: sticky; top: 0; height: 100svh; overflow: hidden; }
.inner { position: relative; z-index: 2; height: 100%; display: grid; align-content: center; }
.copy { display: grid; gap: 22px; max-width: 640px; padding-top: var(--header-h); will-change: transform, opacity; transition: opacity .2s linear; }
.tag { color: #8FB7FF; }
.lead { color: var(--muted-on-dark); }
.ctas { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
.stage { position: absolute; left: var(--gutter); top: 50%; transform: translateY(-50%); max-width: 460px; display: grid; gap: 14px; will-change: transform, opacity; }
.stage h2 { font-size: clamp(1.875rem, 3.2vw, 2.875rem); line-height: 1.08; letter-spacing: -0.02em; }
.scroll-hint { position: absolute; left: var(--gutter); bottom: 28px; display: flex; align-items: center; gap: 10px; color: var(--muted-on-dark); font-size: 0.6875rem; }
.scroll-hint i { width: 28px; height: 1px; background: currentColor; display: block; animation: hint 1.8s infinite; }
@keyframes hint { 0% { transform: scaleX(.2); transform-origin: left; } 50% { transform: scaleX(1); } 100% { transform: scaleX(.2); transform-origin: right; } }
.callouts { position: absolute; inset: 0; z-index: 3; pointer-events: none; }
.callout { position: absolute; display: flex; align-items: center; gap: 12px; transform: translate(0, -50%); white-space: nowrap; transition: opacity .35s ease; }
.callout i { width: 72px; height: 1px; background: linear-gradient(90deg, #8FB7FF, rgba(143,183,255,0)); position: relative; flex: none; }
.callout i::before { content: ""; position: absolute; left: -4px; top: -4px; width: 9px; height: 9px; border-radius: 50%; background: #8FB7FF; box-shadow: 0 0 0 5px rgba(143,183,255,.18); }
.callout span { font-size: 0.9062rem; color: #fff; background: rgba(6,38,77,.55); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.12); padding: 8px 12px; border-radius: 10px; }
.callout-list { position: absolute; left: var(--gutter); right: var(--gutter); bottom: 12vh; z-index: 3; display: grid; gap: 8px; list-style: none; margin: 0; padding: 0; font-size: 0.875rem; color: #fff; transition: opacity .3s; pointer-events: none; }
.callout-list li { padding-left: 16px; position: relative; } .callout-list li::before { content: ""; position: absolute; left: 0; top: .55em; width: 7px; height: 7px; border-radius: 50%; background: #8FB7FF; }
@media (max-width: 760px) {
  .hero { height: 480svh; } .inner { align-content: end; padding-bottom: 72px; } .copy { max-width: none; } h1 { font-size: clamp(2.125rem, 9.5vw, 2.875rem); }
  .stage { top: auto; bottom: 72px; transform: none; max-width: none; right: var(--gutter); } .stage h2 { font-size: clamp(1.625rem, 7.5vw, 2.125rem); }
}
@media (prefers-reduced-motion: reduce) { .hero { height: 100svh; } .copy { opacity: 1 !important; transform: none !important; } .stage, .callout-list { display: none; } }
</style>
