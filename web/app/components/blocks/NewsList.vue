<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
</script>
<template>
  <section v-if="data.items?.length" class="section">
    <div class="container">
      <div class="row"><UiSectionHead :title="data.title" /><NuxtLink :to="localePath('/news')" class="btn btn-ghost-dark more">{{ $t('news.more') }}</NuxtLink></div>
      <div class="grid cols-3"><NewsCard v-for="n in data.items" :key="n.slug" :item="n" /></div>
    </div>
  </section>
</template>
<style scoped>.row { display: flex; justify-content: space-between; align-items: end; gap: 16px; flex-wrap: wrap; } .more { margin-bottom: clamp(28px, 4vw, 56px); }</style>
