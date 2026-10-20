<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="hero-l">
    <div class="container inner">
      <div class="copy" v-reveal>
        <p v-if="data.eyebrow" class="eyebrow">{{ data.eyebrow }}</p>
        <h1>{{ data.title }}</h1>
        <p v-if="data.text" class="lead">{{ data.text }}</p>
        <div class="ctas"><UiCtaLink :cta="data.cta" variant="btn-navy" /><UiCtaLink :cta="data.cta2" variant="btn-ghost-dark" /></div>
        <div v-if="data.stats?.length" class="stats">
          <div v-for="(s, i) in data.stats" :key="i"><b>{{ s.value }}</b><span>{{ s.label }}</span></div>
        </div>
      </div>
      <div v-if="data.image?.src" class="art" v-reveal="120"><UiPic :img="data.image" loading="eager" /></div>
    </div>
  </section>
</template>
<style scoped>
.hero-l { padding-top: calc(var(--header-h) + clamp(32px, 6vw, 80px)); padding-bottom: clamp(40px, 6vw, 80px); background: linear-gradient(180deg, #F6F8FF 0%, #fff 100%); }
.inner { display: grid; grid-template-columns: 1.1fr .9fr; gap: clamp(24px, 4vw, 64px); align-items: center; }
.copy { display: grid; gap: 20px; }
.ctas { display: flex; flex-wrap: wrap; gap: 10px; }
.stats { display: flex; gap: 28px; flex-wrap: wrap; margin-top: 12px; padding-top: 20px; border-top: 1px solid var(--line); }
.stats div { display: grid; gap: 2px; } .stats b { font-size: 1.625rem; font-weight: 900; letter-spacing: -.02em; font-variant-numeric: tabular-nums; } .stats span { font-size: 0.8125rem; color: var(--muted); }
.art :deep(img) { max-height: 560px; width: auto; margin-inline: auto; object-fit: contain; filter: drop-shadow(0 30px 40px rgba(16,42,86,.18)); }
.art :deep(.pic) { background: radial-gradient(60% 60% at 50% 50%, #E9F0FF 0%, transparent 70%); border-radius: var(--radius-lg); }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } .art :deep(img) { max-height: 360px; } }
</style>
