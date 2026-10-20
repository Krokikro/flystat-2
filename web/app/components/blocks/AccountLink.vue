<script setup lang="ts">
import { UserRound, ArrowUpRight } from '@lucide/vue'
defineProps<{ data: Record<string, any> }>()
const boot = inject<any>('boot', null)
const url = computed(() => boot?.value?.settings?.account_url || '')
</script>
<template>
  <section class="section acc">
    <div class="container box" v-reveal>
      <div class="ic"><UserRound :size="28" /></div>
      <div class="t"><h2>{{ data.title }}</h2><p class="lead">{{ data.text }}</p></div>
      <a v-if="url" :href="url" class="btn btn-primary" target="_blank" rel="noopener">{{ data.button }} <ArrowUpRight :size="16" /></a>
      <span v-else class="btn btn-ghost-dark soon">{{ data.button }} · {{ $t('common.soon') }}</span>
    </div>
  </section>
</template>
<style scoped>
.acc { padding-block: 24px 56px; }
.box { display: grid; grid-template-columns: auto 1fr auto; gap: 24px; align-items: center; background: linear-gradient(120deg, #EAF2FF, #F3F0FF); border-radius: var(--radius-lg); padding: clamp(24px, 3vw, 40px); }
.ic { width: 64px; height: 64px; border-radius: 18px; background: #fff; color: var(--blue-d); display: grid; place-items: center; }
.t { display: grid; gap: 6px; } .t h2 { font-size: clamp(22px, 2.4vw, 30px); }
.soon { opacity: .7; cursor: default; }
@media (max-width: 760px) { .box { grid-template-columns: 1fr; } }
</style>
