<script setup lang="ts">
const props = defineProps<{ cta?: { label?: string; href?: string } | null; variant?: string }>()
const localePath = useLocalePath(); const route = useRoute()
const href = computed(() => {
  const h = props.cta?.href || ''
  if (!h) return ''
  if (h.startsWith('#')) return route.path + h
  if (/^https?:/.test(h)) return h
  const [path, hash] = h.split('#')
  return localePath(path || '/') + (hash ? '#' + hash : '')
})
const external = computed(() => /^https?:/.test(props.cta?.href || ''))
</script>
<template>
  <a v-if="cta?.label && external" :href="href" class="btn" :class="variant || 'btn-primary'" target="_blank" rel="noopener">{{ cta.label }}</a>
  <NuxtLink v-else-if="cta?.label" :to="href" class="btn" :class="variant || 'btn-primary'">{{ cta.label }}</NuxtLink>
</template>
