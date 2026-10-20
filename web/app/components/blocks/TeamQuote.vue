<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section"><div class="container"><blockquote class="q" v-reveal><UiPic v-if="data.image?.src" :img="data.image" class="ava" /><p>«{{ data.text }}»</p><footer><b>{{ data.author }}</b><span>{{ data.role }}</span></footer></blockquote></div></section>
</template>
<style scoped>
.q { margin: 0; display: grid; gap: 18px; max-width: 820px; font-size: clamp(20px, 2.4vw, 30px); font-weight: 500; letter-spacing: -.01em; line-height: 1.35; }
.ava { width: 64px; height: 64px; border-radius: 50%; overflow: hidden; }
footer { font-size: 15px; display: grid; } footer span { color: var(--muted); }
</style>
