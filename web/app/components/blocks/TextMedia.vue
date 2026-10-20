<script setup lang="ts">
/** Copy + media, 50/50, alternating by `media_side`. Tones: light | alt | warm | dark.
 *  Media sits on a soft gradient plate and drifts with the scroll; renders are contained, photos cover, app screens get a phone chassis.
 *  Optional `chips` (array of short strings) float over the plate as spec callouts. */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const float = ref<HTMLElement | null>(null)
const src = computed(() => props.data.image?.src || '')
const kind = computed(() => src.value.includes('/app/') ? 'phone' : src.value.includes('/renders/') ? 'render' : 'photo')
const tone = computed(() => props.data.tone || 'light')
const tall = ref(false) // tall renders (e.g. the exploded applicator) get a portrait plate instead of a square
useParallax(float, 36, root)
useScrollFx(({ gsap }) => {
  const plate = root.value?.querySelector('.plate'); if (!plate) return
  return gsap.fromTo(plate, { clipPath: 'inset(8% round 32px)', scale: .96 }, { clipPath: 'inset(0% round 32px)', scale: 1, ease: 'none', scrollTrigger: { trigger: plate, start: 'top 90%', end: 'top 45%', scrub: .5 } })
})
</script>
<template>
  <section ref="root" class="section tm" :class="[`tone-${tone}`, `k-${kind}`, { left: data.media_side === 'left' }]">
    <div class="container inner">
      <div class="copy">
        <p v-if="data.eyebrow" class="eyebrow" v-reveal>{{ data.eyebrow }}</p>
        <h2 v-reveal="60">{{ data.title }}</h2>
        <div class="prose" v-reveal="120" v-html="data.text" />
        <div v-if="data.cta?.label" v-reveal="180"><UiCtaLink :cta="data.cta" :variant="tone === 'dark' ? 'btn-primary' : 'btn-navy'" /></div>
      </div>
      <div class="media" v-reveal="100">
        <div class="plate" :class="{ tall }">
          <video v-if="data.video" :src="mediaUrl(data.video)" :poster="mediaUrl(src)" autoplay muted loop playsinline />
          <div v-else ref="float" class="float">
            <UiPhoneFrame v-if="kind === 'phone'" class="ph"><img :src="mediaUrl(src)" :alt="data.image?.alt || ''" width="780" height="1688" loading="lazy" decoding="async"></UiPhoneFrame>
            <img v-else :src="mediaUrl(src)" :alt="data.image?.alt || ''" :width="kind === 'render' ? 1200 : 1200" :height="kind === 'render' ? 1200 : 1500" loading="lazy" decoding="async" @load="(e: any) => tall = e.target.naturalHeight / e.target.naturalWidth > 1.6">
          </div>
          <ul v-if="data.chips?.length" class="chips" aria-hidden="true"><li v-for="(c, i) in data.chips" :key="i" :style="{ '--d': `${i * 120}ms` }" v-reveal="300 + i * 120">{{ c }}</li></ul>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.tm { overflow: hidden; }
.inner { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(32px, 6vw, 110px); align-items: center; }
.left .copy { order: 2; } .left .media { order: 1; }
.copy { display: grid; gap: clamp(16px, 1.6vw, 24px); justify-items: start; }
.copy h2 { font-size: clamp(2rem, 1rem + 2.6vw, 3.8rem); letter-spacing: -.03em; }
.copy .prose { font-size: clamp(1.05rem, 1rem + .3vw, 1.3rem); line-height: 1.6; max-width: 48ch; }
.media { position: relative; }
.plate { position: relative; width: 100%; aspect-ratio: 1 / 1; border-radius: var(--radius-lg); overflow: hidden; display: grid; place-items: center; background: radial-gradient(90% 90% at 30% 20%, #FFFFFF 0%, #E9F0FF 55%, #DCE8FF 100%); box-shadow: inset 0 0 0 1px rgba(16,42,86,.05), var(--shadow); will-change: transform, clip-path; }
.plate video { width: 100%; height: 100%; object-fit: cover; }
.plate, .float { grid-template-rows: 100%; grid-template-columns: 100%; }
.float { width: 100%; height: 100%; display: grid; place-items: center; will-change: transform; }
.k-render .float img { width: 78%; height: 78%; object-fit: contain; filter: drop-shadow(0 50px 50px rgba(16,42,86,.28)); }
.k-render .plate.tall { aspect-ratio: 4 / 5; } .k-render .plate.tall img { width: 86%; height: 90%; }
.k-photo .plate { aspect-ratio: 4 / 5; } .k-photo .float img { width: 100%; height: 100%; object-fit: cover; }
.k-phone .plate { aspect-ratio: 1 / 1; } .ph { --pw: clamp(220px, 20vw, 300px); width: var(--pw); margin-top: 12%; margin-bottom: -30%; }
.chips { list-style: none; margin: 0; padding: 0; position: absolute; left: clamp(16px, 2vw, 28px); bottom: clamp(16px, 2vw, 28px); display: flex; flex-wrap: wrap; gap: 8px; max-width: 70%; }
.chips li { padding: 10px 14px; border-radius: 999px; font-weight: 700; font-size: .85rem; letter-spacing: -.01em; background: rgba(255,255,255,.82); color: var(--ink); backdrop-filter: blur(10px); box-shadow: 0 8px 24px rgba(16,42,86,.12); border: 1px solid rgba(255,255,255,.7); }
.tone-alt { background: var(--bg-alt); } .tone-alt .plate { background: radial-gradient(90% 90% at 30% 20%, #FFFFFF 0%, #F4F7FF 60%, #E7EEFF 100%); }
.tone-warm { background: var(--warm-2); } .tone-warm .plate { background: radial-gradient(90% 90% at 30% 20%, #FFFFFF 0%, #FBF4EC 50%, #F0DEC8 100%); }
.tone-dark { background: linear-gradient(180deg, var(--navy-800), var(--navy-900)); color: var(--fg-on-dark); }
.tone-dark .prose { color: var(--muted-on-dark); } .tone-dark .prose :deep(strong) { color: #fff; } .tone-dark .eyebrow { color: #8FB7FF; }
.tone-dark .plate { background: radial-gradient(80% 80% at 50% 40%, rgba(46,144,250,.35) 0%, rgba(14,38,89,.6) 55%, rgba(7,15,36,.9) 100%); box-shadow: inset 0 0 0 1px rgba(255,255,255,.08), 0 40px 80px rgba(0,0,0,.4); }
.tone-dark.k-render .float img { filter: drop-shadow(0 50px 60px rgba(0,0,0,.6)); }
.tone-dark .chips li { background: rgba(6,38,77,.7); color: #fff; border-color: rgba(255,255,255,.14); }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; gap: 32px; } .left .copy { order: 1; } .left .media { order: 2; } .k-render .plate { aspect-ratio: 5 / 4; } .k-render .float img { width: 72%; height: 86%; } .ph { --pw: 200px; } }
</style>
