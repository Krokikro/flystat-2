<script setup lang="ts">
import type { NuxtError } from '#app'
const props = defineProps<{ error: NuxtError }>()
const { t } = useI18n(); const localePath = useLocalePath()
const is404 = computed(() => props.error?.statusCode === 404)
useSeoMeta({ title: () => (is404.value ? t('errors.404_title') : t('errors.500_title')), robots: 'noindex' })
</script>
<template>
  <NuxtLayout>
    <section class="err">
      <div class="container">
        <p class="mono code">{{ error?.statusCode || 500 }}</p>
        <h1>{{ is404 ? t('errors.404_title') : t('errors.500_title') }}</h1>
        <p class="lead">{{ is404 ? t('errors.404_text') : t('errors.500_text') }}</p>
        <button class="btn btn-primary" @click="clearError({ redirect: localePath('/') })">{{ t('errors.home') }}</button>
      </div>
    </section>
  </NuxtLayout>
</template>
<style scoped>
.err { min-height: 70vh; display: grid; align-items: center; padding-top: var(--header-h); }
.err .container { display: grid; gap: 16px; max-width: 640px; justify-items: start; }
.code { color: var(--muted); }
</style>
