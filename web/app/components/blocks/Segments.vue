<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
</script>
<template>
  <section class="section seg band-warm">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" />
      <div class="grid cols-4">
        <NuxtLink v-for="(it, i) in data.items" :key="i" :to="localePath(it.href || '/')" class="tile" v-reveal="i * 80">
          <UiPic :img="it.image" />
          <div class="ov"><h3>{{ it.title }}</h3><p>{{ it.text }}</p></div>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
<style scoped>
.tile { position: relative; border-radius: var(--radius-lg); overflow: hidden; aspect-ratio: 3/4; color: #fff; display: grid; align-items: end; isolation: isolate; }
.tile :deep(.pic) { position: absolute; inset: 0; } .tile :deep(img) { transition: transform 1s var(--ease); filter: saturate(1.05); } .tile:hover :deep(img) { transform: scale(1.05); }
.ov { position: relative; padding: 22px; background: linear-gradient(180deg, rgba(7,15,36,0) 0%, rgba(7,15,36,.85) 100%); display: grid; gap: 6px; padding-top: 80px; }
.ov h3 { color: #fff; } .ov p { font-size: 0.875rem; color: rgba(255,255,255,.85); }
</style>
