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
.demo-badge { position: fixed; left: 14px; bottom: max(14px, env(safe-area-inset-bottom)); z-index: 60; display: inline-flex; align-items: center; gap: 8px; background: rgba(7, 15, 36, .78); color: #fff; padding: 7px 12px; border-radius: 999px; font-size: 0.65rem; letter-spacing: .12em; pointer-events: none; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, .12); }
.site:has(.demo-badge) .cb { bottom: calc(max(14px, env(safe-area-inset-bottom)) + 44px); }
.demo-badge::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--warn); box-shadow: 0 0 0 3px rgba(247, 144, 9, .25); }
</style>
