<script setup lang="ts">
const { locale } = useI18n()
const head = useLocaleHead({ dir: true, seo: true })
useHead({ htmlAttrs: { lang: head.value.htmlAttrs?.lang || locale.value, dir: head.value.htmlAttrs?.dir }, link: () => head.value.link || [], meta: () => head.value.meta || [] })
</script>
<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>
