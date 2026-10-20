<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section cmp band-alt">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <div class="table-wrap" v-reveal>
        <table>
          <thead><tr><th></th><th class="fs">Flystat</th><th>{{ $t('faq.cats.device') === 'Device' ? 'Typical CGM' : 'CGM' }}</th></tr></thead>
          <tbody><tr v-for="(r, i) in data.rows" :key="i"><th scope="row">{{ r.label }}</th><td class="fs">{{ r.flystat }}</td><td>{{ r.market }}</td></tr></tbody>
        </table>
      </div>
      <p v-if="data.footnote" class="foot">{{ data.footnote }}</p>
    </div>
  </section>
</template>
<style scoped>
table { width: 100%; border-collapse: collapse; font-size: 15.5px; min-width: 560px; background: #fff; }
th, td { padding: 16px 20px; border-bottom: 1px solid var(--line); text-align: left; }
thead th { font-size: 12px; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
tbody th { font-weight: 600; width: 30%; }
.fs { background: #EAF2FF; font-weight: 700; color: var(--ink); }
tr:last-child td, tr:last-child th { border-bottom: 0; }
.foot { margin-top: 18px; font-size: 13.5px; color: var(--muted); max-width: 80ch; }
</style>
