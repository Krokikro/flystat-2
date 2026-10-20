<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section steps band-alt">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" />
      <ol class="list">
        <li v-for="(it, i) in data.items" :key="i" v-reveal="i * 60">
          <span class="n">{{ String(i + 1).padStart(2, '0') }}</span>
          <div class="ic"><UiIcon :name="it.icon" :size="24" /></div>
          <div class="t"><h3>{{ it.title }}</h3><p>{{ it.text }}</p></div>
        </li>
      </ol>
      <div v-if="data.cta?.label" class="more" v-reveal><UiCtaLink :cta="data.cta" variant="btn-navy" /></div>
    </div>
  </section>
</template>
<style scoped>
.list { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 14px; }
.list li { display: grid; grid-template-columns: auto auto 1fr; gap: 16px; align-items: start; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 20px 22px; }
.n { font-family: var(--mono); font-size: 0.75rem; color: var(--muted); padding-top: 4px; }
.ic { width: 42px; height: 42px; border-radius: 12px; background: var(--bg-alt); color: var(--blue-d); display: grid; place-items: center; }
.t { display: grid; gap: 6px; } .t p { color: var(--ink-2); font-size: 0.9375rem; }
.more { margin-top: 28px; }
</style>
