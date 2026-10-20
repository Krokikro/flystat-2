<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const { units, set } = useUnits(); const { t } = useI18n()
const boot = inject<any>('boot', null)
const conv = (v: string) => units.value === 'mgdl' ? v.replace(/2[.,]2[–-]22[.,]2 mmol\/L \((40[–-]400) mg\/dL\)/, '$1 mg/dL (2.2–22.2 mmol/L)') : v
</script>
<template>
  <section class="section spec">
    <div class="container">
      <div class="head">
        <UiSectionHead :title="data.title" />
        <div class="units" role="group" :aria-label="t('nav.units')"><button :class="{ on: units === 'mmol' }" @click="set('mmol')">{{ t('common.mmol') }}</button><button :class="{ on: units === 'mgdl' }" @click="set('mgdl')">{{ t('common.mgdl') }}</button></div>
      </div>
      <div class="table-wrap" v-reveal>
        <table>
          <tbody>
            <tr v-for="(r, i) in data.rows" :key="i"><th scope="row">{{ r.label }}</th><td>{{ conv(r.value) }}</td><td class="note">{{ r.note }}</td></tr>
          </tbody>
        </table>
      </div>
      <p v-if="data.footnote" class="foot">{{ data.footnote }}</p>
      <p v-if="boot?.value?.settings?.certification_status" class="cert mono">{{ boot.value.settings.certification_status }}</p>
    </div>
  </section>
</template>
<style scoped>
.head { display: flex; justify-content: space-between; align-items: end; gap: 20px; flex-wrap: wrap; }
.units { display: inline-flex; border: 1px solid var(--line); border-radius: 999px; padding: 3px; margin-bottom: clamp(28px, 4vw, 56px); }
.units button { padding: 7px 14px; border-radius: 999px; font-size: 0.8125rem; font-weight: 700; color: var(--muted); } .units button.on { background: var(--ink); color: #fff; }
table { width: 100%; border-collapse: collapse; font-size: 0.9688rem; min-width: 640px; }
th { text-align: left; font-weight: 700; padding: 16px 20px; width: 28%; border-bottom: 1px solid var(--line); background: var(--bg-alt); }
td { padding: 16px 20px; border-bottom: 1px solid var(--line); font-variant-numeric: tabular-nums; } td.note { color: var(--muted); font-size: 0.875rem; }
tr:last-child th, tr:last-child td { border-bottom: 0; }
.foot { margin-top: 18px; font-size: 0.8438rem; color: var(--muted); max-width: 80ch; }
.cert { margin-top: 10px; color: var(--warn); font-size: 0.6875rem; }
</style>
