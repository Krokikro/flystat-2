<script setup lang="ts">
/** FAQ: sticky title (and search / topic chips when `searchable`) on the left, an accordion on the right. Answers animate open with a grid-row transition; the plus rotates into a cross. */
import { Search } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const q = ref(''); const cat = ref('')
const cats = computed(() => Array.from(new Set((props.data.items || []).map((i: any) => i.category))))
const items = computed(() => (props.data.items || []).filter((i: any) => (!cat.value || i.category === cat.value) && (!q.value || (i.q + ' ' + i.a).toLowerCase().includes(q.value.toLowerCase()))))
const open = ref<number | null>(null)
const uid = Math.random().toString(36).slice(2, 7)
watch([q, cat], () => (open.value = null))
</script>
<template>
  <section class="section faq">
    <div class="container inner">
      <div class="side">
        <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" tight />
        <div v-if="data.searchable" class="search"><Search :size="18" /><input v-model="q" type="search" :placeholder="t('faq.search')" :aria-label="t('faq.search')"></div>
        <div v-if="data.searchable && cats.length > 1" class="cats"><button type="button" :class="{ on: !cat }" @click="cat = ''">{{ t('faq.all') }}</button><button v-for="c in cats" :key="String(c)" type="button" :class="{ on: cat === c }" @click="cat = String(c)">{{ t('faq.cats.' + c) }}</button></div>
      </div>
      <div class="list">
        <div v-for="(it, i) in items" :key="it.q" class="item" :class="{ open: open === i }" v-reveal="Math.min(i, 6) * 50">
          <h3>
            <button type="button" :id="`fq-${uid}-${i}`" :aria-expanded="open === i" :aria-controls="`fa-${uid}-${i}`" @click="open = open === i ? null : i">
              <span>{{ it.q }}</span>
              <i class="plus" aria-hidden="true"><b /><b /></i>
            </button>
          </h3>
          <div :id="`fa-${uid}-${i}`" class="panel" role="region" :aria-labelledby="`fq-${uid}-${i}`">
            <div class="clip"><div class="a prose" v-html="it.a" /></div>
          </div>
        </div>
        <p v-if="!items.length" class="none">{{ t('faq.nothing') }}</p>
      </div>
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.4fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.side { position: sticky; top: calc(var(--header-h) + 32px); display: grid; gap: 18px; }
.side :deep(.section-head) { margin-bottom: 8px; }
.search { display: flex; align-items: center; gap: 10px; border: 1px solid var(--line); border-radius: 14px; padding: 0 14px; color: var(--muted); background: #fff; }
.search input { flex: 1; border: 0; padding: 14px 0; font: inherit; font-size: 1rem; outline: none; background: transparent; color: var(--ink); min-width: 0; }
.cats { display: flex; flex-wrap: wrap; gap: 8px; } .cats button { padding: 8px 14px; border-radius: 999px; border: 1px solid var(--line); font-size: .85rem; font-weight: 600; transition: background .3s, color .3s; } .cats button.on { background: var(--ink); color: #fff; border-color: var(--ink); }
.list { border-top: 1px solid var(--line); }
.item { border-bottom: 1px solid var(--line); }
.item h3 { font-size: inherit; letter-spacing: 0; line-height: inherit; }
.item h3 button { width: 100%; display: flex; justify-content: space-between; gap: 24px; align-items: center; text-align: left; padding: clamp(20px, 2vw, 30px) 0; font-weight: 700; font-size: clamp(1.1rem, 1rem + .45vw, 1.5rem); letter-spacing: -.015em; line-height: 1.25; color: var(--ink); transition: color .3s; }
.item h3 button:hover { color: var(--blue-d); }
.plus { position: relative; flex: none; width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--line); display: grid; place-items: center; transition: transform .5s var(--ease), background .4s, border-color .4s; }
.plus b { position: absolute; width: 16px; height: 2px; background: currentColor; border-radius: 2px; } .plus b:last-child { transform: rotate(90deg); }
.item.open .plus { transform: rotate(135deg); background: var(--ink); border-color: var(--ink); color: #fff; }
.panel { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .55s var(--ease); }
.item.open .panel { grid-template-rows: 1fr; }
.clip { overflow: hidden; }
.a { padding: 0 64px clamp(22px, 2vw, 32px) 0; font-size: clamp(1rem, 1rem + .15vw, 1.15rem); opacity: 0; transform: translateY(-6px); transition: opacity .4s ease .1s, transform .5s var(--ease) .05s; }
.item.open .a { opacity: 1; transform: none; }
.none { color: var(--muted); padding: 20px 0; }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; gap: 24px; } .side { position: static; } .a { padding-right: 0; } .plus { width: 38px; height: 38px; } }
</style>
