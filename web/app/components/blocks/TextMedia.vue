<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section tm" :class="[`tone-${data.tone || 'light'}`, { left: data.media_side === 'left' }]">
    <div class="container inner">
      <div class="copy" v-reveal>
        <p v-if="data.eyebrow" class="eyebrow">{{ data.eyebrow }}</p>
        <h2>{{ data.title }}</h2>
        <div class="prose" v-html="data.text" />
        <UiCtaLink :cta="data.cta" :variant="data.tone === 'dark' ? 'btn-primary' : 'btn-navy'" />
      </div>
      <div class="media" v-reveal="120">
        <video v-if="data.video" :src="mediaUrl(data.video)" :poster="mediaUrl(data.image?.src)" autoplay muted loop playsinline />
        <UiPic v-else :img="data.image" :generated="data.generated_badge" />
      </div>
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: 1fr 1fr; gap: clamp(28px, 5vw, 80px); align-items: center; }
.left .copy { order: 2; } .left .media { order: 1; }
.copy { display: grid; gap: 18px; justify-items: start; }
.media :deep(.pic), .media video { border-radius: var(--radius-lg); overflow: hidden; width: 100%; }
.media :deep(img) { aspect-ratio: 4/3; object-fit: contain; }
.tone-light .media :deep(.pic) { background: radial-gradient(70% 70% at 50% 50%, #E9F0FF 0%, #F6F8FF 100%); }
.tone-warm { background: var(--warm-2); } .tone-warm .media :deep(img) { object-fit: cover; }
.tone-dark { background: var(--navy-800); color: var(--fg-on-dark); } .tone-dark .prose, .tone-dark .prose :deep(strong) { color: var(--muted-on-dark); } .tone-dark .prose :deep(strong) { color: #fff; } .tone-dark .eyebrow { color: #8FB7FF; }
.tone-dark .media :deep(.pic) { background: radial-gradient(60% 60% at 50% 45%, rgba(46,144,250,.35) 0%, transparent 70%); }
.tone-dark .media :deep(img) { filter: drop-shadow(0 40px 50px rgba(0,0,0,.5)); }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } .left .copy { order: 1; } .left .media { order: 2; } }
</style>
