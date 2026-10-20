<script setup lang="ts">
import type { NuxtError } from '#app'
import { Home } from '@lucide/vue'
const props = defineProps<{ error: NuxtError }>()
const { t } = useI18n(); const localePath = useLocalePath()
const is404 = computed(() => props.error?.statusCode === 404)
useSeoMeta({ title: () => (is404.value ? t('errors.404_title') : t('errors.500_title')), robots: 'noindex' })
</script>
<template>
  <NuxtLayout>
    <section class="err">
      <div class="container">
        <div class="box">
          <p class="code" aria-hidden="true">{{ error?.statusCode || 500 }}</p>
          <h1>{{ is404 ? t('errors.404_title') : t('errors.500_title') }}</h1>
          <p class="lead">{{ is404 ? t('errors.404_text') : t('errors.500_text') }}</p>
          <div class="acts">
            <button class="btn btn-primary" @click="clearError({ redirect: localePath('/') })"><Home :size="18" /> {{ t('errors.home') }}</button>
          </div>
        </div>
        <div class="art" aria-hidden="true"><img src="/media/renders/applicator-top.webp" alt="" width="1033" height="1355" loading="eager" decoding="async"></div>
      </div>
    </section>
  </NuxtLayout>
</template>
<style scoped>
.err { min-height: 100svh; display: grid; align-items: center; padding-top: var(--header-h); background: radial-gradient(60% 60% at 90% 20%, #EAF2FF 0%, transparent 60%), #fff; overflow: hidden; }
.err .container { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: clamp(24px, 4vw, 72px); align-items: center; padding-block: clamp(40px, 6vw, 96px); }
.box { display: grid; gap: 16px; max-width: 620px; justify-items: start; }
.code { font-size: clamp(5rem, 3rem + 10vw, 12rem); font-weight: 900; line-height: .9; letter-spacing: -.05em; color: transparent; -webkit-text-stroke: 2px var(--line); background: linear-gradient(120deg, var(--blue), var(--purple)); -webkit-background-clip: text; background-clip: text; opacity: .9; }
.acts { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; max-width: 100%; } .acts .btn { white-space: normal; text-align: center; }
.art { justify-self: center; width: min(100%, 420px); }
.art img { width: 100%; filter: drop-shadow(0 40px 50px rgba(16, 42, 86, .25)); transform: rotate(14deg); }
@media (max-width: 860px) { .err .container { grid-template-columns: 1fr; } .art { width: min(60%, 260px); order: -1; } .code { -webkit-text-stroke: 1px var(--line); } }
</style>
