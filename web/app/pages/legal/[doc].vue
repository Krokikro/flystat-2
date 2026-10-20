<script setup lang="ts">
import { Download, Scale } from '@lucide/vue'
const route = useRoute(); const { locale, t } = useI18n(); const localePath = useLocalePath()
const boot = inject<any>('boot', null)
const { data: doc, error } = await useAsyncData(`doc-${locale.value}-${route.params.doc}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/documents/${route.params.doc}`))
if (!doc.value || error.value) throw createError({ statusCode: 404, statusMessage: 'Not found', fatal: true })
useSeoMeta({ title: () => doc.value.title, titleTemplate: (x) => `${x} — Flystat`, robots: 'noindex, follow' })
// give h2s ids so the side nav can jump to them
const parsed = computed(() => {
  const toc: { id: string; title: string }[] = []; let n = 0
  const html = String(doc.value?.body || '').replace(/<h2(?![^>]*\bid=)([^>]*)>([\s\S]*?)<\/h2>/g, (_m, attrs, inner) => { const id = `s-${++n}`; toc.push({ id, title: String(inner).replace(/<[^>]+>/g, '') }); return `<h2 id="${id}"${attrs}>${inner}</h2>` })
  return { html, toc }
})
const toc = computed(() => parsed.value.toc); const body = computed(() => parsed.value.html)
const others = computed(() => (boot?.value?.legal || []) as { slug: string; title: string }[])
</script>
<template>
  <article class="legal">
    <div class="container">
      <header class="head" v-reveal>
        <p class="eyebrow">{{ t('footer.legal') }}</p>
        <h1>{{ doc.title }}</h1>
        <p class="meta"><span v-if="doc.version" class="pill mono">{{ t('common.version') }} {{ doc.version }}</span><span class="pill mono soft">{{ t('common.updated') }} {{ doc.effective_at }}</span></p>
      </header>
      <div class="inner">
        <aside class="side">
          <nav v-if="others.length" class="docs" :aria-label="t('footer.legal')">
            <span class="h"><Scale :size="14" /> {{ t('footer.legal') }}</span>
            <NuxtLink v-for="d in others" :key="d.slug" :to="localePath('/legal/' + d.slug)" :class="{ on: d.slug === route.params.doc }">{{ d.title }}</NuxtLink>
          </nav>
          <nav v-if="toc.length > 2" class="toc" :aria-label="t('common.contents')">
            <span class="h">{{ t('common.contents') }}</span>
            <a v-for="s in toc" :key="s.id" :href="'#' + s.id">{{ s.title }}</a>
          </nav>
        </aside>
        <div class="main">
          <div class="prose body" v-html="body" />
          <a v-if="doc.file" :href="mediaUrl(doc.file)" class="btn btn-ghost-dark dl" target="_blank" rel="noopener"><Download :size="18" /> {{ t('common.download') }} PDF</a>
        </div>
      </div>
    </div>
  </article>
</template>
<style scoped>
.legal { padding-top: calc(var(--header-h) + clamp(24px, 4vw, 64px)); padding-bottom: clamp(64px, 8vw, 120px); }
.head { display: grid; gap: 16px; max-width: 900px; margin-bottom: clamp(32px, 5vw, 72px); justify-items: start; }
h1 { font-size: clamp(2rem, 1rem + 2.8vw, 3.8rem); }
.meta { display: flex; flex-wrap: wrap; gap: 8px; }
.pill { font-size: .72rem; padding: 7px 12px; border-radius: 999px; background: var(--bg-alt); color: var(--ink); } .pill.soft { color: var(--muted); }
.inner { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.side { position: sticky; top: calc(var(--header-h) + 24px); display: grid; gap: 28px; }
.h { display: inline-flex; align-items: center; gap: 6px; font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; color: var(--muted); margin-bottom: 8px; }
.docs, .toc { display: grid; gap: 2px; }
.docs a, .toc a { padding: 9px 12px; border-radius: 10px; color: var(--ink-2); font-size: .95rem; font-weight: 500; line-height: 1.3; transition: background .2s, color .2s; }
.docs a:hover, .toc a:hover { background: var(--bg-alt); color: var(--ink); }
.docs a.on { background: var(--ink); color: #fff; font-weight: 700; }
.body { font-size: clamp(1.05rem, 1rem + .25vw, 1.2rem); line-height: 1.7; max-width: 72ch; }
.body :deep(h2) { scroll-margin-top: calc(var(--header-h) + 24px); font-size: clamp(1.35rem, 1rem + .9vw, 1.9rem); }
.dl { margin-top: 32px; }
@media (max-width: 900px) { .inner { grid-template-columns: 1fr; } .side { position: static; } .toc { display: none; } .docs { display: flex; flex-wrap: wrap; gap: 6px; } .docs .h { width: 100%; } .docs a { border: 1px solid var(--line); border-radius: 999px; font-size: .85rem; padding: 7px 12px; } }
</style>
