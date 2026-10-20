<script setup lang="ts">
import world from '~/assets/world.json'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const countries = computed<Record<string, any>>(() => Object.fromEntries((props.data.countries || []).map((c: any) => [c.code, c])))
const selected = ref<string>('')
const hover = ref<string>('')
const sel = computed(() => countries.value[selected.value])
const distributors = computed(() => (props.data.distributors || []).filter((d: any) => d.countries?.includes(selected.value)))
const cls = (code: string) => { const c = countries.value[code]; return c ? `st-${c.status}` : 'st-none' }
const regions = computed(() => { const m: Record<string, any[]> = {}; for (const c of props.data.countries || []) (m[c.region] ||= []).push(c); return m })
const formData = computed(() => ({ cohort: 'distributor_contact', tone: 'light', title: sel.value?.name || t('map.choose'), text: distributors.value.length ? distributors.value.map((d: any) => d.name).join(', ') : t('map.no_distributor'), button: t('form.success_title') }))
</script>
<template>
  <section class="section wmap">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <div class="layout">
        <div class="map" v-reveal>
          <svg :viewBox="`0 0 ${world.w} ${world.h}`" role="img" :aria-label="data.title">
            <path :d="world.sphere" class="sphere" />
            <path v-for="(c, code) in world.countries" :key="code" :d="c.d" :class="['c', cls(String(code)), { hov: hover === code, sel: selected === code }]" @mouseenter="hover = String(code)" @mouseleave="hover = ''" @click="selected = countries[String(code)] ? String(code) : ''" />
            <g v-for="(c, code) in world.countries" :key="'m' + code">
              <circle v-if="countries[String(code)] && countries[String(code)].status !== 'free'" :cx="c.c[0]" :cy="c.c[1]" r="5" :class="['pin', cls(String(code))]" />
            </g>
          </svg>
          <div v-if="hover && countries[hover]" class="tip">{{ countries[hover].name }} · {{ t('map.' + countries[hover].status) }}</div>
          <ul class="legend"><li><i class="st-present" />{{ t('map.present') }}</li><li><i class="st-negotiation" />{{ t('map.negotiation') }}</li><li><i class="st-free" />{{ t('map.free') }}</li></ul>
        </div>
        <div class="side" v-reveal="120">
          <label class="field"><span class="sr-only">{{ t('map.choose') }}</span>
            <select v-model="selected"><option value="">{{ t('map.choose') }}</option><optgroup v-for="(list, r) in regions" :key="r" :label="t('map.regions.' + r)"><option v-for="c in list" :key="c.code" :value="c.code">{{ c.name }}</option></optgroup></select>
          </label>
          <div v-if="sel" class="info">
            <p class="eyebrow">{{ t('map.regions.' + sel.region) }}</p>
            <h3>{{ sel.name }}</h3>
            <p class="st" :class="'txt-' + sel.status">{{ t('map.' + sel.status) }}</p>
            <ul v-if="data.show_distributors && distributors.length" class="dists"><li v-for="d in distributors" :key="d.name"><b>{{ d.name }}</b><a v-if="d.website" :href="d.website" target="_blank" rel="noopener">{{ d.website }}</a></li></ul>
          </div>
        </div>
      </div>
      <BlocksLeadForm v-if="data.contact_form && selected" :data="formData" compact class="mini" />
    </div>
  </section>
</template>
<style scoped>
.layout { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr); gap: clamp(20px, 3vw, 48px); align-items: start; }
.map { position: relative; }
svg { width: 100%; height: auto; }
.sphere { fill: var(--bg-alt); }
.c { fill: #D6DEEE; stroke: #fff; stroke-width: .4; transition: fill .25s; cursor: default; }
.c.st-free { fill: #C2D4F0; cursor: pointer; } .c.st-negotiation { fill: #8FB0E8; cursor: pointer; } .c.st-present { fill: var(--blue-d); cursor: pointer; }
.c.hov, .c.sel { fill: var(--ink); }
.pin { fill: #fff; stroke-width: 3; pointer-events: none; } .pin.st-present { stroke: var(--blue-d); } .pin.st-negotiation { stroke: #8FB0E8; }
.tip { position: absolute; left: 12px; top: 12px; background: var(--ink); color: #fff; font-size: 13px; padding: 6px 10px; border-radius: 8px; pointer-events: none; }
.legend { list-style: none; margin: 10px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 16px; font-size: 13px; color: var(--ink-2); } .legend i { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; vertical-align: -1px; }
i.st-present { background: var(--blue-d); } i.st-negotiation { background: #8FB0E8; } i.st-free { background: #C2D4F0; }
.side { display: grid; gap: 16px; }
.info { display: grid; gap: 6px; padding: 20px; border: 1px solid var(--line); border-radius: var(--radius); }
.st { font-weight: 700; } .txt-present { color: var(--ok); } .txt-negotiation { color: var(--warn); } .txt-free { color: var(--muted); }
.dists { list-style: none; margin: 8px 0 0; padding: 0; display: grid; gap: 6px; font-size: 14px; } .dists a { color: var(--blue-d); display: block; }
.mini { padding-block: 32px 0; }
@media (max-width: 860px) { .layout { grid-template-columns: 1fr; } }
</style>
