<script setup lang="ts">
const { data: boot } = await useBootstrap()
provide('boot', boot)
const route = useRoute()
const isDemo = computed(() => boot.value?.settings?.site_mode !== 'published')
useHead({ meta: () => isDemo.value ? [{ name: 'robots', content: 'noindex, nofollow' }] : [] })
</script>
<template>
  <div class="site">
    <a class="skip" href="#main">{{ $t('nav.skip') }}</a>
    <main id="main" :key="route.fullPath">
      <slot />
    </main>
    <SiteHeader :boot="boot" />
    <SiteFooter :boot="boot" />
    <CookieBanner />
    <div v-if="isDemo" class="demo-badge mono">{{ $t('common.demo_badge') }}</div>
  </div>
</template>
<style>
.skip { position: absolute; left: -999px; top: 8px; background: var(--ink); color: #fff; padding: 10px 14px; border-radius: 10px; z-index: 100; }
.skip:focus { left: 12px; }
.demo-badge { position: fixed; left: 12px; bottom: 12px; z-index: 60; background: rgba(16,42,86,.85); color: #fff; padding: 6px 10px; border-radius: 8px; font-size: 10px; pointer-events: none; }
</style>
