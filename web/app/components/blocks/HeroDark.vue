<script setup lang="ts">
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const progress = ref(0)
onMounted(() => {
  const onScroll = () => {
    if (!root.value) return
    const r = root.value.getBoundingClientRect(); const total = r.height - window.innerHeight
    progress.value = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 0
  }
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true }); window.addEventListener('resize', onScroll)
  onBeforeUnmount(() => { window.removeEventListener('scroll', onScroll); window.removeEventListener('resize', onScroll) })
})
const textStyle = computed(() => ({ opacity: String(1 - Math.min(1, progress.value * 2.2)), transform: `translateY(${-progress.value * 60}px)` }))
const captionStyle = computed(() => { const p = Math.max(0, (progress.value - 0.45) / 0.4); return { opacity: String(Math.min(1, p * 2)), transform: `translateY(${(1 - Math.min(1, p)) * 24}px)` } })
const { t } = useI18n()
</script>
<template>
  <section ref="root" class="hero">
    <div class="sticky">
      <ClientOnly><DeviceScroller :progress="progress" :scene="data.scene || 'rotate'" /></ClientOnly>
      <div class="container inner">
        <div class="copy" :style="textStyle">
          <p v-if="data.eyebrow" class="tag mono">{{ data.eyebrow }}</p>
          <h1>{{ data.title }}</h1>
          <p v-if="data.text" class="lead">{{ data.text }}</p>
          <div class="ctas"><UiCtaLink :cta="data.cta" /><UiCtaLink :cta="data.cta2" variant="btn-ghost" /></div>
        </div>
        <div class="caption" :style="captionStyle" aria-hidden="true">
          <p class="mono">Flystat · 15 d · 1 min</p>
        </div>
        <div class="scroll-hint mono" :style="{ opacity: String(1 - progress * 4) }"><span>{{ t('common.scroll') }}</span><i /></div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.hero { position: relative; height: 240svh; background: var(--navy-800); color: var(--fg-on-dark); }
.sticky { position: sticky; top: 0; height: 100svh; overflow: hidden; }
.inner { position: relative; z-index: 2; height: 100%; display: grid; align-content: center; }
.copy { display: grid; gap: 20px; max-width: 620px; padding-top: var(--header-h); will-change: transform, opacity; }
.tag { color: #8FB7FF; }
.lead { color: var(--muted-on-dark); }
.ctas { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 6px; }
.caption { position: absolute; right: var(--gutter); bottom: 14vh; color: #8FB7FF; }
.scroll-hint { position: absolute; left: var(--gutter); bottom: 28px; display: flex; align-items: center; gap: 10px; color: var(--muted-on-dark); font-size: 10px; }
.scroll-hint i { width: 28px; height: 1px; background: currentColor; display: block; animation: hint 1.8s infinite; }
@keyframes hint { 0% { transform: scaleX(.2); transform-origin: left; } 50% { transform: scaleX(1); } 100% { transform: scaleX(.2); transform-origin: right; } }
@media (max-width: 760px) { .hero { height: 200svh; } .inner { align-content: end; padding-bottom: 72px; } .copy { max-width: none; } h1 { font-size: clamp(32px, 9vw, 44px); } .caption { display: none; } }
@media (prefers-reduced-motion: reduce) { .hero { height: 100svh; } .copy { opacity: 1 !important; transform: none !important; } }
</style>
