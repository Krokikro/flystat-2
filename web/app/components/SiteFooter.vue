<script setup lang="ts">
import type { Bootstrap } from '~/utils/types'
import { ArrowUp, ShieldAlert, Mail, Send } from '@lucide/vue'
const props = defineProps<{ boot: Bootstrap | null }>()
const localePath = useLocalePath()
const switchLocalePath = useSwitchLocalePath()
const { t, locale, locales } = useI18n()
const company = computed(() => props.boot?.settings?.company || {})
const emails = computed(() => props.boot?.settings?.contact_emails || {})
const channels = computed(() => props.boot?.settings?.telegram_channels || {})
const names = computed(() => props.boot?.locale_names || {})
const account = computed(() => props.boot?.settings?.account_url || localePath('/manual') + '#account')
const year = new Date().getFullYear()
const { reset } = useConsent()
const people = computed(() => (props.boot?.footer || []).filter((x) => x.group !== 'partners'))
const partners = computed(() => (props.boot?.footer || []).filter((x) => x.group === 'partners'))
const toTop = () => window.scrollTo({ top: 0, behavior: 'smooth' })
</script>
<template>
  <footer class="ftr">
    <div class="container">
      <div class="top">
        <div class="brand">
          <NuxtLink :to="localePath('/')" class="logo" aria-label="Flystat">FLYSTAT</NuxtLink>
          <p class="tag">{{ t('seo.default_description') }}</p>
          <p class="note"><ShieldAlert :size="16" />{{ t('footer.made') }}</p>
        </div>
        <NewsletterMini class="nl" />
      </div>

      <div class="cols">
        <nav class="col" :aria-label="t('nav.people')">
          <span class="h">{{ t('nav.people') }}</span>
          <NuxtLink v-for="m in people" :key="m.slug" :to="localePath('/' + m.slug)">{{ m.label }}</NuxtLink>
          <a :href="account" target="_blank" rel="noopener">{{ t('nav.account') }}</a>
        </nav>
        <nav class="col" :aria-label="t('nav.partners')">
          <span class="h">{{ t('nav.partners') }}</span>
          <NuxtLink v-for="m in partners" :key="m.slug" :to="localePath('/' + m.slug)">{{ m.label }}</NuxtLink>
          <NuxtLink :to="localePath('/contacts')">{{ t('footer.contacts') }}</NuxtLink>
        </nav>
        <div class="col wide">
          <span class="h">{{ t('footer.contacts') }}</span>
          <a v-for="(e, k) in emails" :key="k" :href="`mailto:${e}`" class="mail"><Mail :size="14" />{{ e }}</a>
          <template v-if="Object.keys(channels).length"><span class="h sub">{{ t('footer.channels') }}</span><a v-for="(u, l) in channels" :key="l" :href="u" target="_blank" rel="noopener" class="mail"><Send :size="14" />Telegram · {{ String(l).toUpperCase() }}</a></template>
        </div>
        <div class="col lang">
          <span class="h">{{ t('nav.language') }}</span>
          <ul class="langs">
            <li v-for="l in locales" :key="l.code"><NuxtLink :to="switchLocalePath(l.code)" :class="{ on: l.code === locale }" :hreflang="l.code" :lang="l.code">{{ names[l.code] || l.code }}</NuxtLink></li>
          </ul>
        </div>
      </div>

      <div class="bottom">
        <p class="copy">© {{ year }} {{ company.name || 'Flystat' }}<span v-if="company.group"> · {{ t('footer.company') }}</span><br v-if="company.address"><span v-if="company.address" class="addr">{{ company.address }}</span></p>
        <nav class="legal" :aria-label="t('footer.legal')">
          <NuxtLink v-for="d in boot?.legal || []" :key="d.slug" :to="localePath('/legal/' + d.slug)">{{ d.title }}</NuxtLink>
          <button type="button" @click="reset()">{{ t('footer.cookies') }}</button>
        </nav>
        <button type="button" class="up" :aria-label="t('common.scroll')" @click="toTop"><ArrowUp :size="18" /></button>
      </div>
    </div>
  </footer>
</template>
<style scoped>
.ftr { background: var(--navy-900); color: var(--muted-on-dark); padding-block: clamp(56px, 7vw, 110px) 28px; position: relative; overflow: hidden; }
.ftr::before { content: ""; position: absolute; inset: 0 0 auto; height: 1px; background: linear-gradient(90deg, transparent, rgba(143, 183, 255, .5), transparent); }
.top { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr); gap: clamp(28px, 4vw, 80px); align-items: start; padding-bottom: clamp(32px, 4vw, 56px); border-bottom: 1px solid rgba(255, 255, 255, .1); }
.brand { display: grid; gap: 18px; justify-items: start; }
.logo { color: #fff; font-weight: 900; letter-spacing: .1em; font-size: clamp(1.6rem, 1rem + 1.2vw, 2.4rem); line-height: 1; }
.tag { font-size: clamp(1rem, .9rem + .3vw, 1.2rem); max-width: 44ch; line-height: 1.5; color: #D6DEF0; }
.note { display: inline-flex; gap: 8px; align-items: center; font-size: .82rem; padding: 8px 14px; border-radius: 999px; background: rgba(247, 144, 9, .12); color: #FDB022; border: 1px solid rgba(247, 144, 9, .3); max-width: 100%; }
.nl { justify-self: end; width: 100%; }
.cols { display: grid; grid-template-columns: 1fr 1fr 1.4fr 1.4fr; gap: clamp(24px, 3vw, 48px); padding-block: clamp(32px, 4vw, 56px); }
.col { display: grid; gap: 12px; align-content: start; font-size: 1rem; }
.col a { color: #D6DEF0; width: fit-content; transition: color .2s; } .col a:hover { color: #fff; }
.h { font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; color: #fff; opacity: .55; margin-bottom: 4px; }
.h.sub { margin-top: 12px; }
.mail { display: inline-flex; gap: 8px; align-items: center; font-size: .95rem; overflow-wrap: anywhere; } .mail svg { opacity: .6; flex: none; }
.langs { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 6px; }
.langs a { display: inline-block; padding: 7px 12px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, .14); font-size: .85rem; font-weight: 600; color: #D6DEF0; }
.langs a:hover { border-color: rgba(255, 255, 255, .4); } .langs a.on { background: #fff; color: var(--ink); border-color: #fff; }
.bottom { padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, .1); display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px 32px; align-items: center; font-size: .82rem; }
.copy { line-height: 1.6; grid-column: 1 / -1; order: 3; } .addr { opacity: .75; }
.legal { display: flex; flex-wrap: wrap; gap: 6px 20px; } .legal a, .legal button { color: #D6DEF0; } .legal a:hover, .legal button:hover { color: #fff; }
.up { width: 44px; height: 44px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, .18); color: #fff; display: grid; place-items: center; transition: background .25s, transform .25s var(--ease); }
.up:hover { background: rgba(255, 255, 255, .1); transform: translateY(-2px); }
@media (max-width: 1100px) { .cols { grid-template-columns: 1fr 1fr; } }
@media (max-width: 860px) { .top { grid-template-columns: 1fr; } .nl { justify-self: start; } }
@media (max-width: 520px) { .cols { grid-template-columns: 1fr; } }
</style>
