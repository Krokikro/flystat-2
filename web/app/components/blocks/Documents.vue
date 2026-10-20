<script setup lang="ts">
/** Document cards. Data: { title, types[], items[{slug,type,title,file,version}] }. */
import { Download, ArrowRight, Clock } from '@lucide/vue'
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath(); const { t } = useI18n()
const ICON: Record<string, string> = { manual: 'book-open', spec: 'file-text', presskit: 'images', presentation: 'presentation', legal: 'scale' }
const tagOf = (d: any) => (d.file ? String(d.file).split('.').pop()?.toUpperCase() : 'PDF')
</script>
<template>
  <section v-if="data.items?.length" class="section docs">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <div class="grid cols-3">
        <component :is="d.file ? 'a' : (d.type === 'manual' ? 'NuxtLink' : 'div')" v-for="(d, i) in data.items" :key="d.slug" :href="d.file ? mediaUrl(d.file) : undefined" :to="d.type === 'manual' && !d.file ? localePath('/manual') : undefined" :target="d.file ? '_blank' : undefined" rel="noopener" :class="['doc', { live: d.file || d.type === 'manual' }]" v-reveal="i * 70">
          <div class="top">
            <span class="icon"><UiIcon :name="ICON[d.type] || 'file-text'" :size="26" /></span>
            <span v-if="d.file" class="tag mono">{{ tagOf(d) }}</span>
          </div>
          <h3>{{ d.title }}</h3>
          <p class="ver"><span class="mono">{{ d.version }}</span></p>
          <span v-if="d.file" class="act"><Download :size="18" /> {{ t('common.download') }}</span>
          <span v-else-if="d.type === 'manual'" class="act"><ArrowRight :size="18" /> {{ t('common.learn_more') }}</span>
          <span v-else class="act soon"><Clock :size="18" /> {{ t('common.soon') }}</span>
        </component>
      </div>
    </div>
  </section>
</template>
<style scoped>
.doc { display: grid; gap: 14px; align-content: start; min-width: 0; padding: clamp(24px, 2.6vw, 40px); border-radius: var(--radius); background: #fff; border: 1px solid var(--line); position: relative; overflow: hidden; transition: transform .5s var(--ease), box-shadow .5s var(--ease), border-color .3s; min-height: 260px; }
.doc::before { content: ""; position: absolute; inset: 0 0 auto; height: 6px; background: linear-gradient(90deg, var(--blue), var(--purple)); opacity: 0; transition: opacity .3s; }
.doc.live:hover { transform: translateY(-4px); box-shadow: var(--shadow); border-color: transparent; } .doc.live:hover::before { opacity: 1; }
.top { display: flex; justify-content: space-between; align-items: start; }
.icon { width: 60px; height: 60px; border-radius: 18px; display: grid; place-items: center; background: linear-gradient(140deg, #EAF2FF, #F3F0FF); color: var(--blue-d); }
.tag { font-size: .68rem; padding: 6px 10px; border-radius: 8px; background: var(--bg-alt); color: var(--ink-2); }
h3 { font-size: clamp(1.15rem, 1rem + .4vw, 1.4rem); }
.ver .mono { font-size: .7rem; color: var(--muted); }
.act { margin-top: auto; padding-top: 10px; display: inline-flex; gap: 8px; align-items: center; color: var(--blue-d); font-weight: 700; font-size: .95rem; }
.act.soon { color: var(--muted); }
</style>
