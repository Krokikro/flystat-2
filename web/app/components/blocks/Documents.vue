<script setup lang="ts">
import { FileText, Download } from '@lucide/vue'
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath(); const { t } = useI18n()
</script>
<template>
  <section v-if="data.items?.length" class="section docs">
    <div class="container">
      <UiSectionHead :title="data.title" />
      <div class="grid cols-3">
        <component :is="d.file ? 'a' : (d.type === 'manual' ? 'NuxtLink' : 'div')" v-for="d in data.items" :key="d.slug" :href="d.file ? mediaUrl(d.file) : undefined" :to="d.type === 'manual' && !d.file ? localePath('/manual') : undefined" :target="d.file ? '_blank' : undefined" rel="noopener" class="card doc" v-reveal>
          <div class="icon"><FileText /></div>
          <h3>{{ d.title }}</h3>
          <p class="mono">{{ d.version }}<template v-if="!d.file && d.type !== 'manual'"> · {{ t('common.soon') }}</template></p>
          <span v-if="d.file" class="dl"><Download :size="16" /> {{ t('common.download') }}</span>
        </component>
      </div>
    </div>
  </section>
</template>
<style scoped>.doc p { font-size: 11px; color: var(--muted); } .dl { display: inline-flex; gap: 6px; align-items: center; color: var(--blue-d); font-weight: 700; font-size: 14px; }</style>
