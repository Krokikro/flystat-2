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
        <div class="group">
          <span class="group-label">{{ t('nav.people') }}</span>
          <NuxtLink v-for="m in people" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink>
        </div>
        <div class="group">
          <span class="group-label">{{ t('nav.partners') }}</span>
          <NuxtLink v-for="m in partners" :key="m.slug" :to="link(m.slug)">{{ m.label }}</NuxtLink>
        </div>
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
.hdr { position: fixed; inset: 0 0 auto; z-index: 50; height: var(--header-h); display: flex; align-items: center; transition: background .4s, box-shadow .4s, color .4s; color: var(--ink); background: rgba(255,255,255,.0); }
.hdr.scrolled, .hdr.open { background: rgba(255,255,255,.86); backdrop-filter: blur(14px); box-shadow: 0 1px 0 var(--line); }
body.has-dark-hero .hdr:not(.scrolled):not(.open) { color: #fff; }
.hdr-in { width: 100%; display: flex; align-items: center; gap: 28px; }
.logo { font-weight: 900; letter-spacing: .08em; font-size: 18px; }
.nav { display: flex; gap: 28px; margin-left: 8px; }
.group { display: flex; align-items: center; gap: 18px; font-size: 14.5px; font-weight: 500; }
.group-label { font-size: 11px; letter-spacing: .12em; text-transform: uppercase; opacity: .55; font-weight: 700; margin-right: 2px; }
.group a { opacity: .9; } .group a:hover, .group a.router-link-active { opacity: 1; text-decoration: underline; text-underline-offset: 6px; text-decoration-thickness: 2px; }
.tools { margin-left: auto; display: flex; align-items: center; gap: 14px; }
.account { display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; font-weight: 600; opacity: .9; white-space: nowrap; }
.cta { padding: 10px 16px; font-size: 14px; }
.burger { display: none; width: 40px; height: 40px; border-radius: 10px; align-items: center; justify-content: center; }
.sheet { position: absolute; top: var(--header-h); left: 0; right: 0; background: #fff; color: var(--ink); border-bottom: 1px solid var(--line); box-shadow: var(--shadow); max-height: calc(100dvh - var(--header-h)); overflow: auto; }
.sheet-in { display: grid; gap: 22px; padding-block: 22px 28px; }
.sheet-group { display: grid; gap: 12px; font-size: 18px; font-weight: 600; }
.sheet-group a { display: flex; gap: 8px; align-items: center; }
.sheet-enter-active, .sheet-leave-active { transition: opacity .25s, transform .25s; } .sheet-enter-from, .sheet-leave-to { opacity: 0; transform: translateY(-8px); }
@media (max-width: 1360px) { .group-label { display: none; } .nav { gap: 18px; } .group { gap: 14px; } }
@media (max-width: 1080px) { .nav { display: none; } .account span { display: none; } .burger { display: inline-flex; } .cta { display: none; } }
@media (max-width: 480px) { .account { display: none; } }
</style>
