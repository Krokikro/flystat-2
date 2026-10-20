<script setup lang="ts">
/** Editorial news card: cover with a date chip, category, title, excerpt and a "read" arrow. `featured` makes a tall hero variant for the first slot. */
import type { NewsCard } from '~/utils/types'
defineProps<{ item: NewsCard; featured?: boolean }>()
const localePath = useLocalePath(); const { locale, t } = useI18n()
const fmt = (d: string | null) => d ? new Intl.DateTimeFormat(locale.value, { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d)) : ''
</script>
<template>
  <NuxtLink :to="localePath('/news/' + item.slug)" class="ncard" :class="{ featured }" v-reveal>
    <div class="img">
      <img v-if="item.cover" :src="mediaUrl(item.cover)" :alt="item.title" width="800" height="500" loading="lazy" decoding="async">
      <div v-else class="ph" />
      <span v-if="item.published_at" class="date">{{ fmt(item.published_at) }}</span>
    </div>
    <div class="body">
      <p class="meta mono">{{ t('news.' + item.category) }}</p>
      <h3>{{ item.title }}</h3>
      <p class="ex">{{ item.excerpt }}</p>
      <span class="read">{{ t('news.read') }} <UiIcon name="arrow-right" :size="18" /></span>
    </div>
  </NuxtLink>
</template>
<style scoped>
.ncard { display: grid; grid-template-rows: auto 1fr; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--line); background: #fff; transition: transform .6s var(--ease), box-shadow .6s var(--ease), border-color .4s; min-width: 0; }
.ncard:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -20px rgba(16,42,86,.22); border-color: rgba(46,144,250,.3); }
.img { position: relative; aspect-ratio: 16 / 10; background: var(--bg-alt); overflow: hidden; }
.img img { width: 100%; height: 100%; object-fit: cover; transition: transform 1s var(--ease); } .ncard:hover img { transform: scale(1.05); }
.ph { position: absolute; inset: 0; background: linear-gradient(135deg, #E9F0FF, #F3F0FF); }
.date { position: absolute; left: 16px; top: 16px; padding: 8px 12px; border-radius: 999px; font-size: .8rem; font-weight: 700; background: rgba(255,255,255,.88); color: var(--ink); backdrop-filter: blur(8px); box-shadow: 0 6px 20px rgba(16,42,86,.12); }
.body { padding: clamp(20px, 2.2vw, 32px); display: grid; gap: 10px; align-content: start; }
.meta { color: var(--blue-d); font-size: .7rem; }
.ncard h3 { font-size: clamp(1.2rem, 1rem + .5vw, 1.55rem); line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.ex { color: var(--ink-2); font-size: 1rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.read { display: inline-flex; align-items: center; gap: 8px; margin-top: 8px; font-weight: 700; font-size: .95rem; color: var(--ink); transition: gap .3s var(--ease); } .ncard:hover .read { gap: 14px; color: var(--blue-d); }
.featured h3 { font-size: clamp(1.5rem, 1rem + 1.1vw, 2.3rem); -webkit-line-clamp: 4; }
.featured .ex { font-size: 1.08rem; -webkit-line-clamp: 4; }
@media (min-width: 901px) {
  .featured { grid-template-rows: none; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); }
  .featured .img { aspect-ratio: auto; min-height: 380px; height: 100%; }
  .featured .body { align-content: center; padding: clamp(28px, 3vw, 48px); }
}
</style>
