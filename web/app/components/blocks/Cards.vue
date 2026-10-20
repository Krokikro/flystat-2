<script setup lang="ts">
defineProps<{ data: Record<string, any> }>()
const localePath = useLocalePath()
</script>
<template>
  <section class="section cards">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" />
      <div class="grid" :class="`cols-${data.columns || 3}`">
        <component :is="it.href ? 'NuxtLink' : 'div'" v-for="(it, i) in data.items" :key="i" :to="it.href ? localePath(it.href) : undefined" class="card" v-reveal="i * 70">
          <UiPic v-if="it.image?.src" :img="it.image" class="thumb" />
          <div v-else-if="it.icon" class="icon"><UiIcon :name="it.icon" /></div>
          <h3>{{ it.title }}</h3>
          <p>{{ it.text }}</p>
        </component>
      </div>
    </div>
  </section>
</template>
<style scoped>.thumb { border-radius: 14px; overflow: hidden; aspect-ratio: 4/3; }</style>
