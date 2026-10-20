<script setup lang="ts">
/** Long-form manual with sticky, scroll-spied in-page nav. Anchor ids in the body (install, pair, account …) are preserved. */
import { Download } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const m = computed(() => props.data.manual)
const toc = computed(() => { const out: { id: string; title: string }[] = []; const re = /<h2 id="([^"]+)">([^<]+)<\/h2>/g; let x; while ((x = re.exec(m.value?.body || ''))) out.push({ id: x[1], title: x[2] }); return out })
const body = ref<HTMLElement | null>(null)
const active = ref('')
const nav = ref<HTMLElement | null>(null)
onMounted(() => {
  const hs = Array.from(body.value?.querySelectorAll<HTMLElement>('h2[id]') || [])
  if (!hs.length) return
  active.value = hs[0]!.id
  const io = new IntersectionObserver((entries) => {
    for (const en of entries) if (en.isIntersecting) active.value = (en.target as HTMLElement).id
  }, { rootMargin: '-20% 0px -65% 0px', threshold: 0 })
  hs.forEach((h) => io.observe(h))
  onBeforeUnmount(() => io.disconnect())
})
watch(active, (id) => { const a = nav.value?.querySelector<HTMLElement>(`a[data-id="${id}"]`); a?.scrollIntoView?.({ block: 'nearest', inline: 'center', behavior: 'smooth' }) })
</script>
<template>
  <section v-if="m" class="section manual">
    <div class="container">
      <header class="mhead" v-reveal>
        <div class="mh-txt">
          <p v-if="data.title" class="eyebrow">{{ data.title }}</p>
          <h2>{{ m.title }}</h2>
          <p class="ver"><span class="pill">{{ t('common.version') }} {{ m.version }}</span><span class="pill soft">{{ t('common.updated') }} {{ m.effective_at }}</span></p>
          <a v-if="m.file" id="download" :href="mediaUrl(m.file)" class="btn btn-navy" target="_blank" rel="noopener"><Download :size="18" /> {{ t('common.download') }} PDF</a>
          <span v-else id="download" class="btn btn-ghost-dark soon"><Download :size="18" /> PDF · {{ t('common.soon') }}</span>
        </div>
        <div class="mh-art" aria-hidden="true">
          <img src="/media/renders/applicator-front.webp" alt="" width="1364" height="1600" loading="lazy" decoding="async" class="a">
          <img src="/media/renders/sensor-top.webp" alt="" width="1134" height="1080" loading="lazy" decoding="async" class="s">
        </div>
      </header>

      <div class="inner">
        <aside class="toc">
          <nav ref="nav" :aria-label="t('common.contents')">
            <a v-for="(s, i) in toc" :key="s.id" :href="'#' + s.id" :data-id="s.id" :class="{ on: active === s.id }"><span class="n">{{ String(i + 1).padStart(2, '0') }}</span><span class="t">{{ s.title.replace(/^\d+\.\s*/, '') }}</span></a>
          </nav>
        </aside>
        <article ref="body" class="prose body" v-html="m.body" />
      </div>
    </div>
  </section>
