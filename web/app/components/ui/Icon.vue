<script setup lang="ts">
import * as icons from '@lucide/vue'
const props = withDefaults(defineProps<{ name?: string | null; size?: number }>(), { size: 22 })
const comp = computed(() => {
  if (!props.name) return (icons as any).Circle
  const pascal = props.name.split('-').map((s) => s.charAt(0).toUpperCase() + s.slice(1)).join('')
  return (icons as any)[pascal] || (icons as any)[pascal + 'Icon'] || (icons as any).Circle
})
</script>
<template><component :is="comp" :size="size" :stroke-width="1.75" aria-hidden="true" /></template>
