<script setup lang="ts">
/** Cinematic 16:9 video on a navy band. Data: { title, video, poster{src,alt}, caption, generated_badge? }. */
import { Play, Pause } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const { t } = useI18n()
const v = ref<HTMLVideoElement | null>(null)
const playing = ref(false); const started = ref(false)
const FALLBACK = '/media/hero/poster.webp'
const poster = ref(mediaUrl(props.data.poster?.src) || FALLBACK)
onMounted(() => {
  // the poster frame may not exist on disk yet (API points at a frame sequence) — fall back to the hero poster
  if (poster.value !== FALLBACK) { const im = new Image(); im.onerror = () => (poster.value = FALLBACK); im.src = poster.value }
})
const toggle = () => { const el = v.value; if (!el) return; if (el.paused) { el.play().catch(() => {}); started.value = true } else el.pause() }
</script>
<template>
  <section class="section vb band-dark">
    <div class="container">
      <UiSectionHead :title="data.title" :text="data.text" />
    </div>
    <div class="stage">
      <div class="frame" :class="{ playing }" v-reveal>
        <video ref="v" :src="mediaUrl(data.video)" :poster="poster" playsinline preload="metadata" :controls="started" @play="playing = true" @pause="playing = false" @ended="playing = false; started = false" @click="!started && toggle()" />
        <button class="play" :class="{ hide: started }" :aria-label="playing ? t('common.pause') : t('common.play')" @click="toggle">
          <span class="ring" /><span class="btn-in"><Pause v-if="playing" :size="30" /><Play v-else :size="30" class="tri" /></span>
        </button>
        <span v-if="data.generated_badge" class="badge-gen">{{ t('common.generated') }}</span>
      </div>
    </div>
    <div class="container"><p v-if="data.caption" class="cap">{{ data.caption }}</p></div>
  </section>
</template>
<style scoped>
.vb { position: relative; overflow: hidden; background: radial-gradient(90% 60% at 50% 100%, #0E2659 0%, var(--navy-800) 60%, var(--navy-900) 100%); }
.stage { max-width: min(1600px, 100%); margin-inline: auto; padding-inline: var(--gutter); }
.frame { position: relative; aspect-ratio: 16 / 9; border-radius: var(--radius-lg); overflow: hidden; background: #040C1E; box-shadow: 0 60px 120px rgba(0, 0, 0, .55), 0 0 0 1px rgba(255, 255, 255, .08); }
.frame::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(6, 38, 77, .25), transparent 30%, transparent 70%, rgba(4, 12, 30, .55)); pointer-events: none; transition: opacity .6s; }
.frame.playing::after { opacity: 0; }
video { width: 100%; height: 100%; object-fit: cover; display: block; cursor: pointer; }
.play { position: absolute; inset: 0; display: grid; place-items: center; transition: opacity .4s; }
.play.hide { opacity: 0; pointer-events: none; }
.btn-in { position: relative; width: clamp(76px, 8vw, 112px); height: clamp(76px, 8vw, 112px); border-radius: 50%; background: rgba(255, 255, 255, .94); color: var(--ink); display: grid; place-items: center; box-shadow: 0 30px 60px rgba(0, 0, 0, .45); transition: transform .5s var(--ease), background .3s; }
.tri { margin-left: 5px; fill: currentColor; }
.ring { position: absolute; width: clamp(76px, 8vw, 112px); height: clamp(76px, 8vw, 112px); border-radius: 50%; border: 2px solid rgba(255, 255, 255, .6); animation: ring 2.4s var(--ease) infinite; }
@keyframes ring { 0% { transform: scale(1); opacity: .7; } 100% { transform: scale(1.9); opacity: 0; } }
.play:hover .btn-in { transform: scale(1.06); background: #fff; }
.cap { margin-top: clamp(18px, 2vw, 28px); font-size: .95rem; color: var(--muted-on-dark); max-width: 70ch; }
@media (prefers-reduced-motion: reduce) { .ring { animation: none; } }
</style>
