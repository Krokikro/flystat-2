<script setup lang="ts">
/** Interactive world map. Data: { title, text, show_distributors, contact_form, countries[{code,name,region,status}], distributors[] }. */
import world from '~/assets/world.json'
import { ChevronDown, Globe2 } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const countries = computed<Record<string, any>>(() => Object.fromEntries((props.data.countries || []).map((c: any) => [c.code, c])))
const selected = ref<string>('')
const hover = ref<string>('')
const tipPos = ref({ x: 0, y: 0 })
const sel = computed(() => countries.value[selected.value])
const distributors = computed(() => (props.data.distributors || []).filter((d: any) => d.countries?.includes(selected.value)))
const cls = (code: string) => { const c = countries.value[code]; return c ? `st-${c.status}` : 'st-none' }
const regions = computed(() => { const m: Record<string, any[]> = {}; for (const c of props.data.countries || []) (m[c.region] ||= []).push(c); return m })
const byStatus = (s: string) => (props.data.countries || []).filter((c: any) => c.status === s)
const counts = computed(() => ({ present: byStatus('present').length, negotiation: byStatus('negotiation').length, free: byStatus('free').length }))
const pins = computed(() => Object.entries(world.countries as Record<string, any>).filter(([code]) => countries.value[code] && countries.value[code].status !== 'free').map(([code, c]) => ({ code, x: c.c[0], y: c.c[1], status: countries.value[code].status })))
const formData = computed(() => ({ cohort: 'distributor_contact', tone: 'light', title: sel.value?.name || t('map.choose'), text: distributors.value.length ? distributors.value.map((d: any) => d.name).join(', ') : t('map.no_distributor'), button: t('common.send') }))
const onMove = (e: MouseEvent) => { const r = (e.currentTarget as HTMLElement).getBoundingClientRect(); tipPos.value = { x: e.clientX - r.left, y: e.clientY - r.top } }
const pick = (code: string) => { if (countries.value[code]) selected.value = code }
</script>
<template>
  <section class="section wmap">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
      <div class="layout">
        <div class="map" v-reveal @mousemove="onMove">
          <svg :viewBox="`0 0 ${world.w} ${world.h}`" role="img" :aria-label="data.title">
            <defs>
              <radialGradient id="wm-glow" cx="50%" cy="40%" r="70%"><stop offset="0" stop-color="#fff" /><stop offset="1" stop-color="#E7EEFB" /></radialGradient>
            </defs>
            <path :d="world.sphere" class="sphere" />
            <path v-for="(c, code) in world.countries" :key="code" :d="c.d" :class="['c', cls(String(code)), { hov: hover === code, sel: selected === code }]" @mouseenter="hover = String(code)" @mouseleave="hover = ''" @click="pick(String(code))" />
            <g v-for="p in pins" :key="'m' + p.code" :class="['pin', 'st-' + p.status, { sel: selected === p.code }]" :transform="`translate(${p.x} ${p.y})`" @click="pick(p.code)" @mouseenter="hover = p.code" @mouseleave="hover = ''">
              <circle r="14" class="pulse" /><circle r="14" class="pulse p2" />
              <circle r="5.5" class="core" />
            </g>
          </svg>
          <div v-if="hover && countries[hover]" class="tip" :style="{ left: tipPos.x + 'px', top: tipPos.y + 'px' }"><b>{{ countries[hover].name }}</b><span :class="'txt-' + countries[hover].status">{{ t('map.' + countries[hover].status) }}</span></div>
          <ul class="legend" :aria-label="t('map.legend')">
            <li><i class="st-present" /><span>{{ t('map.present') }}</span><b>{{ counts.present }}</b></li>
            <li><i class="st-negotiation" /><span>{{ t('map.negotiation') }}</span><b>{{ counts.negotiation }}</b></li>
            <li><i class="st-free" /><span>{{ t('map.free') }}</span><b>{{ counts.free }}</b></li>
          </ul>
        </div>

        <div class="side" v-reveal="120">
          <label class="field pick">
            <span class="sr-only">{{ t('map.choose') }}</span>
            <span class="sel-wrap"><Globe2 :size="18" class="g" /><select v-model="selected"><option value="">{{ t('map.choose') }}</option><optgroup v-for="(list, r) in regions" :key="r" :label="t('map.regions.' + r)"><option v-for="c in list" :key="c.code" :value="c.code">{{ c.name }}</option></optgroup></select><ChevronDown :size="18" class="chev" /></span>
          </label>
          <Transition name="fade" mode="out-in">
            <div v-if="sel" :key="sel.code" class="info" :class="'is-' + sel.status">
              <p class="eyebrow">{{ t('map.regions.' + sel.region) }}</p>
              <h3>{{ sel.name }}</h3>
              <p class="st" :class="'txt-' + sel.status"><i :class="'st-' + sel.status" />{{ t('map.' + sel.status) }}</p>
              <ul v-if="data.show_distributors && distributors.length" class="dists"><li v-for="d in distributors" :key="d.name"><b>{{ d.name }}</b><a v-if="d.website" :href="d.website" target="_blank" rel="noopener">{{ d.website }}</a></li></ul>
            </div>
            <div v-else class="info empty">
              <p class="eyebrow">{{ t('map.legend') }}</p>
              <div v-for="s in ['present', 'negotiation'].filter((k) => byStatus(k).length)" :key="s" class="grp">
                <p class="gl"><i :class="'st-' + s" />{{ t('map.' + s) }}</p>
                <div class="chips"><button v-for="c in byStatus(s)" :key="c.code" type="button" class="chip" @click="selected = c.code">{{ c.name }}</button></div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
      <BlocksLeadForm v-if="data.contact_form && selected" :data="formData" compact class="mini" />
    </div>
  </section>
