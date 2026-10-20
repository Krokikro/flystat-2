<script setup lang="ts">
import { Download } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const m = computed(() => props.data.manual)
const toc = computed(() => { const out: { id: string; title: string }[] = []; const re = /<h2 id="([^"]+)">([^<]+)<\/h2>/g; let x; while ((x = re.exec(m.value?.body || ''))) out.push({ id: x[1], title: x[2] }); return out })
</script>
<template>
  <section v-if="m" class="section manual">
    <div class="container inner">
      <aside class="toc">
        <p class="eyebrow">{{ t('common.contents') }}</p>
        <nav><a v-for="s in toc" :key="s.id" :href="'#' + s.id">{{ s.title }}</a></nav>
        <p class="ver mono">{{ t('common.version') }} {{ m.version }} · {{ m.effective_at }}</p>
        <a v-if="m.file" id="download" :href="mediaUrl(m.file)" class="btn btn-navy" target="_blank" rel="noopener"><Download :size="16" /> {{ t('common.download') }} PDF</a>
        <p v-else id="download" class="ver">PDF — {{ t('common.soon').toLowerCase() }}</p>
      </aside>
      <article class="prose body" v-html="m.body" />
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: clamp(24px, 4vw, 72px); align-items: start; }
.toc { position: sticky; top: calc(var(--header-h) + 24px); display: grid; gap: 14px; }
.toc nav { display: grid; gap: 8px; font-size: 0.875rem; } .toc nav a { color: var(--ink-2); } .toc nav a:hover { color: var(--blue-d); }
.ver { font-size: 0.6875rem; color: var(--muted); }
.body :deep(h2) { scroll-margin-top: calc(var(--header-h) + 16px); }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } .toc { position: static; } }
</style>
