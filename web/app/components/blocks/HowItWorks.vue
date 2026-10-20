<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section hiw">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" />
      <ol class="steps">
        <li v-for="(it, i) in data.items" :key="i" v-reveal="i * 100">
          <div class="num"><span>{{ i + 1 }}</span><UiIcon :name="it.icon" :size="26" /></div>
          <h3>{{ it.title }}</h3>
          <p>{{ it.text }}</p>
          <UiPic v-if="it.image?.src" :img="it.image" />
        </li>
      </ol>
    </div>
  </section>
</template>
<style scoped>
.hiw { background: linear-gradient(180deg, var(--navy-900) 0%, var(--navy-900) 8%, #fff 8%, #fff 100%); position: relative; }
.hiw::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 8%; background: var(--navy-900); border-radius: 0 0 40px 40px; z-index: 0; }
.hiw .container { position: relative; z-index: 1; padding-top: 40px; }
.steps { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 28px; counter-reset: s; }
.steps li { display: grid; gap: 12px; align-content: start; padding: 28px; border-radius: var(--radius-lg); background: var(--bg-alt); position: relative; }
.num { display: flex; align-items: center; justify-content: space-between; color: var(--blue-d); margin-bottom: 8px; }
.num span { font-family: var(--mono); font-size: 0.8125rem; letter-spacing: .14em; }
.steps p { color: var(--ink-2); }
</style>
