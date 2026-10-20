<script setup lang="ts">
const { locale, t } = useI18n(); const route = useRoute(); const router = useRouter()
const cat = computed(() => String(route.query.category || ''))
const pageNo = computed(() => Number(route.query.page || 1))
const { data } = await useAsyncData(`news-list-${locale.value}-${cat.value}-${pageNo.value}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/news`, { query: { category: cat.value || undefined, page: pageNo.value } }), { watch: [cat, pageNo] })
const cats = ['', 'news', 'events', 'research', 'company']
</script>
<template>
  <section class="section news-index">
    <div class="container">
      <div class="filters" role="tablist">
        <NuxtLink v-for="c in cats" :key="c" :to="{ query: c ? { category: c } : {} }" :class="['chip', { active: cat === c }]" role="tab">{{ c ? t('news.' + c) : t('news.all') }}</NuxtLink>
      </div>
      <div class="grid cols-3"><NewsCard v-for="n in data?.items || []" :key="n.slug" :item="n" /></div>
      <nav v-if="(data?.last_page || 1) > 1" class="pager" aria-label="Pages">
        <NuxtLink v-for="p in data.last_page" :key="p" :to="{ query: { ...(cat ? { category: cat } : {}), page: p } }" :class="['chip', { active: p === pageNo }]">{{ p }}</NuxtLink>
      </nav>
    </div>
  </section>
</template>
<style scoped>
.filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 28px; }
.chip { padding: 8px 14px; border-radius: 999px; border: 1px solid var(--line); font-size: 13.5px; font-weight: 600; }
.chip.active { background: var(--ink); color: #fff; border-color: var(--ink); }
.pager { display: flex; gap: 8px; margin-top: 32px; }
</style>
