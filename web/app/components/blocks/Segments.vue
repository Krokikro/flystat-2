<script setup lang="ts">
/** "Different days, same sensor": large photographic tiles, 2×2 on desktop, a horizontal snap strip on phones. Each tile links to its /for/* page. */
const props = defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
const { t } = useI18n()
const root = ref<HTMLElement | null>(null)
const items = computed(() => props.data.items || [])
useScrollFx(({ gsap }) => {
  if (!root.value || window.innerWidth < 1024) return
  return Array.from(root.value.querySelectorAll<HTMLElement>('.tile img')).map((img) => gsap.fromTo(img, { yPercent: -6 }, { yPercent: 6, ease: 'none', scrollTrigger: { trigger: img.closest('.tile'), start: 'top bottom', end: 'bottom top', scrub: .6 } }))
})
</script>
<template>
  <section ref="root" class="section seg band-warm">
    <div class="container">
      <div class="head">
        <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" tight />
      </div>
      <div class="tiles" :class="{ odd: items.length % 2 === 1 }">
        <NuxtLink v-for="(it, i) in items" :key="i" :to="localePath(it.href || '/')" class="tile" v-reveal="(i % 2) * 120">
          <img :src="mediaUrl(it.image?.src)" :alt="it.image?.alt || it.title" width="1200" height="1600" loading="lazy" decoding="async">
          <span class="shade" aria-hidden="true" />
          <span class="ov">
            <span class="ic"><UiIcon :name="it.icon" :size="20" /></span>
            <h3>{{ it.title }}</h3>
            <p>{{ it.text }}</p>
            <span class="more">{{ t('common.learn_more') }} <UiIcon name="arrow-right" :size="18" /></span>
          </span>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
<style scoped>
.seg { overflow: hidden; }
.head { display: flex; align-items: end; justify-content: space-between; gap: 24px; }
.tiles { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: clamp(16px, 1.8vw, 28px); }
.tile { position: relative; display: block; border-radius: var(--radius-lg); overflow: hidden; aspect-ratio: 5 / 4; color: #fff; isolation: isolate; background: var(--navy-800); transform: translateZ(0); }
.odd .tile:last-child { grid-column: 1 / -1; aspect-ratio: 21 / 9; }
.tile img { position: absolute; inset: -8% 0; width: 100%; height: 116%; object-fit: cover; object-position: 50% 22%; transition: transform 1.2s var(--ease), filter 1.2s var(--ease); will-change: transform; }
.tile:hover img { transform: scale(1.06); filter: saturate(1.08); }
.shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7,15,36,.05) 30%, rgba(7,15,36,.55) 70%, rgba(7,15,36,.92) 100%); transition: opacity .6s; }
.ov { position: absolute; inset: auto 0 0 0; padding: clamp(22px, 2.6vw, 40px); display: grid; gap: 8px; justify-items: start; }
.ic { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; background: rgba(255,255,255,.14); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.2); margin-bottom: 8px; }
.ov h3 { color: #fff; font-size: clamp(1.5rem, 1rem + 1.3vw, 2.5rem); letter-spacing: -.025em; text-shadow: 0 2px 20px rgba(0,0,0,.3); }
.ov p { color: rgba(255,255,255,.86); font-size: clamp(1rem, 1rem + .2vw, 1.15rem); max-width: 44ch; line-height: 1.5; }
.more { display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; font-weight: 700; font-size: .95rem; padding: 10px 16px; border-radius: 999px; background: #fff; color: var(--ink); transition: gap .3s var(--ease), transform .5s var(--ease); }
.tile:hover .more { gap: 14px; }
@media (min-width: 1024px) { .ov p, .more { opacity: 0; transform: translateY(12px); transition: opacity .6s var(--ease), transform .6s var(--ease); } .tile:hover .ov p, .tile:hover .more, .tile:focus-visible .ov p, .tile:focus-visible .more { opacity: 1; transform: none; } .tile:hover .more { transition-delay: .08s; } }
@media (max-width: 1023px) { .tile { aspect-ratio: 4 / 5; } .odd .tile:last-child { aspect-ratio: 16 / 10; } }
@media (max-width: 640px) {
  .tiles { display: flex; gap: 14px; overflow-x: auto; scroll-snap-type: x mandatory; margin-inline: calc(-1 * var(--gutter)); padding-inline: var(--gutter); padding-bottom: 8px; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
  .tiles::-webkit-scrollbar { display: none; }
  .tile, .odd .tile:last-child { flex: 0 0 78vw; max-width: 340px; aspect-ratio: 3 / 4; scroll-snap-align: start; }
  .ov { padding: 20px; } .ov p { font-size: .95rem; }
}
</style>
