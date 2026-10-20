<script setup lang="ts">
const route = useRoute(); const { locale, t } = useI18n(); const localePath = useLocalePath()
const { data: item, error } = await useAsyncData(`news-${locale.value}-${route.params.slug}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/news/${route.params.slug}`))
if (!item.value || error.value) throw createError({ statusCode: 404, statusMessage: 'Not found', fatal: true })
const og = mediaUrl(item.value.seo?.og_image || item.value.cover); const coverUrl = mediaUrl(item.value.cover)
useSeoMeta({ title: () => item.value.seo?.title || item.value.title, titleTemplate: (x) => `${x} — Flystat`, description: () => item.value.seo?.description || item.value.excerpt, ogImage: og, ogType: 'article', articlePublishedTime: () => item.value.published_at })
useHead({ script: () => [{ type: 'application/ld+json', innerHTML: JSON.stringify({ '@context': 'https://schema.org', '@type': 'NewsArticle', headline: item.value.title, datePublished: item.value.published_at, image: coverUrl, author: { '@type': 'Organization', name: item.value.author || 'Flystat' } }) }] })
const date = computed(() => item.value.published_at ? new Intl.DateTimeFormat(locale.value, { dateStyle: 'long' }).format(new Date(item.value.published_at)) : '')
const share = () => { if (navigator.share) navigator.share({ title: item.value.title, url: location.href }).catch(() => {}); else navigator.clipboard?.writeText(location.href) }
</script>
<template>
  <article class="art">
    <div class="container">
      <NuxtLink :to="localePath('/news')" class="back">← {{ t('news.back') }}</NuxtLink>
      <p class="meta mono">{{ t('news.' + item.category) || item.category }} · {{ date }}</p>
      <h1>{{ item.title }}</h1>
      <p v-if="item.excerpt" class="lead">{{ item.excerpt }}</p>
      <figure v-if="item.cover" class="cover"><img :src="mediaUrl(item.cover)" :alt="item.title" width="1600" height="900" loading="eager" fetchpriority="high"></figure>
      <div class="prose body" v-html="item.body" />
      <div class="actions"><button class="btn btn-ghost-dark" @click="share">{{ t('news.share') }}</button></div>
      <section v-if="item.neighbors?.length" class="related">
        <h2>{{ t('news.related') }}</h2>
        <div class="grid cols-3"><NewsCard v-for="n in item.neighbors" :key="n.slug" :item="n" /></div>
      </section>
    </div>
  </article>
</template>
<style scoped>
.art { padding-top: calc(var(--header-h) + 32px); padding-bottom: 80px; }
.art .container { max-width: 880px; display: grid; gap: 18px; }
.back { color: var(--muted); font-weight: 600; font-size: 14px; }
.meta { color: var(--muted); }
.cover img { border-radius: var(--radius-lg); width: 100%; aspect-ratio: 16/9; object-fit: cover; }
.body { margin-top: 10px; }
.actions { display: flex; gap: 10px; }
.related { margin-top: 48px; display: grid; gap: 20px; }
</style>
