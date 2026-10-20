<script setup lang="ts">
defineProps<{ eyebrow?: string; title?: string; text?: string; center?: boolean }>()
</script>
<template>
  <div class="section-head" :class="{ center }" v-reveal>
    <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>
    <h2 v-if="title">{{ title }}</h2>
    <p v-if="text" class="lead">{{ text }}</p>
    <slot />
  </div>
</template>
<style scoped>.center { text-align: center; margin-inline: auto; justify-items: center; } .center .eyebrow::before { display: none; }</style>
