<script setup lang="ts">
import { ChevronDown, Search } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const q = ref(''); const cat = ref('')
const cats = computed(() => Array.from(new Set((props.data.items || []).map((i: any) => i.category))))
const items = computed(() => (props.data.items || []).filter((i: any) => (!cat.value || i.category === cat.value) && (!q.value || (i.q + ' ' + i.a).toLowerCase().includes(q.value.toLowerCase()))))
const open = ref<number | null>(null)
</script>
<template>
  <section class="section faq">
    <div class="container inner">
      <div class="side">
        <UiSectionHead :title="data.title" />
        <div v-if="data.searchable" class="search"><Search :size="16" /><input v-model="q" type="search" :placeholder="t('faq.search')" :aria-label="t('faq.search')"></div>
        <div v-if="data.searchable && cats.length > 1" class="cats"><button :class="{ on: !cat }" @click="cat = ''">{{ t('faq.all') }}</button><button v-for="c in cats" :key="String(c)" :class="{ on: cat === c }" @click="cat = String(c)">{{ t('faq.cats.' + c) }}</button></div>
      </div>
      <div class="list">
        <details v-for="(it, i) in items" :key="i" :open="open === i" v-reveal="i * 40" @toggle="(e: any) => e.target.open && (open = i)">
          <summary><span>{{ it.q }}</span><ChevronDown :size="20" /></summary>
          <div class="a prose" v-html="it.a" />
        </details>
        <p v-if="!items.length" class="none">{{ t('faq.nothing') }}</p>
      </div>
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.6fr); gap: clamp(24px, 4vw, 64px); align-items: start; }
.side { position: sticky; top: calc(var(--header-h) + 24px); display: grid; gap: 14px; }
.search { display: flex; align-items: center; gap: 10px; border: 1px solid var(--line); border-radius: 12px; padding: 0 12px; color: var(--muted); }
.search input { flex: 1; border: 0; padding: 12px 0; font: inherit; font-size: 15px; outline: none; background: transparent; color: var(--ink); min-width: 0; }
.cats { display: flex; flex-wrap: wrap; gap: 6px; } .cats button { padding: 6px 12px; border-radius: 999px; border: 1px solid var(--line); font-size: 13px; font-weight: 600; } .cats button.on { background: var(--ink); color: #fff; border-color: var(--ink); }
details { border-bottom: 1px solid var(--line); }
summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; gap: 16px; align-items: center; padding: 20px 0; font-weight: 700; font-size: 17px; letter-spacing: -.01em; }
summary::-webkit-details-marker { display: none; }
summary svg { flex: none; transition: transform .3s var(--ease); color: var(--muted); } details[open] summary svg { transform: rotate(180deg); }
.a { padding: 0 0 22px; font-size: 16px; }
.none { color: var(--muted); padding: 20px 0; }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } .side { position: static; } }
</style>
