<script setup lang="ts">
/** Contact e-mail cards + legal-entity card. Data: { title, show_company, items[{label,email,text}] }. */
import { Copy, Check, Building2, MapPin, Hash, CalendarDays } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any> }>()
const boot = inject<any>('boot', null)
const company = computed(() => boot?.value?.settings?.company || {})
const copied = ref('')
const copy = async (e: string) => { try { await navigator.clipboard.writeText(e); copied.value = e; setTimeout(() => (copied.value = ''), 1500) } catch {} }
// icon by the mailbox name, stable across locales
const iconFor = (email: string) => {
  const k = (email || '').split('@')[0]?.toLowerCase() || ''
  if (k.startsWith('distrib')) return 'handshake'
  if (k.startsWith('support')) return 'life-buoy'
  if (k.startsWith('clinic')) return 'stethoscope'
  if (k.startsWith('press')) return 'newspaper'
  if (k.startsWith('privacy')) return 'shield-check'
  return 'mail'
}
const items = computed(() => (props.data.items || []) as { label: string; email: string; text?: string }[])
</script>
<template>
  <section class="section contacts">
    <div class="container" :class="['inner', { solo: !data.show_company }]">
      <div class="emails">
        <UiSectionHead :title="data.title" :text="data.text" />
        <ul class="list">
          <li v-for="(it, i) in items" :key="i" v-reveal="i * 60">
            <span class="ic"><UiIcon :name="iconFor(it.email)" :size="22" /></span>
            <div class="t">
              <b>{{ it.label }}</b>
              <a :href="`mailto:${it.email}`">{{ it.email }}</a>
              <small v-if="it.text">{{ it.text }}</small>
            </div>
            <button class="cp" :class="{ ok: copied === it.email }" :aria-label="'copy ' + it.email" @click="copy(it.email)"><Check v-if="copied === it.email" :size="18" /><Copy v-else :size="18" /></button>
          </li>
        </ul>
      </div>
      <aside v-if="data.show_company" class="legal" v-reveal="160">
        <div class="lg-head">
          <span class="lg-ic"><Building2 :size="24" /></span>
          <p class="eyebrow">{{ company.brand || 'Flystat' }} · {{ company.group }}</p>
        </div>
        <h3>{{ company.name }}</h3>
        <p v-if="company.name_cn" class="cn">{{ company.name_cn }}</p>
        <dl>
          <div v-if="company.address"><dt><MapPin :size="16" />{{ $t('footer.address') }}</dt><dd>{{ company.address }}</dd></div>
          <div v-if="company.reg_number"><dt><Hash :size="16" />Reg. №</dt><dd class="mono">{{ company.reg_number }}</dd></div>
          <div v-if="company.founded"><dt><CalendarDays :size="16" />Est.</dt><dd>{{ company.founded }}</dd></div>
        </dl>
      </aside>
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr); gap: clamp(28px, 4vw, 72px); align-items: start; }
.inner.solo { grid-template-columns: 1fr; }
.list { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: 1fr; gap: 14px; }
.solo .list { grid-template-columns: repeat(auto-fit, minmax(min(100%, 380px), 1fr)); }
.list li { display: grid; grid-template-columns: auto 1fr auto; gap: 18px; align-items: center; padding: clamp(18px, 2vw, 26px); border: 1px solid var(--line); border-radius: var(--radius); background: #fff; min-width: 0; transition: transform .5s var(--ease), box-shadow .5s var(--ease); }
.list li:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
.ic { width: 52px; height: 52px; border-radius: 16px; background: var(--bg-alt); color: var(--blue-d); display: grid; place-items: center; }
.t { display: grid; gap: 4px; min-width: 0; }
.t b { font-size: 1.05rem; letter-spacing: -.01em; }
.t a { color: var(--blue-d); font-weight: 700; font-size: clamp(1.05rem, .9rem + .45vw, 1.4rem); letter-spacing: -.01em; overflow-wrap: anywhere; }
.t small { color: var(--muted); font-size: .875rem; line-height: 1.45; }
.cp { width: 40px; height: 40px; border-radius: 12px; color: var(--muted); display: grid; place-items: center; border: 1px solid var(--line); transition: background .2s, color .2s; }
.cp:hover { background: var(--bg-alt); color: var(--ink); } .cp.ok { background: #ECFDF3; color: var(--ok); border-color: #A6F4C5; }
.legal { display: grid; gap: 12px; padding: clamp(24px, 2.8vw, 40px); border-radius: var(--radius-lg); background: linear-gradient(160deg, #F0F4FE, #FBF4EC); border: 1px solid var(--line); position: sticky; top: calc(var(--header-h) + 24px); }
.lg-head { display: grid; gap: 14px; } .lg-head .eyebrow { margin: 0; }
.lg-ic { width: 52px; height: 52px; border-radius: 16px; background: #fff; color: var(--blue-d); display: grid; place-items: center; }
.legal h3 { font-size: clamp(1.2rem, 1rem + .5vw, 1.5rem); }
.cn { color: var(--muted); }
dl { display: grid; gap: 12px; margin: 8px 0 0; padding-top: 16px; border-top: 1px solid rgba(16, 42, 86, .1); }
dl div { display: grid; gap: 4px; font-size: .95rem; }
dt { display: inline-flex; align-items: center; gap: 6px; color: var(--muted); font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
dd { margin: 0; color: var(--ink); line-height: 1.45; } dd.mono { text-transform: none; letter-spacing: .04em; }
@media (max-width: 960px) { .inner { grid-template-columns: 1fr; } .legal { position: static; } }
@media (max-width: 480px) { .list li { grid-template-columns: 1fr auto; align-items: start; } .ic { display: none; } .t a { font-size: 1.05rem; } }
</style>
