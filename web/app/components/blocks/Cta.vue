<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="cta-band" :class="`tone-${data.tone || 'dark'}`">
    <div class="container inner" v-reveal>
      <div class="copy"><h2>{{ data.title }}</h2><p v-if="data.text" class="lead">{{ data.text }}</p></div>
      <div class="acts"><UiCtaLink :cta="data.cta" :variant="data.tone === 'light' ? 'btn-navy' : data.tone === 'purple' ? 'btn-white' : 'btn-primary'" /><UiCtaLink :cta="data.cta2" :variant="data.tone === 'light' ? 'btn-ghost-dark' : 'btn-ghost'" /></div>
    </div>
  </section>
</template>
<style scoped>
.cta-band { padding-block: clamp(48px, 7vw, 96px); }
.tone-dark { background: radial-gradient(70% 120% at 80% 50%, #1E4D9C 0%, var(--navy-800) 60%); color: #fff; } .tone-dark .lead { color: var(--muted-on-dark); }
.tone-purple { background: linear-gradient(120deg, #5925DC 0%, #7A5AF8 60%, #9C84FF 100%); color: #fff; } .tone-purple .lead { color: rgba(255,255,255,.82); }
.tone-light { background: var(--bg-alt); }
.inner { display: grid; grid-template-columns: 1.4fr 1fr; gap: 32px; align-items: center; }
.copy { display: grid; gap: 12px; }
.acts { display: flex; gap: 10px; flex-wrap: wrap; justify-content: end; }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } .acts { justify-content: start; } }
</style>
