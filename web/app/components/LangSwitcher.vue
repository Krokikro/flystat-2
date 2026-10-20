<script setup lang="ts">
import type { Bootstrap } from '~/utils/types'
import { Globe } from '@lucide/vue'
const props = defineProps<{ boot: Bootstrap | null }>()
const { locale, locales } = useI18n()
const switchLocalePath = useSwitchLocalePath()
const open = ref(false)
const names = computed(() => props.boot?.locale_names || {})
const root = ref<HTMLElement | null>(null)
onMounted(() => { const h = (e: MouseEvent) => { if (!root.value?.contains(e.target as Node)) open.value = false }; document.addEventListener('click', h); onBeforeUnmount(() => document.removeEventListener('click', h)) })
</script>
<template>
  <div ref="root" class="lang">
    <button class="lang-btn" :aria-expanded="open" :aria-label="$t('nav.language')" @click="open = !open"><Globe :size="16" /><span class="mono">{{ locale }}</span></button>
    <Transition name="pop">
      <ul v-if="open" class="menu" role="menu">
        <li v-for="l in locales" :key="l.code"><NuxtLink :to="switchLocalePath(l.code)" :class="{ active: l.code === locale }" role="menuitem" :hreflang="l.code" @click="open = false"><span class="mono code">{{ l.code }}</span>{{ names[l.code] || l.code }}</NuxtLink></li>
      </ul>
    </Transition>
  </div>
</template>
<style scoped>
.lang { position: relative; }
.lang-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 10px; border-radius: 10px; font-weight: 700; }
.lang-btn .mono { font-size: 0.75rem; }
.menu { position: absolute; right: 0; top: calc(100% + 8px); margin: 0; padding: 8px; list-style: none; background: #fff; color: var(--ink); border: 1px solid var(--line); border-radius: 14px; box-shadow: var(--shadow); min-width: 200px; display: grid; }
.menu a { display: flex; gap: 12px; align-items: center; padding: 9px 10px; border-radius: 10px; font-size: 0.875rem; font-weight: 500; }
.menu a:hover, .menu a.active { background: var(--bg-alt); }
.code { width: 22px; color: var(--muted); font-size: 0.6875rem; }
.pop-enter-active, .pop-leave-active { transition: opacity .18s, transform .18s; } .pop-enter-from, .pop-leave-to { opacity: 0; transform: translateY(-4px); }
</style>
