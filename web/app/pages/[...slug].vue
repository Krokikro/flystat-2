<script setup lang="ts">
import type { Block } from '~/utils/types'
const route = useRoute()
const slug = computed(() => (Array.isArray(route.params.slug) ? route.params.slug.join('/') : String(route.params.slug || '')))
const { data: page, error } = await usePage(slug.value)
if (page.value?.redirect) {
  const localePath = useLocalePath()
  await navigateTo(localePath(page.value.redirect), { redirectCode: page.value.code || 301, external: /^https?:/.test(page.value.redirect) })
}
if (!page.value || error.value) throw createError({ statusCode: 404, statusMessage: 'Page not found', fatal: true })

const { t } = useI18n()
const siteUrl = useRuntimeConfig().public.siteUrl
const seo = computed(() => page.value?.seo || {})
const ogImg = computed(() => mediaUrl(seo.value.og_image) || `${siteUrl}/og-default.jpg`)
useSeoMeta({
  title: () => seo.value.title || page.value?.title,
  titleTemplate: (t0) => (t0 ? `${t0} — Flystat` : 'Flystat'),
  description: () => seo.value.description || t('seo.default_description'),
  ogTitle: () => seo.value.title || page.value?.title,
  ogDescription: () => seo.value.description || t('seo.default_description'),
  ogImage: () => ogImg.value,
  ogType: 'website', twitterCard: 'summary_large_image',
  robots: () => (seo.value.noindex ? 'noindex, nofollow' : undefined),
})
const firstDark = computed(() => page.value?.blocks?.[0]?.type === 'hero_dark')
useHead({ bodyAttrs: { class: () => (firstDark.value ? 'has-dark-hero' : '') } })

const components: Record<string, any> = {
  hero_dark: resolveComponent('BlocksHeroDark'), hero_light: resolveComponent('BlocksHeroLight'), stats: resolveComponent('BlocksStats'),
  how_it_works: resolveComponent('BlocksHowItWorks'), text_media: resolveComponent('BlocksTextMedia'), cards: resolveComponent('BlocksCards'),
  steps: resolveComponent('BlocksSteps'), spec_table: resolveComponent('BlocksSpecTable'), comparison: resolveComponent('BlocksComparison'),
  faq: resolveComponent('BlocksFaq'), form: resolveComponent('BlocksLeadForm'), news: resolveComponent('BlocksNewsList'), map: resolveComponent('BlocksWorldMap'),
  cta: resolveComponent('BlocksCta'), app_screens: resolveComponent('BlocksAppScreens'), segments: resolveComponent('BlocksSegments'), timeline: resolveComponent('BlocksTimeline'),
  manual_sections: resolveComponent('BlocksManualSections'), account_link: resolveComponent('BlocksAccountLink'), documents: resolveComponent('BlocksDocuments'),
  contacts: resolveComponent('BlocksContacts'), team_quote: resolveComponent('BlocksTeamQuote'), video: resolveComponent('BlocksVideoBlock'),
}
// JSON-LD: organisation + FAQ when present
const faqBlocks = computed(() => (page.value?.blocks || []).filter((b: Block) => b.type === 'faq' && b.data.items?.length))
useHead({ script: () => [
  { type: 'application/ld+json', innerHTML: JSON.stringify({ '@context': 'https://schema.org', '@type': 'Organization', name: 'Flystat', url: siteUrl, logo: `${siteUrl}/favicon.svg` }) },
  ...faqBlocks.value.map((b: Block) => ({ type: 'application/ld+json', innerHTML: JSON.stringify({ '@context': 'https://schema.org', '@type': 'FAQPage', mainEntity: b.data.items.map((i: any) => ({ '@type': 'Question', name: i.q, acceptedAnswer: { '@type': 'Answer', text: i.a } })) }) })),
] })
</script>
<template>
  <div :class="['page', `tpl-${page?.template}`]">
    <component :is="components[b.type] || 'div'" v-for="b in page?.blocks" :id="b.anchor || undefined" :key="b.id" :data="b.data" :page="page" />
    <template v-if="page?.template === 'news'"><NewsIndex /></template>
  </div>
</template>
