<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue'
const { locale, t } = useI18n(); const route = useRoute()
const cat = computed(() => String(route.query.category || ''))
const pageNo = computed(() => Number(route.query.page || 1))
const { data } = await useAsyncData(`news-list-${locale.value}-${cat.value}-${pageNo.value}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/news`, { query: { category: cat.value || undefined, page: pageNo.value } }), { watch: [cat, pageNo] })
const cats = ['', 'news', 'events', 'research', 'company']
const items = computed(() => (data.value?.items || []) as any[])
const last = computed(() => Number(data.value?.last_page || 1))
const q = (p: number) => ({ query: { ...(cat.value ? { category: cat.value } : {}), ...(p > 1 ? { page: p } : {}) } })
// first article of the first page is the lead story
const lead = computed(() => (pageNo.value === 1 && items.value.length > 3 ? items.value[0] : null))
const rest = computed(() => (lead.value ? items.value.slice(1) : items.value))
</script>
<template>
  <section class="section news-index">
    <div class="container">
      <div class="filters" role="tablist">
        <NuxtLink v-for="c in cats" :key="c" :to="{ query: c ? { category: c } : {} }" :class="['chip', { active: cat === c }]" role="tab" :aria-selected="cat === c">{{ c ? t('news.' + c) : t('news.all') }}</NuxtLink>
      </div>
      <div v-if="lead" class="lead-wrap"><NewsCard :item="lead" class="lead-card" /></div>
      <div class="grid cols-3"><NewsCard v-for="n in rest" :key="n.slug" :item="n" /></div>
      <p v-if="!items.length" class="empty lead">{{ t('faq.nothing') }}</p>
      <nav v-if="last > 1" class="pager" aria-label="Pages">
        <NuxtLink :to="q(Math.max(1, pageNo - 1))" :class="['pg', 'arrow', { off: pageNo <= 1 }]" :aria-disabled="pageNo <= 1"><ChevronLeft :size="18" /></NuxtLink>
        <NuxtLink v-for="p in last" :key="p" :to="q(p)" :class="['pg', { active: p === pageNo }]" :aria-current="p === pageNo ? 'page' : undefined">{{ p }}</NuxtLink>
        <NuxtLink :to="q(Math.min(last, pageNo + 1))" :class="['pg', 'arrow', { off: pageNo >= last }]" :aria-disabled="pageNo >= last"><ChevronRight :size="18" /></NuxtLink>
      </nav>
    </div>
  </section>
</template>
<style scoped>
.news-index { padding-top: clamp(24px, 3vw, 48px); }
.filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: clamp(28px, 3vw, 44px); }
.chip { padding: .65rem 1.2rem; border-radius: 999px; border: 1px solid var(--line); font-size: .95rem; font-weight: 600; background: #fff; transition: background .25s, color .25s, border-color .25s; }
.chip:hover { background: var(--bg-alt); }
.chip.active { background: var(--ink); color: #fff; border-color: var(--ink); }
.lead-wrap { margin-bottom: clamp(16px, 2vw, 28px); }
.lead-card { display: grid !important; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); align-items: stretch; }
.lead-card :deep(.img) { aspect-ratio: auto; min-height: 380px; height: 100%; }
.lead-card :deep(.body) { padding: clamp(28px, 3vw, 48px); align-content: center; gap: 14px; }
.lead-card :deep(h3) { font-size: clamp(1.5rem, 1rem + 1.4vw, 2.4rem); line-height: 1.1; letter-spacing: -.02em; }
.lead-card :deep(.ex) { font-size: 1.05rem; -webkit-line-clamp: 5; }
.empty { margin-top: 24px; }
.pager { display: flex; flex-wrap: wrap; gap: 8px; margin-top: clamp(32px, 4vw, 56px); justify-content: center; }
.pg { min-width: 44px; height: 44px; padding: 0 14px; border-radius: 12px; border: 1px solid var(--line); display: inline-grid; place-items: center; font-weight: 700; font-size: .95rem; background: #fff; transition: background .2s; }
.pg:hover { background: var(--bg-alt); } .pg.active { background: var(--ink); color: #fff; border-color: var(--ink); }
.pg.off { opacity: .35; pointer-events: none; }
@media (max-width: 860px) { .lead-card { grid-template-columns: 1fr; } .lead-card :deep(.img) { aspect-ratio: 16 / 10; min-height: 0; height: auto; } }
</style>