</template>
<style scoped>
.layout { display: grid; grid-template-columns: minmax(0, 2.2fr) minmax(300px, 1fr); gap: clamp(20px, 3vw, 48px); align-items: start; }
.map { position: relative; padding: clamp(10px, 1.4vw, 20px); border-radius: var(--radius-lg); background: linear-gradient(160deg, #F6F9FF, #EDF2FC); border: 1px solid var(--line); overflow: hidden; }
.map > svg { width: 100%; height: auto; display: block; overflow: visible; }
.sphere { fill: url(#wm-glow); }
.c { fill: #D3DCEC; stroke: #fff; stroke-width: .5; transition: fill .3s; cursor: default; }
.c.st-free { fill: #BFD2F0; cursor: pointer; } .c.st-negotiation { fill: #8FB0E8; cursor: pointer; } .c.st-present { fill: var(--blue-d); cursor: pointer; }
.c.hov { fill: var(--navy-600); } .c.sel { fill: var(--ink); }
.pin { cursor: pointer; }
.pin .core { fill: #fff; stroke-width: 3; }
.pin.st-present .core { stroke: var(--blue-d); } .pin.st-negotiation .core { stroke: #6E97DE; }
.pin .pulse { fill: var(--blue-d); opacity: 0; transform-box: fill-box; transform-origin: center; animation: pulse 3s var(--ease) infinite; }
.pin.st-negotiation .pulse { fill: #8FB0E8; }
.pin .p2 { animation-delay: 1.5s; }
.pin.sel .core { fill: var(--ink); stroke: #fff; }
@keyframes pulse { 0% { transform: scale(.25); opacity: .55; } 100% { transform: scale(1.3); opacity: 0; } }
.tip { position: absolute; transform: translate(14px, 14px); background: var(--ink); color: #fff; font-size: .85rem; padding: 8px 12px; border-radius: 10px; pointer-events: none; display: grid; gap: 2px; white-space: nowrap; box-shadow: var(--shadow); z-index: 3; }
.tip span { font-size: .75rem; opacity: .85; }
.legend { list-style: none; margin: 12px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 8px; font-size: .85rem; color: var(--ink-2); }
.legend li { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px 7px 10px; border-radius: 999px; background: #fff; border: 1px solid var(--line); }
.legend b { font-variant-numeric: tabular-nums; color: var(--ink); }
i.st-present, i.st-negotiation, i.st-free { display: inline-block; width: 12px; height: 12px; border-radius: 50%; flex: none; }
i.st-present { background: var(--blue-d); } i.st-negotiation { background: #8FB0E8; } i.st-free { background: #BFD2F0; }
.side { display: grid; gap: 16px; position: sticky; top: calc(var(--header-h) + 24px); }
.sel-wrap { position: relative; display: block; }
.sel-wrap .g { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--blue-d); pointer-events: none; }
.sel-wrap .chev { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
.pick select { appearance: none; -webkit-appearance: none; padding: 1rem 2.6rem 1rem 2.8rem; font-size: 1.05rem; font-weight: 600; border-radius: 16px; }
.info { display: grid; gap: 8px; padding: clamp(20px, 2.4vw, 32px); border: 1px solid var(--line); border-radius: var(--radius); background: #fff; }
.info h3 { font-size: clamp(1.4rem, 1rem + .9vw, 2rem); }
.info.is-present { background: linear-gradient(160deg, #EEF5FF, #fff); } .info.is-negotiation { background: linear-gradient(160deg, #FFF8EC, #fff); }
.st { font-weight: 700; display: inline-flex; align-items: center; gap: 8px; } .txt-present { color: var(--blue-d); } .txt-negotiation { color: #B54708; } .txt-free { color: var(--muted); }
.dists { list-style: none; margin: 10px 0 0; padding: 12px 0 0; border-top: 1px solid var(--line); display: grid; gap: 8px; font-size: .95rem; } .dists a { color: var(--blue-d); display: block; }
.grp { display: grid; gap: 8px; margin-top: 6px; }
.gl { display: inline-flex; align-items: center; gap: 8px; font-size: .8rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; color: var(--muted); }
.chips { display: flex; flex-wrap: wrap; gap: 6px; }
.chip { padding: 7px 12px; border-radius: 999px; border: 1px solid var(--line); font-size: .85rem; font-weight: 600; background: #fff; transition: background .2s, border-color .2s; }
.chip:hover { background: var(--bg-alt); border-color: var(--sky); }
.mini { margin-top: clamp(24px, 3vw, 48px); border-radius: var(--radius-lg); overflow: hidden; }
.fade-enter-active, .fade-leave-active { transition: opacity .25s, transform .25s; } .fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(6px); }
@media (max-width: 960px) { .layout { grid-template-columns: 1fr; } .side { position: static; } }
@media (prefers-reduced-motion: reduce) { .pin .pulse { animation: none; } }
</style>
