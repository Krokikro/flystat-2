<script setup lang="ts">
/** Personal-account banner. Data: { title, text, button }; url from bootstrap settings.account_url. */
import { UserRound, ArrowUpRight, LineChart, FileText, History } from '@lucide/vue'
defineProps<{ data: Record<string, any> }>()
const boot = inject<any>('boot', null)
const url = computed(() => boot?.value?.settings?.account_url || '')
</script>
<template>
  <section class="section acc">
    <div class="container">
      <div class="box" v-reveal>
        <div class="glow" aria-hidden="true" />
        <div class="t">
          <div class="ic"><UserRound :size="26" /></div>
          <h2>{{ data.title }}</h2>
          <p class="lead">{{ data.text }}</p>
          <div class="acts">
            <a v-if="url" :href="url" class="btn btn-primary" target="_blank" rel="noopener">{{ data.button }} <ArrowUpRight :size="18" /></a>
            <span v-else class="btn btn-white soon">{{ data.button }} · {{ $t('common.soon') }}</span>
          </div>
        </div>
        <div class="art" aria-hidden="true">
          <div class="phone"><img src="/media/app/reports.webp" alt="" width="780" height="1688" loading="lazy" decoding="async"></div>
          <ul class="chips">
            <li><LineChart :size="16" /></li><li><FileText :size="16" /></li><li><History :size="16" /></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.acc { padding-block: clamp(24px, 3vw, 48px) clamp(56px, 7vw, 110px); }
.box { position: relative; display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr); gap: clamp(24px, 3vw, 56px); align-items: center; background: linear-gradient(120deg, #06264D, #0E2659 55%, #1E4D9C); color: var(--fg-on-dark); border-radius: var(--radius-lg); padding: clamp(28px, 4vw, 64px); overflow: hidden; min-height: 340px; }
.glow { position: absolute; width: 60%; aspect-ratio: 1; right: -10%; top: -40%; border-radius: 50%; background: radial-gradient(closest-side, rgba(143, 183, 255, .35), transparent 70%); pointer-events: none; }
.t { position: relative; display: grid; gap: 16px; justify-items: start; }
.ic { width: 60px; height: 60px; border-radius: 18px; background: rgba(255, 255, 255, .12); color: #8FB7FF; display: grid; place-items: center; }
.t h2 { color: #fff; font-size: clamp(1.7rem, 1rem + 1.8vw, 2.8rem); }
.t .lead { color: var(--muted-on-dark); }
.acts { margin-top: 8px; max-width: 100%; } .acts .btn { white-space: normal; text-align: center; max-width: 100%; }
.soon { opacity: .92; cursor: default; }
.art { position: relative; height: 100%; min-height: 260px; }
.phone { position: absolute; right: 8%; top: 12%; width: min(300px, 70%); aspect-ratio: 780 / 1688; border-radius: 40px; border: 8px solid #0A1B3A; background: #0A1B3A; box-shadow: 0 40px 80px rgba(0, 0, 0, .45); overflow: hidden; transform: rotate(-6deg); transition: transform 1s var(--ease); }
.box:hover .phone { transform: rotate(-4deg) translateY(-10px); }
.phone img { width: 100%; height: 100%; object-fit: cover; object-position: top; border-radius: 32px; }
.chips { list-style: none; margin: 0; padding: 0; position: absolute; left: 4%; top: 10%; display: grid; gap: 10px; }
.chips li { width: 44px; height: 44px; border-radius: 14px; background: rgba(255, 255, 255, .1); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, .14); color: #fff; display: grid; place-items: center; animation: float 6s var(--ease) infinite; }
.chips li:nth-child(2) { animation-delay: -2s; } .chips li:nth-child(3) { animation-delay: -4s; }
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
@media (max-width: 860px) {
  .box { grid-template-columns: minmax(0, 1fr); min-height: 0; padding-bottom: 0; }
  .art { min-height: 220px; } .phone { right: 50%; transform: translateX(50%) rotate(0); top: 8%; width: min(240px, 60%); }
  .box:hover .phone { transform: translateX(50%); } .chips { display: none; }
}
</style>
