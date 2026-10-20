<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section tl">
    <div class="container">
      <UiSectionHead :title="data.title" />
      <ol class="line">
        <li v-for="(it, i) in data.items" :key="i" v-reveal="i * 80">
          <span class="date mono">{{ it.date }}</span>
          <div class="dot" />
          <div class="body"><h3>{{ it.title }}</h3><p>{{ it.text }}</p></div>
        </li>
      </ol>
    </div>
  </section>
</template>
<style scoped>
.line { list-style: none; margin: 0; padding: 0; display: grid; }
.line li { display: grid; grid-template-columns: 110px 24px 1fr; gap: 16px; padding-bottom: 32px; position: relative; }
.line li::before { content: ""; position: absolute; left: calc(110px + 16px + 11px); top: 14px; bottom: -6px; width: 2px; background: var(--line); } .line li:last-child::before { display: none; }
.date { color: var(--blue-d); padding-top: 4px; }
.dot { width: 24px; height: 24px; border-radius: 50%; background: #fff; border: 2px solid var(--blue); display: grid; place-items: center; position: relative; z-index: 1; } .dot::after { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--blue); }
.body { display: grid; gap: 6px; } .body p { color: var(--ink-2); max-width: 70ch; }
@media (max-width: 560px) { .line li { grid-template-columns: 24px 1fr; } .date { grid-column: 2; order: -1; } .line li::before { left: 11px; } }
</style>
