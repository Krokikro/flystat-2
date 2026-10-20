<script setup lang="ts">
/** Section heading: eyebrow / title / lead, each revealing with its own delay. `size="lg"` for the display headline of a major band. */
defineProps<{ eyebrow?: string; title?: string; text?: string; center?: boolean; size?: 'md' | 'lg'; tight?: boolean }>()
</script>
<template>
  <div class="section-head sh" :class="[{ center, tight }, `sz-${size || 'md'}`]">
    <p v-if="eyebrow" class="eyebrow" v-reveal>{{ eyebrow }}</p>
    <h2 v-if="title" v-reveal="80">{{ title }}</h2>
    <p v-if="text" class="lead" v-reveal="160">{{ text }}</p>
    <slot />
  </div>
</template>
<style scoped>
.sh { gap: clamp(14px, 1.4vw, 22px); max-width: 880px; }
.sh.tight { margin-bottom: clamp(20px, 2.5vw, 36px); }
.sz-lg { max-width: 1000px; } .sz-lg h2 { font-size: clamp(2.2rem, 1rem + 3.2vw, 4.4rem); letter-spacing: -.03em; line-height: 1.02; }
.sz-lg .lead { font-size: clamp(1.1rem, 1rem + .5vw, 1.5rem); max-width: 62ch; }
.center { text-align: center; margin-inline: auto; justify-items: center; } .center .eyebrow::before { display: none; }
.center .lead { margin-inline: auto; }
</style>
