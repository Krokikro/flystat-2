<script setup lang="ts">
const route = useRoute(); const { locale, t } = useI18n()
const { data: doc, error } = await useAsyncData(`doc-${locale.value}-${route.params.doc}`, () => $fetch<any>(`${apiBase()}/api/v2/${locale.value}/documents/${route.params.doc}`))
if (!doc.value || error.value) throw createError({ statusCode: 404, statusMessage: 'Not found', fatal: true })
useSeoMeta({ title: () => doc.value.title, titleTemplate: (x) => `${x} — Flystat`, robots: 'noindex, follow' })
</script>
<template>
  <article class="legal">
    <div class="container">
      <p class="eyebrow">{{ t('footer.legal') }}</p>
      <h1>{{ doc.title }}</h1>
      <p class="meta mono"><template v-if="doc.version">{{ t('common.version') }} {{ doc.version }} · </template>{{ t('common.updated') }} {{ doc.effective_at }}</p>
      <div class="prose" v-html="doc.body" />
      <a v-if="doc.file" :href="mediaUrl(doc.file)" class="btn btn-ghost-dark" target="_blank" rel="noopener">{{ t('common.download') }} PDF</a>
    </div>
  </article>
</template>
<style scoped>
.legal { padding-top: calc(var(--header-h) + 32px); padding-bottom: 80px; }
.legal .container { max-width: 820px; display: grid; gap: 16px; }
.meta { color: var(--muted); }
</style>
