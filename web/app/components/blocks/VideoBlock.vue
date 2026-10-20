<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const { t } = useI18n(); const v = ref<HTMLVideoElement | null>(null); const playing = ref(false)
const toggle = () => { if (!v.value) return; v.value.paused ? v.value.play() : v.value.pause(); playing.value = !v.value.paused }
</script>
<template>
  <section class="section vb band-dark">
    <div class="container">
      <UiSectionHead :title="data.title" />
      <div class="wrap" v-reveal>
        <video ref="v" :src="mediaUrl(data.video)" :poster="mediaUrl(data.poster?.src)" playsinline preload="metadata" @ended="playing = false" @click="toggle" />
        <button v-if="!playing" class="play" :aria-label="t('common.play')" @click="toggle"><span /></button>
        <span v-if="data.generated_badge" class="badge-gen">{{ t('common.generated') }}</span>
      </div>
      <p v-if="data.caption" class="cap">{{ data.caption }}</p>
    </div>
  </section>
</template>
<style scoped>
.wrap { position: relative; border-radius: var(--radius-lg); overflow: hidden; aspect-ratio: 16/9; background: #06264D; }
.wrap video { width: 100%; height: 100%; object-fit: cover; cursor: pointer; }
.play { position: absolute; inset: 0; display: grid; place-items: center; } .play span { width: 84px; height: 84px; border-radius: 50%; background: rgba(255,255,255,.92); display: grid; place-items: center; box-shadow: var(--shadow-dark); } .play span::after { content: ""; border-left: 26px solid var(--ink); border-top: 15px solid transparent; border-bottom: 15px solid transparent; margin-left: 6px; }
.cap { margin-top: 14px; font-size: 13.5px; color: var(--muted-on-dark); max-width: 70ch; }
</style>