</template>
<style scoped>
.manual { padding-top: clamp(32px, 4vw, 64px); }
.mhead { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); align-items: center; gap: clamp(20px, 3vw, 48px); border-radius: var(--radius-lg); background: linear-gradient(120deg, #06264D 0%, #0E2659 55%, #1E4D9C 100%); color: var(--fg-on-dark); padding: clamp(28px, 4vw, 64px); margin-bottom: clamp(36px, 5vw, 80px); overflow: hidden; position: relative; }
.mhead .eyebrow { color: #8FB7FF; }
.mh-txt { display: grid; gap: 18px; justify-items: start; min-width: 0; } .mh-txt .btn { white-space: normal; max-width: 100%; }
.mh-txt h2 { color: #fff; }
.ver { display: flex; flex-wrap: wrap; gap: 8px; }
.pill { font-family: var(--mono); font-size: .72rem; letter-spacing: .1em; text-transform: uppercase; padding: 7px 12px; border-radius: 999px; background: rgba(255, 255, 255, .14); color: #fff; }
.pill.soft { background: rgba(255, 255, 255, .07); color: var(--muted-on-dark); }
.mhead .btn-navy { background: #fff; color: var(--ink); }
.soon { border-color: rgba(255, 255, 255, .35); color: #fff; opacity: .8; cursor: default; }
.mh-art { position: relative; aspect-ratio: 5 / 4; min-height: 200px; }
.mh-art img { position: absolute; height: auto; filter: drop-shadow(0 30px 40px rgba(0, 0, 0, .45)); }
.mh-art .a { width: 52%; left: 8%; top: -4%; transform: rotate(-8deg); }
.mh-art .s { width: 44%; right: 2%; bottom: 0; }
.inner { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.toc { position: sticky; top: calc(var(--header-h) + 24px); display: grid; gap: 18px; }
.toc nav { display: grid; gap: 2px; }
.toc nav a { display: grid; grid-template-columns: 30px 1fr; gap: 8px; align-items: baseline; padding: 10px 12px; border-radius: 12px; color: var(--ink-2); font-size: .95rem; line-height: 1.3; font-weight: 500; transition: background .25s, color .25s; border-left: 2px solid transparent; }
.toc nav a .n { font-family: var(--mono); font-size: .7rem; color: var(--muted); letter-spacing: .08em; }
.toc nav a:hover { color: var(--ink); background: var(--bg-alt); }
.toc nav a.on { color: var(--blue-d); background: var(--bg-alt); font-weight: 700; } .toc nav a.on .n { color: var(--blue-d); }
.body { max-width: 72ch; font-size: clamp(1.08rem, 1rem + .25vw, 1.25rem); line-height: 1.7; }
.body :deep(h2) { scroll-margin-top: calc(var(--header-h) + 24px); font-size: clamp(1.5rem, 1rem + 1.3vw, 2.2rem); margin: 2.4em 0 .7em; padding-top: 1.2em; border-top: 1px solid var(--line); }
.body :deep(h2:first-child) { margin-top: 0; padding-top: 0; border-top: 0; }
.body :deep(ol) { padding-left: 0; list-style: none; counter-reset: step; display: grid; gap: 14px; margin: 1.2em 0 1.6em; }
.body :deep(ol > li) { counter-increment: step; position: relative; padding: 18px 20px 18px 66px; border-radius: var(--radius-sm); background: var(--bg-alt); margin: 0; }
.body :deep(ol > li::before) { content: counter(step); position: absolute; left: 18px; top: 16px; width: 34px; height: 34px; border-radius: 50%; background: var(--ink); color: #fff; font-weight: 900; font-size: .9rem; display: grid; place-items: center; }
.body :deep(table) { font-size: .95em; }
.body :deep(em) { color: var(--muted); font-size: .9em; }
@media (max-width: 900px) {
  .mhead { grid-template-columns: 1fr; }
  .mh-art { aspect-ratio: 16 / 7; min-height: 160px; }
  .mh-art .a { width: 30%; left: 12%; top: -10%; } .mh-art .s { width: 32%; right: 12%; }
  .inner { grid-template-columns: 1fr; gap: 24px; }
  .toc { position: sticky; top: var(--header-h); z-index: 5; background: rgba(255, 255, 255, .92); backdrop-filter: blur(12px); margin-inline: calc(-1 * var(--gutter)); padding: 10px var(--gutter); border-bottom: 1px solid var(--line); gap: 0; }
  .toc nav { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; scroll-snap-type: x proximity; }
  .toc nav::-webkit-scrollbar { display: none; }
  .toc nav a { display: inline-flex; gap: 6px; flex: none; padding: 8px 12px; border: 1px solid var(--line); border-radius: 999px; font-size: .85rem; scroll-snap-align: center; border-left-width: 1px; }
  .toc nav a.on { background: var(--ink); color: #fff; border-color: var(--ink); } .toc nav a.on .n { color: #B9C6E2; }
  .body :deep(h2) { scroll-margin-top: calc(var(--header-h) + 72px); }
}
</style>
