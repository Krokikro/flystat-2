<script setup lang="ts">
const route = useRoute(); const { t } = useI18n(); const localePath = useLocalePath()
const key = ['waitlist', 'distribution', 'support'].includes(String(route.params.form)) ? String(route.params.form) : 'default'
useSeoMeta({ title: () => t('thanks.title'), robots: 'noindex, nofollow' })
</script>
<template>
  <section class="thanks">
    <div class="container">
      <div class="box">
        <p class="eyebrow">Flystat</p>
        <h1>{{ t('thanks.title') }}</h1>
        <p class="lead">{{ t('thanks.' + key) }}</p>
        <div class="acts"><NuxtLink :to="localePath('/')" class="btn btn-primary">{{ t('thanks.back') }}</NuxtLink><NuxtLink :to="localePath('/device')" class="btn btn-ghost-dark">{{ t('thanks.meanwhile') }}: {{ t('common.learn_more') }}</NuxtLink></div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.thanks { min-height: 70vh; display: grid; place-items: center; padding-top: var(--header-h); }
.box { display: grid; gap: 18px; max-width: 640px; padding: 40px 0; }
.acts { display: flex; gap: 10px; flex-wrap: wrap; }
</style>
