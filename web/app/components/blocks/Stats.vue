<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const nums = ref<HTMLElement[]>([])
onMounted(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return
  const io = new IntersectionObserver((es) => es.forEach((e) => {
    if (!e.isIntersecting) return; io.unobserve(e.target)
    const el = e.target as HTMLElement; const target = parseFloat(el.dataset.v || '0'); if (!isFinite(target)) return
    const t0 = performance.now(); const dur = 1200; const dec = (el.dataset.v || '').includes('.') ? 1 : 0
    const step = (now: number) => { const k = Math.min(1, (now - t0) / dur); const e2 = 1 - Math.pow(1 - k, 3); el.textContent = (target * e2).toFixed(dec); if (k < 1) requestAnimationFrame(step) }
    requestAnimationFrame(step)
  }), { threshold: 0.4 })
  nums.value.forEach((n) => io.observe(n))
})
</script>
<template>
  <section class="section stats band-dark">
    <div class="container">
      <h2 v-if="data.title" class="h" v-reveal>{{ data.title }}</h2>
      <div class="grid cols-4">
        <div v-for="(s, i) in data.items" :key="i" class="stat" v-reveal="i * 80">
          <div class="val"><span ref="nums" :data-v="s.value">{{ s.value }}</span><small>{{ s.suffix }}</small></div>
          <h3>{{ s.label }}</h3>
          <p>{{ s.text }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.stats { background: linear-gradient(180deg, var(--navy-800) 0%, var(--navy-900) 100%); }
.h { margin-bottom: 40px; max-width: 600px; }
.stat { display: grid; gap: 10px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.14); }
.val { display: flex; align-items: baseline; gap: 4px; font-weight: 900; font-size: clamp(48px, 6vw, 84px); letter-spacing: -.04em; line-height: 1; color: #fff; font-variant-numeric: tabular-nums; }
.val small { font-size: .35em; font-weight: 700; color: #8FB7FF; letter-spacing: 0; }
.stat h3 { color: #fff; } .stat p { color: var(--muted-on-dark); font-size: 15px; }
</style>
