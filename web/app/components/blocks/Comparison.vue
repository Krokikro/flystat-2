<script setup lang="ts">
/** Flystat vs typical CGM matrix. Data: { title, text, rows[{label, flystat, market}], footnote }. */
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const ICONS = ['calendar-days', 'timer', 'droplet-off', 'package-open', 'waves', 'circle-dot', 'bluetooth', 'thermometer']
const icon = (i: number) => ICONS[i] || 'circle'
const rows = computed(() => (props.data.rows || []) as { label: string; flystat: string; market: string }[])
// No dedicated key for the second column yet; keep the English wording where the UI is English.
const marketLabel = computed(() => (t('faq.cats.device') === 'Device' ? 'Typical CGM' : 'CGM'))
</script>
<template>
  <section class="section cmp band-alt">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <div class="matrix-wrap" v-reveal>
        <table class="matrix">
          <thead>
            <tr>
              <th class="crit" scope="col"><span class="sr-only">{{ data.title }}</span></th>
              <th class="fs" scope="col"><span class="brand"><span class="dot" />Flystat</span></th>
              <th class="mk" scope="col"><span class="mk-l">{{ marketLabel }}</span></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(r, i) in rows" :key="i">
              <th class="crit" scope="row"><span class="cell"><span class="ic"><UiIcon :name="icon(i)" :size="20" /></span><span>{{ r.label }}</span></span></th>
              <td class="fs"><span class="cell"><UiIcon name="check" :size="18" class="chk" /><span>{{ r.flystat }}</span></span></td>
              <td class="mk">{{ r.market }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-if="data.footnote" class="foot" v-reveal>{{ data.footnote }}</p>
    </div>
  </section>
</template>
<style scoped>
.matrix-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--line); box-shadow: 0 24px 60px rgba(16, 42, 86, .08); }
.matrix { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 720px; font-size: 1rem; }
th, td { text-align: left; vertical-align: middle; padding: clamp(18px, 2vw, 28px) clamp(18px, 2.2vw, 36px); border-bottom: 1px solid var(--line); }
tbody tr:last-child th, tbody tr:last-child td { border-bottom: 0; }
thead th { padding-top: clamp(20px, 2vw, 30px); padding-bottom: clamp(16px, 1.6vw, 24px); font-size: .8rem; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); font-weight: 700; }
.crit { width: 30%; font-weight: 700; color: var(--ink); background: #fff; }
tbody .crit { font-size: 1.05rem; }
.cell { display: flex; align-items: center; gap: 14px; }
.ic { width: 42px; height: 42px; border-radius: 12px; background: var(--bg-alt); color: var(--blue-d); display: grid; place-items: center; flex: none; }
.fs { width: 36%; background: linear-gradient(180deg, #EEF5FF, #E6F0FF); color: var(--ink); font-weight: 700; font-size: clamp(1.05rem, 1rem + .3vw, 1.3rem); letter-spacing: -.01em; position: relative; }
thead .fs { background: var(--ink); color: #fff; }
.brand { display: inline-flex; align-items: center; gap: 10px; font-size: 1rem; letter-spacing: .06em; font-weight: 900; }
.dot { width: 10px; height: 10px; border-radius: 50%; background: var(--blue); box-shadow: 0 0 0 4px rgba(46, 144, 250, .3); }
.chk { color: var(--blue-d); flex: none; background: #fff; border-radius: 50%; padding: 3px; width: 26px; height: 26px; }
.mk { color: var(--ink-2); font-size: 1rem; }
.mk-l { display: inline-block; }
.foot { margin-top: 20px; font-size: .875rem; color: var(--muted); max-width: 90ch; }
/* phones: criterion column sticks while the matrix scrolls sideways */
@media (max-width: 760px) {
  .matrix { min-width: 640px; font-size: .95rem; }
  .crit { position: sticky; left: 0; z-index: 2; width: 44vw; min-width: 160px; box-shadow: 8px 0 16px -10px rgba(16, 42, 86, .25); }
  tbody .crit .ic, td.fs .chk { display: none; }
}
</style>
