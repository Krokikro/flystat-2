<script setup lang="ts">
import type { Bootstrap } from '~/utils/types'
import { Menu, X, ChevronDown, UserRound, Globe } from '@lucide/vue'
const props = defineProps<{ boot: Bootstrap | null }>()
const localePath = useLocalePath()
const { t } = useI18n()
const open = ref(false)
const scrolled = ref(false)
const route = useRoute()
watch(() => route.fullPath, () => (open.value = false))
onMounted(() => {
  const onScroll = () => (scrolled.value = window.scrollY > 24)
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true })
  onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))
})
const people = computed(() => (props.boot?.header || []).filter((m) => m.group === 'people'))
const partners = computed(() => (props.boot?.header || []).filter((m) => m.group === 'partners'))
const account = computed(() => props.boot?.settings?.account_url || localePath('/manual') + '#account')
const link = (slug: string) => localePath('/' + slug)
</script>
<template>
  <header class="hdr" :class="{ scrolled, open }">
    <div class="container hdr-in">
      <NuxtLink :to="localePath('/')" class="logo" aria-label="Flystat">FLYSTAT</NuxtLink>
      <nav class="nav" aria-label="Main">
        <NuxtLink v-for="m in people" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink>
        <span class="sep" aria-hidden="true" />
        <NuxtLink v-for="m in partners" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink>
      </nav>
      <div class="tools">
        <LangSwitcher :boot="boot" />
        <a :href="account" class="account" target="_blank" rel="noopener"><UserRound :size="16" /><span>{{ t('nav.account') }}</span></a>
        <NuxtLink :to="localePath('/') + '#waitlist'" class="btn btn-primary cta">{{ t('nav.waitlist') }}</NuxtLink>
        <button class="burger" :aria-label="open ? t('nav.close') : t('nav.menu')" :aria-expanded="open" @click="open = !open"><X v-if="open" :size="22" /><Menu v-else :size="22" /></button>
      </div>
    </div>
    <Transition name="sheet">
      <div v-if="open" class="sheet">
        <div class="container sheet-in">
          <div class="sheet-group"><span class="group-label">{{ t('nav.people') }}</span><NuxtLink v-for="m in people" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink></div>
          <div class="sheet-group"><span class="group-label">{{ t('nav.partners') }}</span><NuxtLink v-for="m in partners" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink></div>
          <div class="sheet-group"><a :href="account" target="_blank" rel="noopener"><UserRound :size="16" /> {{ t('nav.account') }}</a><NuxtLink :to="localePath('/contacts')">{{ t('footer.contacts') }}</NuxtLink></div>
          <NuxtLink :to="localePath('/') + '#waitlist'" class="btn btn-primary">{{ t('nav.waitlist') }}</NuxtLink>
        </div>
      </div>
    </Transition>
  </header>
</template>
<style scoped>
.hdr { position: fixed; inset: 0 0 auto; z-index: 50; height: var(--header-h); display: flex; align-items: center; transition: background .4s, box-shadow .4s, color .4s; color: var(--ink); background: rgba(255,255,255,0); }
.hdr.scrolled, .hdr.open { background: rgba(255,255,255,.88); backdrop-filter: blur(16px); box-shadow: 0 1px 0 var(--line); }
body.has-dark-hero .hdr:not(.scrolled):not(.open) { color: #fff; }
.hdr-in { width: 100%; display: flex; align-items: center; gap: clamp(16px, 2vw, 36px); }
.logo { font-weight: 900; letter-spacing: .08em; font-size: 1.2rem; flex: none; }
.nav { display: flex; align-items: center; gap: clamp(14px, 1.4vw, 26px); font-size: .95rem; font-weight: 500; min-width: 0; }
.nav a { opacity: .88; white-space: nowrap; padding: 6px 0; position: relative; }
.nav a::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 2px; border-radius: 2px; background: currentColor; transform: scaleX(0); transform-origin: left; transition: transform .3s var(--ease); }
.nav a:hover, .nav a.router-link-active { opacity: 1; } .nav a:hover::after, .nav a.router-link-active::after { transform: scaleX(1); }
.sep { width: 1px; height: 18px; background: currentColor; opacity: .25; margin-inline: 4px; }
.tools { margin-left: auto; display: flex; align-items: center; gap: clamp(10px, 1vw, 18px); flex: none; }
.account { display: inline-flex; align-items: center; gap: 7px; font-size: .9rem; font-weight: 600; opacity: .9; white-space: nowrap; }
.cta { padding: .7rem 1.1rem; font-size: .9rem; border-radius: 12px; }
.burger { display: none; width: 42px; height: 42px; border-radius: 10px; align-items: center; justify-content: center; }
.sheet { position: absolute; top: var(--header-h); left: 0; right: 0; background: #fff; color: var(--ink); border-bottom: 1px solid var(--line); box-shadow: var(--shadow); max-height: calc(100dvh - var(--header-h)); overflow: auto; }
.sheet-in { display: grid; gap: 24px; padding-block: 24px 32px; }
.sheet-group { display: grid; gap: 12px; font-size: 1.15rem; font-weight: 600; }
.sheet-group a { display: flex; gap: 8px; align-items: center; }
.group-label { font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; opacity: .55; font-weight: 700; }
.sheet-enter-active, .sheet-leave-active { transition: opacity .25s, transform .25s; } .sheet-enter-from, .sheet-leave-to { opacity: 0; transform: translateY(-8px); }
@media (max-width: 1500px) { .account span { display: none; } }
@media (max-width: 1180px) { .nav { display: none; } .burger { display: inline-flex; } .cta { display: none; } }
@media (max-width: 480px) { .account { display: none; } }
</style>