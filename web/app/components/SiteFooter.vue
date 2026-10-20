<script setup lang="ts">
import type { Bootstrap } from '~/utils/types'
const props = defineProps<{ boot: Bootstrap | null }>()
const localePath = useLocalePath()
const { t } = useI18n()
const company = computed(() => props.boot?.settings?.company || {})
const emails = computed(() => props.boot?.settings?.contact_emails || {})
const channels = computed(() => props.boot?.settings?.telegram_channels || {})
const account = computed(() => props.boot?.settings?.account_url || localePath('/manual') + '#account')
const year = new Date().getFullYear()
const { reset } = useConsent()
</script>
<template>
  <footer class="ftr">
    <div class="container">
      <div class="top">
        <div class="brand">
          <div class="logo">FLYSTAT</div>
          <p class="note">{{ t('footer.company') }}<br>{{ t('footer.made') }}</p>
          <NewsletterMini />
        </div>
        <nav class="col" aria-label="Site">
          <span class="h">{{ t('nav.people') }}</span>
          <NuxtLink v-for="m in (boot?.footer || []).filter(x => x.group !== 'partners')" :key="m.slug" :to="localePath('/' + m.slug)">{{ m.label }}</NuxtLink>
          <a :href="account" target="_blank" rel="noopener">{{ t('nav.account') }}</a>
        </nav>
        <nav class="col" aria-label="Partners">
          <span class="h">{{ t('nav.partners') }}</span>
          <NuxtLink v-for="m in (boot?.footer || []).filter(x => x.group === 'partners')" :key="m.slug" :to="localePath('/' + m.slug)">{{ m.label }}</NuxtLink>
          <NuxtLink :to="localePath('/contacts')">{{ t('footer.contacts') }}</NuxtLink>
        </nav>
        <div class="col">
          <span class="h">{{ t('footer.contacts') }}</span>
          <a v-for="(e, k) in emails" :key="k" :href="`mailto:${e}`">{{ e }}</a>
          <template v-if="Object.keys(channels).length"><span class="h" style="margin-top:10px">{{ t('footer.channels') }}</span><a v-for="(u, l) in channels" :key="l" :href="u" target="_blank" rel="noopener">Telegram · {{ String(l).toUpperCase() }}</a></template>
        </div>
      </div>
      <div class="bottom">
        <p>© {{ year }} {{ company.name || 'Flystat' }}<template v-if="company.address"> · {{ company.address }}</template></p>
        <nav class="legal" aria-label="Legal">
          <NuxtLink v-for="d in boot?.legal || []" :key="d.slug" :to="localePath('/legal/' + d.slug)">{{ d.title }}</NuxtLink>
          <button @click="reset()">{{ t('footer.cookies') }}</button>
        </nav>
      </div>
    </div>
  </footer>
</template>
<style scoped>
.ftr { background: var(--navy-900); color: var(--muted-on-dark); padding-block: clamp(48px, 6vw, 80px) 28px; margin-top: 0; }
.top { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.2fr; gap: 32px; }
.logo { color: #fff; font-weight: 900; letter-spacing: .08em; font-size: 1.25rem; margin-bottom: 14px; }
.note { font-size: 0.8438rem; max-width: 36ch; line-height: 1.55; margin-bottom: 18px; }
.col { display: grid; gap: 10px; align-content: start; font-size: 0.9062rem; }
.col a:hover { color: #fff; }
.h { font-size: 0.6875rem; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; color: #fff; opacity: .6; }
.bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.1); display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; font-size: 0.7812rem; }
.legal { display: flex; flex-wrap: wrap; gap: 6px 18px; } .legal a:hover, .legal button:hover { color: #fff; }
@media (max-width: 900px) { .top { grid-template-columns: 1fr 1fr; } }
@media (max-width: 520px) { .top { grid-template-columns: 1fr; } }
</style>
