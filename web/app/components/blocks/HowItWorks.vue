<script setup lang="ts">
/** Three steps: the headline stays pinned on the left while large numbered cards stack up on the right.
 *  Each card shows media chosen by the image path: app screens get a phone chassis, renders a gradient plate, photos a cover crop. */
const props = defineProps<{ data: Record<string, any> }>()
const root = ref<HTMLElement | null>(null)
const defaults = ['/media/renders/sensor.webp', '/media/app/home.webp', '/media/app/reports.webp']
const items = computed(() => (props.data.items || []).map((it: any, i: number) => ({ ...it, src: it.image?.src || defaults[i] || '', alt: it.image?.alt || it.title })))
const active = ref(0)
onMounted(() => {
  const cards = Array.from(root.value?.querySelectorAll<HTMLElement>('.card') || [])
  const io = new IntersectionObserver((es) => es.forEach((e) => { if (e.isIntersecting) active.value = cards.indexOf(e.target as HTMLElement) }), { rootMargin: '-40% 0px -40% 0px' })
  cards.forEach((c) => io.observe(c)); onBeforeUnmount(() => io.disconnect())
})
const kind = (src: string) => src.includes('/app/') ? 'phone' : src.includes('/renders/') ? 'render' : 'photo'

useScrollFx(({ gsap }) => {
  if (!root.value || window.innerWidth < 1024) return
  const cards = Array.from(root.value.querySelectorAll<HTMLElement>('.card'))
  return cards.slice(0, -1).map((c, i) => gsap.to(c, { scale: .94 - (cards.length - 2 - i) * .02, ease: 'none', scrollTrigger: { trigger: cards[i + 1], start: 'top 80%', end: 'top 25%', scrub: .4 } }))
})
</script>
<template>
  <section ref="root" class="section hiw">
    <div class="container inner">
      <div class="side">
        <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" tight />
        <ol class="dots" aria-hidden="true"><li v-for="(it, i) in items" :key="i" :class="{ on: active === i }">{{ String(i + 1).padStart(2, '0') }}</li></ol>
      </div>
      <ol class="cards">
        <li v-for="(it, i) in items" :key="i" class="card" :class="`k-${kind(it.src)}`" :style="{ '--i': i }" v-reveal="i * 60">
          <div class="txt">
            <div class="num"><span class="mono">{{ String(i + 1).padStart(2, '0') }}</span><i><UiIcon :name="it.icon" :size="22" /></i></div>
            <h3>{{ it.title }}</h3>
            <p>{{ it.text }}</p>
          </div>
          <div class="media">
            <UiPhoneFrame v-if="kind(it.src) === 'phone'" class="ph"><img :src="mediaUrl(it.src)" :alt="it.alt" width="780" height="1688" loading="lazy" decoding="async"></UiPhoneFrame>
            <div v-else-if="kind(it.src) === 'render'" class="plate"><img :src="mediaUrl(it.src)" :alt="it.alt" width="1215" height="614" loading="lazy" decoding="async"></div>
            <div v-else class="photo"><img :src="mediaUrl(it.src)" :alt="it.alt" width="1200" height="1600" loading="lazy" decoding="async"></div>
          </div>
        </li>
      </ol>
    </div>
  </section>
</template>
<style scoped>
.hiw { position: relative; z-index: 2; background: #fff; margin-top: -56px; border-radius: 56px 56px 0 0; padding-top: clamp(56px, 7vw, 120px); }
.inner { display: grid; grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.side { position: sticky; top: calc(var(--header-h) + 40px); display: grid; gap: 28px; }
.side :deep(.section-head) { margin-bottom: 0; }
.dots { list-style: none; margin: 0; padding: 0; display: flex; gap: 10px; font-family: var(--mono); font-size: .75rem; letter-spacing: .14em; color: var(--muted); }
.dots li { padding: 6px 10px; border: 1px solid var(--line); border-radius: 999px; transition: background .4s, color .4s, border-color .4s; } .dots li.on { background: var(--ink); color: #fff; border-color: var(--ink); }
.cards { list-style: none; margin: 0; padding: 0; display: grid; gap: clamp(20px, 2.4vw, 36px); }
.card { position: sticky; top: calc(var(--header-h) + 24px + var(--i) * 18px); display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(20px, 3vw, 48px); align-items: center; min-height: clamp(420px, 44vw, 600px); padding: clamp(28px, 3.4vw, 56px); border-radius: var(--radius-lg); background: var(--bg-alt); transform-origin: 50% 0; will-change: transform; overflow: hidden; box-shadow: 0 -10px 40px rgba(16,42,86,.06); }
.card:nth-child(2) { background: linear-gradient(160deg, #F0F4FE 0%, #F3F0FF 100%); }
.card:nth-child(3) { background: var(--warm-2); }
.txt { display: grid; gap: 16px; align-content: start; }
.num { display: flex; align-items: center; gap: 12px; color: var(--blue-d); margin-bottom: 6px; }
.num span { font-size: .8rem; letter-spacing: .16em; }
.num i { width: 44px; height: 44px; border-radius: 12px; background: #fff; display: grid; place-items: center; box-shadow: 0 6px 16px rgba(16,42,86,.08); }
.card h3 { font-size: clamp(1.5rem, 1rem + 1.1vw, 2.2rem); letter-spacing: -.02em; line-height: 1.1; }
.card p { color: var(--ink-2); font-size: clamp(1rem, 1rem + .15vw, 1.15rem); line-height: 1.55; max-width: 40ch; }
.media { position: relative; display: grid; place-items: center; min-height: 100%; }
.ph { --pw: clamp(190px, 16vw, 250px); width: var(--pw); margin-bottom: -34%; align-self: end; }
.plate { width: 100%; aspect-ratio: 1 / 1; border-radius: 50%; background: radial-gradient(closest-side, #fff 0%, rgba(255,255,255,.55) 60%, rgba(255,255,255,0) 100%); display: grid; place-items: center; }
.plate img { width: 92%; height: auto; filter: drop-shadow(0 30px 30px rgba(16,42,86,.22)); }
.photo { width: 100%; aspect-ratio: 4 / 5; border-radius: var(--radius); overflow: hidden; } .photo img { width: 100%; height: 100%; object-fit: cover; }
@media (max-width: 1023px) { .inner { grid-template-columns: 1fr; } .side { position: static; } .card { position: relative; top: auto; min-height: 0; } .ph { margin-bottom: -22%; } }
@media (max-width: 640px) { .card { grid-template-columns: 1fr; padding: 24px; gap: 20px; } .media { order: -1; } .ph { --pw: 170px; margin: 0 0 -50% 0; } .plate { width: 80%; } .photo { aspect-ratio: 16/10; } }
</style>
