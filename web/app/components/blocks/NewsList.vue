<script setup lang="ts">
/** Latest news: editorial header with a "more" link, then a grid of NewsCard (first card featured when there are exactly three). */
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
</script>
<template>
  <section v-if="data.items?.length" class="section news">
    <div class="container">
      <div class="row">
        <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" size="lg" tight />
        <NuxtLink :to="localePath('/news')" class="btn btn-ghost-dark more" v-reveal="120">{{ $t('news.more') }} <UiIcon name="arrow-right" :size="18" /></NuxtLink>
      </div>
      <div class="grid" :class="{ three: data.items.length === 3 }">
        <NewsCard v-for="(n, i) in data.items" :key="n.slug" :item="n" :featured="data.items.length === 3 && i === 0" />
      </div>
    </div>
  </section>
</template>
<style scoped>
.row { display: flex; justify-content: space-between; align-items: end; gap: 24px; flex-wrap: wrap; margin-bottom: clamp(28px, 3.5vw, 56px); }
.row :deep(.section-head) { margin-bottom: 0; }
.more { margin-bottom: 6px; }
.grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: clamp(16px, 1.8vw, 28px); }
.three { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.three :deep(.ncard:first-child) { grid-column: 1 / -1; }
@media (max-width: 900px) { .grid, .three { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .grid, .three { grid-template-columns: 1fr; } }
</style>
