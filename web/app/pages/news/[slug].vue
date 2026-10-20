<script setup lang="ts">
import { ArrowLeft, Share2, Link2, Check } from '@lucide/vue'
const route = useRoute(); const { locale, t } = useI18n(); const localePath = useLocalePath()
const { data: item, error } = await useAsyncData(`news-${locale.value}-${route.params.slug}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/news/${route.params.slug}`))
if (!item.value || error.value) throw createError({ statusCode: 404, statusMessage: 'Not found', fatal: true })
const og = mediaUrl(item.value.seo?.og_image || item.value.cover); const coverUrl = mediaUrl(item.value.cover)
useSeoMeta({ title: () => item.value.seo?.title || item.value.title, titleTemplate: (x) => `${x} — Flystat`, description: () => item.value.seo?.description || item.value.excerpt, ogImage: og, ogType: 'article', articlePublishedTime: () => item.value.published_at })
useHead({ script: () => [{ type: 'application/ld+json', innerHTML: JSON.stringify({ '@context': 'https://schema.org', '@type': 'NewsArticle', headline: item.value.title, datePublished: item.value.published_at, image: coverUrl, author: { '@type': 'Organization', name: item.value.author || 'Flystat' } }) }] })
const date = computed(() => item.value.published_at ? new Intl.DateTimeFormat(locale.value, { dateStyle: 'long' }).format(new Date(item.value.published_at)) : '')
const copied = ref(false)
const share = async () => {
  try {
    if (navigator.share) await navigator.share({ title: item.value.title, url: location.href })
    else { await navigator.clipboard?.writeText(location.href); copied.value = true; setTimeout(() => (copied.value = false), 1600) }
  } catch {}
}
const copyLink = async () => { try { await navigator.clipboard?.writeText(location.href); copied.value = true; setTimeout(() => (copied.value = false), 1600) } catch {} }
// reading progress (transform only)
const progress = ref(0); const body = ref<HTMLElement | null>(null)
onMounted(() => {
  let raf = 0
  const upd = () => { raf = 0; const el = body.value; if (!el) return; const r = el.getBoundingClientRect(); const total = r.height - window.innerHeight * 0.6; progress.value = total > 0 ? Math.max(0, Math.min(1, -r.top / total + 0.15)) : 1 }
  const onS = () => { if (!raf) raf = requestAnimationFrame(upd) }
  window.addEventListener('scroll', onS, { passive: true }); upd()
  onBeforeUnmount(() => { window.removeEventListener('scroll', onS); if (raf) cancelAnimationFrame(raf) })
})
</script>
<template>
  <article class="art">
    <span class="progress" aria-hidden="true" :style="{ transform: `scaleX(${progress})` }" />
    <header class="hero">
      <div class="container narrow">
        <NuxtLink :to="localePath('/news')" class="back"><ArrowLeft :size="16" /> {{ t('news.back') }}</NuxtLink>
        <p class="meta"><span class="cat">{{ t('news.' + item.category) || item.category }}</span><span>{{ date }}</span></p>
        <h1>{{ item.title }}</h1>
        <p v-if="item.excerpt" class="lead">{{ item.excerpt }}</p>
      </div>
      <div v-if="item.cover" class="container wide">
        <figure class="cover" v-reveal><img :src="mediaUrl(item.cover)" :alt="item.title" width="1600" height="900" loading="eager" fetchpriority="high"></figure>
      </div>
    </header>

    <div class="container narrow">
      <div ref="body" class="prose body" v-html="item.body" />
      <div class="actions">
        <button class="btn btn-navy" @click="share"><Share2 :size="18" /> {{ t('news.share') }}</button>
        <button class="btn btn-ghost-dark" :class="{ ok: copied }" @click="copyLink"><Check v-if="copied" :size="18" /><Link2 v-else :size="18" /> {{ copied ? '✓' : 'URL' }}</button>
        <NuxtLink :to="localePath('/news')" class="back end"><ArrowLeft :size="16" /> {{ t('news.back') }}</NuxtLink>
      </div>
    </div>

    <section v-if="item.neighbors?.length" class="related band-alt">
      <div class="container">
        <h2>{{ t('news.related') }}</h2>
        <div class="grid cols-3"><NewsCard v-for="n in item.neighbors" :key="n.slug" :item="n" /></div>
      </div>
    </section>
  </article>
</template>
<style scoped>
.art { padding-top: calc(var(--header-h) + clamp(24px, 4vw, 64px)); }
.progress { position: fixed; left: 0; top: 0; height: 3px; width: 100%; background: linear-gradient(90deg, var(--blue), var(--purple)); transform-origin: left; z-index: 55; pointer-events: none; }
.narrow { max-width: 860px; }
.wide { max-width: 1240px; }
.hero .narrow { display: grid; gap: 18px; justify-items: start; margin-bottom: clamp(28px, 4vw, 56px); }
.back { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-weight: 600; font-size: .95rem; transition: color .2s; } .back:hover { color: var(--ink); }
.meta { display: flex; flex-wrap: wrap; gap: 8px 14px; align-items: center; color: var(--muted); font-size: .9rem; }
.cat { padding: 6px 12px; border-radius: 999px; background: var(--bg-alt); color: var(--blue-d); font-weight: 700; font-size: .8rem; letter-spacing: .04em; text-transform: uppercase; }
h1 { font-size: clamp(2rem, 1rem + 2.8vw, 3.8rem); max-width: 22ch; }
.cover { margin: 0; border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 40px 80px rgba(16, 42, 86, .16); }
.cover img { width: 100%; aspect-ratio: 16 / 9; object-fit: cover; }
.body { margin-top: clamp(36px, 5vw, 72px); font-size: clamp(1.1rem, 1rem + .3vw, 1.3rem); line-height: 1.7; max-width: 72ch; }
.body :deep(p:first-of-type) { font-size: 1.1em; color: var(--ink); }
.body :deep(img) { width: 100%; height: auto; box-shadow: var(--shadow); }
.actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: clamp(32px, 4vw, 56px); padding-top: 24px; border-top: 1px solid var(--line); }
.actions .ok { border-color: var(--ok); color: var(--ok); }
.end { margin-left: auto; }
.related { margin-top: clamp(48px, 7vw, 110px); padding-block: clamp(48px, 6vw, 96px); display: grid; gap: 20px; }
.related .container { display: grid; gap: clamp(20px, 3vw, 40px); }
.related h2 { font-size: clamp(1.6rem, 1rem + 1.4vw, 2.4rem); }
</style>
