<script setup lang="ts">
/** Light hero for inner pages: big headline left, artwork right (photo in a tall rounded frame, render on a plate, app screen in a phone), optional stats row. */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const art = ref<HTMLElement | null>(null)
const src = computed(() => props.data.image?.src || '')
const kind = computed(() => !src.value ? 'none' : src.value.includes('/app/') ? 'phone' : src.value.includes('/renders/') ? 'render' : 'photo')
useParallax(art, 30, root)
</script>
<template>
  <section ref="root" class="hero-l" :class="`k-${kind}`">
    <i class="blob b1" aria-hidden="true" /><i class="blob b2" aria-hidden="true" />
    <div class="container inner">
      <div class="copy">
        <p v-if="data.eyebrow" class="eyebrow" v-reveal>{{ data.eyebrow }}</p>
        <h1 v-reveal="60">{{ data.title }}</h1>
        <p v-if="data.text" class="lead" v-reveal="140">{{ data.text }}</p>
        <div v-if="data.cta?.label || data.cta2?.label" class="ctas" v-reveal="200"><UiCtaLink :cta="data.cta" variant="btn-navy" /><UiCtaLink :cta="data.cta2" variant="btn-ghost-dark" /></div>
        <div v-if="data.stats?.length" class="stats" v-reveal="260">
          <div v-for="(s, i) in data.stats" :key="i"><b>{{ s.value }}</b><span>{{ s.label }}</span></div>
        </div>
      </div>
      <div v-if="kind !== 'none'" class="art" v-reveal="160">
        <div ref="art" class="float">
          <UiPhoneFrame v-if="kind === 'phone'" class="ph"><img :src="mediaUrl(src)" :alt="data.image?.alt || ''" width="780" height="1688" loading="eager" fetchpriority="high"></UiPhoneFrame>
          <div v-else-if="kind === 'render'" class="plate"><img :src="mediaUrl(src)" :alt="data.image?.alt || ''" width="1325" height="1600" loading="eager" fetchpriority="high"></div>
          <div v-else class="photo"><img :src="mediaUrl(src)" :alt="data.image?.alt || ''" width="1200" height="1600" loading="eager" fetchpriority="high"></div>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.hero-l { position: relative; overflow: hidden; isolation: isolate; padding-top: calc(var(--header-h) + clamp(40px, 6vw, 96px)); padding-bottom: clamp(48px, 6vw, 104px); background: linear-gradient(180deg, #F3F6FF 0%, #fff 100%); }
.blob { position: absolute; z-index: 0; border-radius: 50%; pointer-events: none; filter: blur(10px); }
.b1 { width: 48vw; height: 48vw; max-width: 760px; max-height: 760px; right: -12%; top: -20%; background: radial-gradient(closest-side, rgba(46,144,250,.18), transparent 70%); }
.b2 { width: 36vw; height: 36vw; max-width: 560px; max-height: 560px; left: -10%; bottom: -30%; background: radial-gradient(closest-side, rgba(122,90,248,.14), transparent 70%); }
.inner { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr); gap: clamp(32px, 5vw, 96px); align-items: center; }
.k-none .inner { grid-template-columns: minmax(0, 1fr); } .k-none .copy { max-width: 900px; }
.copy { display: grid; gap: clamp(18px, 1.8vw, 28px); justify-items: start; }
.copy h1 { font-size: clamp(2.4rem, 1rem + 3.8vw, 4.8rem); }
.copy .lead { font-size: clamp(1.1rem, 1rem + .45vw, 1.45rem); }
.ctas { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px; }
.ctas :deep(.btn) { padding: 1.1rem 1.9rem; font-size: 1.05rem; }
.stats { display: flex; gap: clamp(28px, 3vw, 56px); flex-wrap: wrap; margin-top: 12px; padding-top: 24px; border-top: 1px solid var(--line); width: 100%; }
.stats div { display: grid; gap: 4px; } .stats b { font-size: clamp(2rem, 1rem + 1.6vw, 3rem); font-weight: 900; letter-spacing: -.03em; line-height: 1; font-variant-numeric: tabular-nums; } .stats span { font-size: .9rem; color: var(--muted); }
.art { position: relative; display: grid; justify-items: center; }
.float { width: 100%; will-change: transform; display: grid; justify-items: center; }
.plate { width: min(100%, 560px); aspect-ratio: 1; border-radius: var(--radius-lg); display: grid; grid-template-rows: 100%; grid-template-columns: 100%; place-items: center; background: radial-gradient(90% 90% at 30% 20%, #fff 0%, #E9F0FF 55%, #DCE8FF 100%); box-shadow: inset 0 0 0 1px rgba(16,42,86,.05), var(--shadow); }
.plate img { width: 78%; height: 78%; object-fit: contain; filter: drop-shadow(0 40px 50px rgba(16,42,86,.28)); }
.photo { position: relative; width: min(100%, 520px); aspect-ratio: 4 / 5; border-radius: 48% 48% var(--radius-lg) var(--radius-lg) / 36% 36% var(--radius-lg) var(--radius-lg); overflow: hidden; box-shadow: var(--shadow); }
.photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 20%; }
.photo::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 60%, rgba(6,38,77,.25) 100%); }
.ph { --pw: clamp(240px, 20vw, 320px); width: var(--pw); }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; gap: 28px; } .plate { width: min(80%, 380px); } .photo { width: min(80%, 360px); aspect-ratio: 1 / 1; border-radius: var(--radius-lg); } .ph { --pw: 220px; } .copy h1 { font-size: clamp(2.2rem, 1rem + 6vw, 3.2rem); } }
</style>
