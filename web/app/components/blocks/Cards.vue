<script setup lang="ts">
/** Feature cards. `columns` (2|3|4) is respected on desktop; a lone last card in a 3-up grid spans two columns so no row is left with an orphan. */
const props = defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
const cols = computed(() => Number(props.data.columns) || 3)
const n = computed(() => (props.data.items || []).length)
const wide = (i: number) => i === n.value - 1 && n.value % cols.value === 1 && cols.value > 1
</script>
<template>
  <section class="section cards" :class="`tone-${data.tone || 'light'}`">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" />
      <div class="grid" :style="{ '--cols': cols }">
        <component :is="it.href ? 'NuxtLink' : 'div'" v-for="(it, i) in data.items" :key="i" :to="it.href ? localePath(it.href) : undefined" class="c" :class="{ wide: wide(i), link: !!it.href }" v-reveal="(i % cols) * 90">
          <UiPic v-if="it.image?.src" :img="it.image" class="thumb" />
          <div v-else-if="it.icon" class="icon"><UiIcon :name="it.icon" :size="28" /></div>
          <h3>{{ it.title }}</h3>
          <p>{{ it.text }}</p>
          <span v-if="it.href" class="arr" aria-hidden="true"><UiIcon name="arrow-up-right" :size="20" /></span>
        </component>
      </div>
    </div>
  </section>
</template>
<style scoped>
.tone-alt { background: var(--bg-alt); } .tone-warm { background: var(--warm-2); }
.grid { display: grid; grid-template-columns: repeat(var(--cols, 3), minmax(0, 1fr)); gap: clamp(16px, 1.8vw, 28px); }
.c { position: relative; min-width: 0; display: grid; gap: 16px; align-content: start; padding: clamp(26px, 2.8vw, 44px); border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--line); overflow: hidden; transition: transform .6s var(--ease), box-shadow .6s var(--ease), border-color .4s; isolation: isolate; }
.c::before { content: ""; position: absolute; inset: 0; background: radial-gradient(120% 90% at 100% 0%, rgba(46,144,250,.10), transparent 60%); opacity: 0; transition: opacity .6s var(--ease); z-index: -1; }
.c:hover { transform: translateY(-6px); box-shadow: 0 30px 60px -20px rgba(16,42,86,.22), 0 12px 30px -16px rgba(16,42,86,.18); border-color: rgba(46,144,250,.35); }
.c:hover::before { opacity: 1; }
.tone-alt .c, .tone-warm .c { border-color: transparent; }
.wide { grid-column: span 2; }
.icon { width: 64px; height: 64px; border-radius: 18px; display: grid; place-items: center; background: linear-gradient(160deg, #EAF2FF, #F3F0FF); color: var(--blue-d); margin-bottom: 8px; transition: transform .6s var(--ease); }
.c:hover .icon { transform: translateY(-2px) rotate(-4deg); }
.thumb { border-radius: 16px; overflow: hidden; aspect-ratio: 16/10; margin: -4px -4px 8px; }
.c h3 { font-size: clamp(1.25rem, 1rem + .6vw, 1.65rem); }
.c p { color: var(--ink-2); font-size: clamp(1rem, 1rem + .12vw, 1.12rem); line-height: 1.55; }
.arr { position: absolute; right: 22px; top: 22px; width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; border: 1px solid var(--line); color: var(--ink); transition: background .3s, color .3s, transform .5s var(--ease); }
.c:hover .arr { background: var(--ink); color: #fff; transform: translate(2px, -2px); }
@media (max-width: 1100px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .wide { grid-column: auto; } }
@media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
</style>
