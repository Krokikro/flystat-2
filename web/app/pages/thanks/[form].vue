<script setup lang="ts">
import { ArrowRight } from '@lucide/vue'
const route = useRoute(); const { t } = useI18n(); const localePath = useLocalePath()
const key = ['waitlist', 'distribution', 'support'].includes(String(route.params.form)) ? String(route.params.form) : 'default'
useSeoMeta({ title: () => t('thanks.title'), robots: 'noindex, nofollow' })
</script>
<template>
  <section class="thanks">
    <div class="container">
      <div class="box">
        <span class="mark" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="64" height="64"><circle cx="32" cy="32" r="29" class="ring" /><path d="M20 33.5l8 8 16-17" class="tick" /></svg>
        </span>
        <p class="eyebrow">Flystat</p>
        <h1>{{ t('thanks.title') }}</h1>
        <p class="lead">{{ t('thanks.' + key) }}</p>
        <div class="acts">
          <NuxtLink :to="localePath('/')" class="btn btn-primary">{{ t('thanks.back') }}</NuxtLink>
          <NuxtLink :to="localePath('/device')" class="btn btn-ghost-dark">{{ t('thanks.meanwhile') }}: {{ t('common.learn_more') }} <ArrowRight :size="18" /></NuxtLink>
        </div>
      </div>
      <div class="art" aria-hidden="true">
        <img src="/media/renders/sensor-exploded.webp" alt="" width="1300" height="1600" loading="lazy" decoding="async">
      </div>
    </div>
  </section>
</template>
<style scoped>
.thanks { min-height: 100svh; display: grid; align-items: center; padding-top: var(--header-h); background: radial-gradient(70% 60% at 85% 30%, #EAF2FF 0%, transparent 60%), #fff; }
.thanks .container { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: clamp(24px, 4vw, 72px); align-items: center; padding-block: clamp(40px, 6vw, 96px); }
.box { display: grid; gap: 18px; max-width: 620px; justify-items: start; }
.mark svg { display: block; width: clamp(64px, 6vw, 88px); height: auto; }
.ring { fill: none; stroke: var(--ok); stroke-width: 3; stroke-dasharray: 190; stroke-dashoffset: 190; animation: draw 1s var(--ease) .1s forwards; }
.tick { fill: none; stroke: var(--ok); stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 40; stroke-dashoffset: 40; animation: draw .6s var(--ease) .7s forwards; }
@keyframes draw { to { stroke-dashoffset: 0; } }
.acts { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; max-width: 100%; } .acts .btn { white-space: normal; text-align: center; }
.art { position: relative; aspect-ratio: 1; max-width: 520px; justify-self: center; width: 100%; }
.art::before { content: ""; position: absolute; inset: 6%; border-radius: 50%; background: radial-gradient(closest-side, #FFFFFF, #EAF2FF 70%, transparent); }
.art img { position: relative; width: 84%; margin: 8% auto 0; filter: drop-shadow(0 40px 50px rgba(16, 42, 86, .25)); animation: hover 7s var(--ease) infinite; }
@keyframes hover { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-14px) rotate(1deg); } }
@media (max-width: 860px) { .thanks .container { grid-template-columns: 1fr; } .art { max-width: 300px; order: -1; } }
@media (prefers-reduced-motion: reduce) { .ring, .tick { stroke-dashoffset: 0; animation: none; } .art img { animation: none; } }
</style>
