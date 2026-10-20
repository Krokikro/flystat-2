<script setup lang="ts">
const props = defineProps<{ img?: { src?: string; alt?: string } | string | null; alt?: string; generated?: boolean; loading?: 'lazy' | 'eager'; sizes?: string }>()
const src = computed(() => mediaUrl(typeof props.img === 'string' ? props.img : props.img?.src))
const alt = computed(() => props.alt || (typeof props.img === 'object' ? props.img?.alt : '') || '')
const { t } = useI18n()
</script>
<template>
  <figure v-if="src" class="pic">
    <img :src="src" :alt="alt" :loading="loading || 'lazy'" decoding="async">
    <span v-if="generated" class="badge-gen" :title="t('common.generated_hint')">{{ t('common.generated') }}</span>
  </figure>
</template>
<style scoped>.pic { position: relative; margin: 0; } .pic img { width: 100%; height: 100%; object-fit: cover; }</style>
