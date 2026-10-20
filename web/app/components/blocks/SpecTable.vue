<script setup lang="ts">
/**
 * Spec sheet: sticky render plate on the left, two-column grid of large label/value cells on the right.
 * Data contract unchanged: { title, rows[{label,value,note}], footnote }. Units switch via useUnits.
 */
const props = defineProps<{ data: Record<string, any> }>()
const { units, set } = useUnits(); const { t } = useI18n()
const boot = inject<any>('boot', null)
const cert = computed(() => boot?.value?.settings?.certification_status || '')
const conv = (v: string) => units.value === 'mgdl' ? v.replace(/2[.,]2[–-]22[.,]2 mmol\/L \((40[–-]400) mg\/dL\)/, '$1 mg/dL (2.2–22.2 mmol/L)') : v
// Icons by row position (rows come from one source for all locales, so the order is stable).
const ICONS = ['calendar-days', 'gauge', 'timer', 'sliders-horizontal', 'hourglass', 'bluetooth', 'droplets', 'circle-dot', 'ruler', 'syringe', 'thermometer', 'warehouse', 'battery-full', 'package']
const icon = (i: number) => ICONS[i] || 'circle'
const rows = computed(() => (props.data.rows || []) as { label: string; value: string; note?: string }[])
const hero = computed(() => rows.value.filter((r) => r.value.length <= 14).slice(0, 3))
</script>
<template>
  <section class="section spec">
    <div class="container">
      <div class="head">
        <UiSectionHead :title="data.title" :text="data.text" />
        <div class="units" role="group" :aria-label="t('nav.units')">
          <button :class="{ on: units === 'mmol' }" @click="set('mmol')">{{ t('common.mmol') }}</button>
          <button :class="{ on: units === 'mgdl' }" @click="set('mgdl')">{{ t('common.mgdl') }}</button>
        </div>
      </div>

      <div class="sheet">
        <aside class="plate" v-reveal>
          <div class="plate-in">
            <img src="/media/renders/applicator-angle.webp" alt="" width="1325" height="1600" loading="lazy" decoding="async" class="r-app">
            <img src="/media/renders/sensor.webp" alt="" width="1215" height="614" loading="lazy" decoding="async" class="r-sensor">
          </div>
          <ul v-if="hero.length" class="hero-vals">
            <li v-for="(r, i) in hero" :key="i"><span class="hv">{{ conv(r.value) }}</span><span class="hl">{{ r.label }}</span></li>
          </ul>
        </aside>

        <dl class="rows">
          <div v-for="(r, i) in rows" :key="i" class="row" v-reveal="Math.min(i, 7) * 40">
            <dt><span class="ic"><UiIcon :name="icon(i)" :size="20" /></span>{{ r.label }}</dt>
            <dd><span class="val">{{ conv(r.value) }}</span><span v-if="r.note" class="note">{{ r.note }}</span></dd>
          </div>
        </dl>
      </div>

      <div class="foot-wrap" v-reveal>
        <p v-if="data.footnote" class="foot">{{ data.footnote }}</p>
        <p v-if="cert" class="cert"><UiIcon name="shield-alert" :size="16" />{{ cert }}</p>
      </div>
    </div>
  </section>
</template>
<style scoped>
.head { display: flex; justify-content: space-between; align-items: end; gap: 20px; flex-wrap: wrap; }
.head :deep(.section-head) { margin-bottom: clamp(28px, 4vw, 56px); }
.units { display: inline-flex; border: 1px solid var(--line); border-radius: 999px; padding: 4px; margin-bottom: clamp(28px, 4vw, 56px); background: #fff; }
.units button { padding: .55rem 1.1rem; border-radius: 999px; font-size: .875rem; font-weight: 700; color: var(--muted); transition: background .25s, color .25s; }
.units button.on { background: var(--ink); color: #fff; }

.sheet { display: grid; grid-template-columns: minmax(300px, .9fr) minmax(0, 1.6fr); gap: clamp(28px, 4vw, 72px); align-items: start; }
.plate { position: sticky; top: calc(var(--header-h) + 24px); display: grid; gap: 18px; }
.plate-in { position: relative; aspect-ratio: 4 / 5; border-radius: var(--radius-lg); overflow: hidden; background: radial-gradient(120% 90% at 20% 0%, #FFFFFF 0%, #EAF2FF 42%, #D9E6FB 100%); }
.plate-in::after { content: ""; position: absolute; inset: auto 8% 8%; height: 18%; border-radius: 50%; background: radial-gradient(closest-side, rgba(16, 42, 86, .18), transparent 72%); }
.r-app { position: absolute; left: 10%; top: 6%; width: 62%; height: auto; filter: drop-shadow(0 30px 40px rgba(16, 42, 86, .28)); transform: rotate(-4deg); transition: transform 1.2s var(--ease); z-index: 1; }
.r-sensor { position: absolute; right: 6%; bottom: 10%; width: 46%; height: auto; filter: drop-shadow(0 18px 24px rgba(16, 42, 86, .22)); z-index: 2; transition: transform 1.2s var(--ease); }
.plate:hover .r-app { transform: rotate(-2deg) translateY(-6px); } .plate:hover .r-sensor { transform: translateY(-4px); }
.hero-vals { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 10px; }
.hero-vals li { display: grid; gap: 4px; padding: 14px 16px; border-radius: var(--radius-sm); background: var(--bg-alt); min-width: 0; }
.hv { font-size: clamp(1.05rem, .85rem + .55vw, 1.4rem); font-weight: 900; letter-spacing: -.02em; line-height: 1.1; color: var(--ink); font-variant-numeric: tabular-nums; hyphens: auto; }
.hl { font-size: .72rem; color: var(--muted); font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }

.rows { margin: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 clamp(24px, 3vw, 48px); border-top: 1px solid var(--line); }
.row { display: grid; gap: 10px; padding: clamp(18px, 2vw, 28px) 0; border-bottom: 1px solid var(--line); min-width: 0; align-content: start; }
.row dt { display: flex; align-items: center; gap: 10px; font-size: .8rem; letter-spacing: .08em; text-transform: uppercase; font-weight: 700; color: var(--muted); }
.ic { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; background: var(--bg-alt); color: var(--blue-d); flex: none; }
.row dd { margin: 0; display: grid; gap: 6px; }
.val { font-size: clamp(1.2rem, 1rem + .7vw, 1.75rem); font-weight: 700; letter-spacing: -.02em; line-height: 1.15; color: var(--ink); font-variant-numeric: tabular-nums; text-wrap: balance; }
.note { color: var(--ink-2); font-size: .95rem; line-height: 1.45; max-width: 42ch; }

.foot-wrap { margin-top: clamp(28px, 3vw, 44px); display: grid; gap: 12px; }
.foot { font-size: .875rem; color: var(--muted); max-width: 90ch; line-height: 1.5; }
.cert { display: inline-flex; gap: 8px; align-items: center; font-size: .8rem; font-weight: 700; color: #B54708; background: #FFFAEB; border: 1px solid #FEDF89; padding: 8px 14px; border-radius: 999px; width: fit-content; max-width: 100%; }

@media (max-width: 1100px) { .rows { grid-template-columns: 1fr; } }
@media (max-width: 900px) {
  .sheet { grid-template-columns: 1fr; }
  .plate { position: static; }
  .plate-in { aspect-ratio: 16 / 10; }
  .r-app { width: 36%; left: 12%; top: 4%; } .r-sensor { width: 40%; right: 8%; bottom: 14%; }
}

</style>
