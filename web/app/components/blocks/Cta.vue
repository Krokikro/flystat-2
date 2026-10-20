<script setup lang="ts">
/** Full-bleed call-to-action band. Tones: dark (navy, applicator render), purple (app gradient, phone with the home screen), light.
 *  `data.image` overrides the default artwork; two buttons (cta / cta2). */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const art = ref<HTMLElement | null>(null)
const tone = computed(() => props.data.tone || 'dark')
const src = computed(() => props.data.image?.src || (tone.value === 'purple' ? '/media/app/home.webp' : tone.value === 'dark' ? '/media/renders/applicator-angle.webp' : ''))
const kind = computed(() => src.value.includes('/app/') ? 'phone' : src.value ? 'render' : 'none')
useParallax(art, 50, root)
</script>
<template>
  <section ref="root" class="cta-band" :class="[`tone-${tone}`, `k-${kind}`]">
    <i class="light" aria-hidden="true" />
    <div class="container inner">
      <div class="copy">
        <h2 v-reveal>{{ data.title }}</h2>
        <p v-if="data.text" class="lead" v-reveal="80">{{ data.text }}</p>
        <div class="acts" v-reveal="160">
          <UiCtaLink :cta="data.cta" :variant="tone === 'light' ? 'btn-navy' : tone === 'purple' ? 'btn-white' : 'btn-primary'" />
          <UiCtaLink :cta="data.cta2" :variant="tone === 'light' ? 'btn-ghost-dark' : 'btn-ghost'" />
        </div>
      </div>
      <div v-if="kind !== 'none'" ref="art" class="art" aria-hidden="true">
        <UiPhoneFrame v-if="kind === 'phone'" class="ph"><img :src="mediaUrl(src)" alt="" width="780" height="1688" loading="lazy" decoding="async"></UiPhoneFrame>
        <img v-else :src="mediaUrl(src)" alt="" width="1325" height="1600" loading="lazy" decoding="async">
      </div>
    </div>
  </section>
</template>
<style scoped>
.cta-band { position: relative; overflow: hidden; padding-block: clamp(64px, 8vw, 128px); isolation: isolate; }
.light { position: absolute; pointer-events: none; z-index: 0; width: 60vw; height: 60vw; max-width: 900px; max-height: 900px; right: -10%; top: 50%; transform: translateY(-50%); border-radius: 50%; }
.tone-dark { background: linear-gradient(110deg, var(--navy-900) 0%, var(--navy-800) 55%, #123A7A 100%); color: #fff; } .tone-dark .lead { color: var(--muted-on-dark); }
.tone-dark .light { background: radial-gradient(closest-side, rgba(46,144,250,.45), rgba(46,144,250,0) 70%); }
.tone-purple { background: linear-gradient(115deg, #3E12B0 0%, #5925DC 40%, #7A5AF8 75%, #9C84FF 100%); color: #fff; } .tone-purple .lead { color: rgba(255,255,255,.84); }
.tone-purple .light { background: radial-gradient(closest-side, rgba(255,255,255,.28), rgba(255,255,255,0) 70%); }
.tone-light { background: var(--bg-alt); }
.inner { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr); gap: clamp(24px, 4vw, 64px); align-items: center; }
.k-none .inner { grid-template-columns: 1fr; }
.copy { display: grid; gap: clamp(16px, 1.6vw, 24px); }
.copy h2 { font-size: clamp(2.1rem, 1rem + 2.8vw, 4rem); letter-spacing: -.03em; max-width: 18ch; }
.lead { max-width: 56ch; }
.acts { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
.acts :deep(.btn) { padding: 1.1rem 1.9rem; font-size: 1.05rem; }
.art { position: relative; display: grid; grid-template-rows: 100%; grid-template-columns: 100%; place-items: center; will-change: transform; }
.k-render .art { height: clamp(300px, 28vw, 440px); }
.k-render .art img { height: 128%; width: auto; max-width: none; margin-top: 6%; filter: drop-shadow(0 50px 60px rgba(0,0,0,.55)); transform: rotate(-10deg); }
.k-phone .art { height: clamp(300px, 28vw, 440px); align-items: end; }
.ph { --pw: clamp(220px, 20vw, 300px); width: var(--pw); margin-bottom: -60%; transform: rotate(-6deg); }
@media (max-width: 860px) {
  .inner { grid-template-columns: 1fr; } .acts { justify-content: start; }
  .k-render .art { height: 240px; order: -1; margin-top: -24px; } .k-render .art img { height: 120%; margin-top: 0; transform: none; }
  .k-phone .art { height: 220px; order: -1; overflow: hidden; } .ph { --pw: 200px; margin-bottom: -70%; }
}
</style>
