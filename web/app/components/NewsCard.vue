<script setup lang="ts">
import type { NewsCard } from '~/utils/types'
defineProps<{ item: NewsCard }>()
const localePath = useLocalePath(); const { locale, t } = useI18n()
const fmt = (d: string | null) => d ? new Intl.DateTimeFormat(locale.value, { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d)) : ''
</script>
<template>
  <NuxtLink :to="localePath('/news/' + item.slug)" class="ncard" v-reveal>
    <div class="img"><img v-if="item.cover" :src="mediaUrl(item.cover)" :alt="item.title" width="800" height="500" loading="lazy"><div v-else class="ph" /></div>
    <div class="body">
      <p class="meta mono">{{ t('news.' + item.category) }} · {{ fmt(item.published_at) }}</p>
      <h3>{{ item.title }}</h3>
      <p class="ex">{{ item.excerpt }}</p>
    </div>
  </NuxtLink>
</template>
<style scoped>
.ncard { display: grid; border-radius: var(--radius); overflow: hidden; border: 1px solid var(--line); background: #fff; transition: transform .5s var(--ease), box-shadow .5s var(--ease); }
.ncard:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
.img { aspect-ratio: 16/10; background: var(--bg-alt); overflow: hidden; } .img img { width: 100%; height: 100%; object-fit: cover; transition: transform .8s var(--ease); } .ncard:hover img { transform: scale(1.04); }
.body { padding: 18px 20px 22px; display: grid; gap: 8px; }
.meta { color: var(--muted); font-size: 0.6875rem; }
.ex { color: var(--ink-2); font-size: 0.9062rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>
