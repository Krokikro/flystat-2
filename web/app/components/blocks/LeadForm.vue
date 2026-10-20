<script setup lang="ts">
/** One form component, presets per cohort (PRD A5). Posts to /api/v2/leads; support requests become JSM tickets on the API side. */
import { Paperclip, Check } from '@lucide/vue'
const props = defineProps<{ data: Record<string, any>; compact?: boolean }>()
const { t, locale } = useI18n(); const route = useRoute(); const localePath = useLocalePath()
const boot = inject<any>('boot', null)
const cohort = computed(() => props.data.cohort || 'waitlist')
const countries = ref<{ code: string; name: string }[]>([])
onMounted(async () => { try { countries.value = await $fetch(`${useRuntimeConfig().public.apiBase}/api/v2/${locale.value}/countries`) } catch {} })

const f = reactive<Record<string, any>>({ name: '', email: '', phone: '', company: '', position: '', country: '', city: '', website: '', channels: [] as string[], volume: '', for_whom: '', role: '', kind: '', subject: '', direction: '', distributor_country: '', message: '', consent: false, website_url: '' })
const files = ref<File[]>([])
const errors = reactive<Record<string, string>>({})
const state = ref<'idle' | 'busy' | 'ok' | 'err' | 'limit'>('idle')

const show = (k: string) => ({
  distribution: ['name', 'company', 'position', 'email', 'phone', 'country', 'city', 'website', 'channels', 'volume', 'message'],
  waitlist: ['name', 'email', 'phone', 'country', 'city', 'for_whom', 'message'],
  support: ['name', 'email', 'country', 'role', 'kind', 'subject', 'message', 'attachment'],
  distributor_contact: ['name', 'email', 'phone', 'distributor_country', 'message'],
  cooperation: ['name', 'company', 'email', 'phone', 'country', 'direction', 'message'],
  subscribe: ['email'],
}[cohort.value] || ['name', 'email', 'message']).includes(k)
const req = (k: string) => ({
  distribution: ['name', 'company', 'email', 'phone', 'country'], waitlist: ['name', 'email', 'country'], support: ['name', 'email', 'kind', 'message'],
  distributor_contact: ['name', 'email', 'distributor_country'], cooperation: ['name', 'email', 'direction', 'message'], subscribe: ['email'],
}[cohort.value] || ['email']).includes(k)

function validate() {
  Object.keys(errors).forEach((k) => delete errors[k])
  for (const k of Object.keys(f)) if (req(k) && show(k) && (Array.isArray(f[k]) ? !f[k].length : !String(f[k]).trim())) errors[k] = t('form.required')
  if (f.email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(f.email)) errors.email = t('form.invalid_email')
  if (f.phone && !/^[+\d][\d\s().-]{5,}$/.test(f.phone)) errors.phone = t('form.invalid_phone')
  if (!f.consent) errors.consent = t('form.required')
  return !Object.keys(errors).length
}
async function submit() {
  if (!validate()) return
  state.value = 'busy'
  const fd = new FormData()
  const fields: Record<string, any> = { position: f.position, city: f.city, website: f.website, channels: f.channels, volume: f.volume, for_whom: f.for_whom, role: f.role, kind: f.kind, subject: f.subject, direction: f.direction, distributor_country: f.distributor_country }
  const meta: Record<string, any> = { screen: `${window.innerWidth}×${window.innerHeight}`, ua: navigator.userAgent, url: location.href, lang: locale.value }
  const utm: Record<string, string> = {}; for (const [k, v] of Object.entries(route.query)) if (k.startsWith('utm_')) utm[k] = String(v)
  const body: Record<string, any> = { cohort: cohort.value, name: f.name, email: f.email, phone: f.phone, company: f.company, country: f.country || f.distributor_country, message: f.message, locale: locale.value, source_page: route.path, consent: f.consent ? 1 : 0, website_url: f.website_url }
  for (const [k, v] of Object.entries(body)) fd.append(k, v == null ? '' : String(v))
  for (const [k, v] of Object.entries(fields)) if (Array.isArray(v)) v.forEach((x) => fd.append(`fields[${k}][]`, x)); else if (v) fd.append(`fields[${k}]`, v)
  for (const [k, v] of Object.entries(meta)) fd.append(`meta[${k}]`, v)
  for (const [k, v] of Object.entries(utm)) fd.append(`utm[${k}]`, v)
  files.value.forEach((file) => fd.append('attachments[]', file))
  try {
    await $fetch(`${useRuntimeConfig().public.apiBase}/api/v2/leads`, { method: 'POST', body: fd })
    state.value = 'ok'
    if (!props.compact) navigateTo(localePath(`/thanks/${['waitlist', 'distribution', 'support'].includes(cohort.value) ? cohort.value : 'default'}`))
  } catch (e: any) { state.value = e?.statusCode === 429 ? 'limit' : 'err' }
}
const privacy = computed(() => localePath('/legal/privacy')); const consentDoc = computed(() => localePath('/legal/consent'))
const consentHtml = computed(() => t('form.consent', { privacy: `<a href="${privacy.value}" target="_blank">${t('form.privacy')}</a>`, consent: `<a href="${consentDoc.value}" target="_blank">${t('form.consent_doc')}</a>` }))
const opt = (k: string) => Object.entries((t(`form.${k}_opts`, {}, { returnObjects: true } as any) as any) || {})
const uid = Math.random().toString(36).slice(2, 7)
</script>
<template>
  <section class="section lf" :class="[`tone-${data.tone || 'light'}`, `cohort-${cohort}`, { 'form-dark': data.tone === 'dark', compact }]">
    <i class="light" aria-hidden="true" />
    <div class="container inner">
      <div class="copy">
        <p v-if="data.eyebrow" class="eyebrow" v-reveal>{{ data.eyebrow }}</p>
        <h2 v-reveal>{{ data.title }}</h2>
        <p v-if="data.text" class="lead" v-reveal="80">{{ data.text }}</p>
      </div>
      <form class="form" :class="{ done: state === 'ok' }" novalidate v-reveal="140" @submit.prevent="submit">
        <div v-if="state === 'ok'" class="ok" role="status"><span class="okic"><Check :size="28" :stroke-width="2.5" /></span><p>{{ data.success || t('thanks.default') }}</p></div>
        <template v-else>
          <input v-model="f.website_url" type="text" name="website_url" tabindex="-1" autocomplete="off" class="hp" aria-hidden="true">
          <div class="fields">
            <div v-if="show('name')" class="field" :class="{ 'has-error': errors.name }"><label :for="`n-${uid}`">{{ t('form.name') }}</label><input :id="`n-${uid}`" v-model="f.name" autocomplete="name"><span v-if="errors.name" class="err">{{ errors.name }}</span></div>
            <div v-if="show('company')" class="field" :class="{ 'has-error': errors.company }"><label :for="`c-${uid}`">{{ t('form.company') }}</label><input :id="`c-${uid}`" v-model="f.company" autocomplete="organization"><span v-if="errors.company" class="err">{{ errors.company }}</span></div>
            <div v-if="show('position')" class="field"><label :for="`p-${uid}`">{{ t('form.position') }} <small>({{ t('form.optional') }})</small></label><input :id="`p-${uid}`" v-model="f.position" autocomplete="organization-title"></div>
            <div v-if="show('email')" class="field" :class="{ 'has-error': errors.email }"><label :for="`e-${uid}`">{{ t('form.email') }}</label><input :id="`e-${uid}`" v-model="f.email" type="email" autocomplete="email" inputmode="email"><span v-if="errors.email" class="err">{{ errors.email }}</span></div>
            <div v-if="show('phone')" class="field" :class="{ 'has-error': errors.phone }"><label :for="`ph-${uid}`">{{ t('form.phone') }} <small v-if="!req('phone')">({{ t('form.optional') }})</small></label><input :id="`ph-${uid}`" v-model="f.phone" type="tel" autocomplete="tel" placeholder="+971 50 000 0000"><span v-if="errors.phone" class="err">{{ errors.phone }}</span></div>
            <div v-if="show('country')" class="field" :class="{ 'has-error': errors.country }"><label :for="`co-${uid}`">{{ t('form.country') }}</label>
              <select :id="`co-${uid}`" v-model="f.country" autocomplete="country"><option value="">{{ t('form.select') }}</option><option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option><option value="XX">{{ t('form.other_country') }}</option></select><span v-if="errors.country" class="err">{{ errors.country }}</span></div>
            <div v-if="show('distributor_country')" class="field" :class="{ 'has-error': errors.distributor_country }"><label :for="`dc-${uid}`">{{ t('form.distributor_country') }}</label>
              <select :id="`dc-${uid}`" v-model="f.distributor_country"><option value="">{{ t('form.select') }}</option><option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option></select><span v-if="errors.distributor_country" class="err">{{ errors.distributor_country }}</span></div>
            <div v-if="show('city')" class="field"><label :for="`ci-${uid}`">{{ t('form.city') }} <small>({{ t('form.optional') }})</small></label><input :id="`ci-${uid}`" v-model="f.city" autocomplete="address-level2"></div>
            <div v-if="show('website')" class="field"><label :for="`w-${uid}`">{{ t('form.website') }} <small>({{ t('form.optional') }})</small></label><input :id="`w-${uid}`" v-model="f.website" type="url" placeholder="https://"></div>
            <div v-if="show('channels')" class="field full"><label>{{ t('form.channels') }}</label><div class="chips"><label v-for="[k, v] in opt('channels')" :key="k" class="chip"><input v-model="f.channels" type="checkbox" :value="k"><span>{{ v }}</span></label></div></div>
            <div v-if="show('volume')" class="field"><label :for="`v-${uid}`">{{ t('form.volume') }}</label><select :id="`v-${uid}`" v-model="f.volume"><option value="">{{ t('form.select') }}</option><option v-for="[k, v] in opt('volume')" :key="k" :value="k">{{ v }}</option></select></div>
            <div v-if="show('for_whom')" class="field"><label :for="`fw-${uid}`">{{ t('form.for_whom') }}</label><select :id="`fw-${uid}`" v-model="f.for_whom"><option value="">{{ t('form.select') }}</option><option v-for="[k, v] in opt('for')" :key="k" :value="k">{{ v }}</option></select></div>
            <div v-if="show('role')" class="field"><label :for="`r-${uid}`">{{ t('form.role') }}</label><select :id="`r-${uid}`" v-model="f.role"><option value="">{{ t('form.select') }}</option><option v-for="[k, v] in opt('role')" :key="k" :value="k">{{ v }}</option></select></div>
            <div v-if="show('kind')" class="field" :class="{ 'has-error': errors.kind }"><label :for="`k-${uid}`">{{ t('form.kind') }}</label><select :id="`k-${uid}`" v-model="f.kind"><option value="">{{ t('form.select') }}</option><option v-for="[k, v] in opt('kind')" :key="k" :value="k">{{ v }}</option></select><span v-if="errors.kind" class="err">{{ errors.kind }}</span></div>
            <div v-if="show('direction')" class="field" :class="{ 'has-error': errors.direction }"><label :for="`d-${uid}`">{{ t('form.direction') }}</label><select :id="`d-${uid}`" v-model="f.direction"><option value="">{{ t('form.select') }}</option><option v-for="[k, v] in opt('direction')" :key="k" :value="k">{{ v }}</option></select><span v-if="errors.direction" class="err">{{ errors.direction }}</span></div>
            <div v-if="show('subject')" class="field full"><label :for="`s-${uid}`">{{ t('form.subject') }}</label><input :id="`s-${uid}`" v-model="f.subject"></div>
            <div v-if="show('message')" class="field full" :class="{ 'has-error': errors.message }"><label :for="`m-${uid}`">{{ cohort === 'support' ? t('form.message') : t('form.comment') }} <small v-if="!req('message')">({{ t('form.optional') }})</small></label><textarea :id="`m-${uid}`" v-model="f.message" rows="4" /><span v-if="errors.message" class="err">{{ errors.message }}</span></div>
            <div v-if="show('attachment')" class="field full"><label class="file"><Paperclip :size="16" /><span>{{ files.length ? files.map(x => x.name).join(', ') : t('form.attachment') }}</span><input type="file" accept="image/*,.pdf,.mp4,.mov" multiple @change="(e: any) => files = Array.from(e.target.files).slice(0, 3)"></label></div>
          </div>
          <label class="check" :class="{ 'has-error': errors.consent }"><input v-model="f.consent" type="checkbox"><span v-html="consentHtml" /></label>
          <span v-if="errors.consent" class="err">{{ errors.consent }}</span>
          <div class="actions">
            <button class="btn" :class="data.tone === 'dark' ? 'btn-primary' : 'btn-navy'" :disabled="state === 'busy'">{{ state === 'busy' ? t('form.sending') : (data.button || t('form.success_title')) }}</button>
            <p v-if="state === 'err'" class="err">{{ t('form.error') }}</p>
            <p v-if="state === 'limit'" class="err">{{ t('form.too_many') }}</p>
          </div>
        </template>
      </form>
    </div>
  </section>
</template>
<style scoped>
.lf { position: relative; overflow: hidden; isolation: isolate; }
.light { position: absolute; z-index: 0; pointer-events: none; width: 50vw; height: 50vw; max-width: 800px; max-height: 800px; left: -12%; bottom: -30%; border-radius: 50%; background: radial-gradient(closest-side, rgba(46,144,250,.16), transparent 70%); }
.lf.tone-dark { background: linear-gradient(180deg, var(--navy-800), var(--navy-900)); color: var(--fg-on-dark); } .tone-dark .lead { color: var(--muted-on-dark); } .tone-dark .eyebrow { color: #8FB7FF; }
.tone-dark .light { background: radial-gradient(closest-side, rgba(46,144,250,.35), transparent 70%); }
.lf.tone-light { background: var(--bg-alt); }
.inner { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: clamp(32px, 5vw, 96px); align-items: start; }
.copy { display: grid; gap: clamp(14px, 1.4vw, 22px); position: sticky; top: calc(var(--header-h) + 32px); }
.copy h2 { font-size: clamp(2rem, 1rem + 2.4vw, 3.6rem); letter-spacing: -.03em; }
.copy .lead { font-size: clamp(1.05rem, 1rem + .3vw, 1.3rem); }
.form { position: relative; display: grid; gap: 20px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius-lg); padding: clamp(24px, 3.2vw, 48px); box-shadow: 0 30px 80px -30px rgba(16,42,86,.25); min-width: 0; }
.tone-dark .form { background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.12); backdrop-filter: blur(12px); box-shadow: 0 40px 80px -30px rgba(0,0,0,.5); }
.cohort-subscribe .inner { grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); }
.fields { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 18px; } .full { grid-column: 1 / -1; }
.form .field label { font-size: .9rem; }
.form .field input, .form .field select, .form .field textarea { padding: 1rem 1.1rem; border-radius: 14px; font-size: 1.02rem; }
.form .field textarea { min-height: 7.5rem; resize: vertical; }
.hp { position: absolute; left: -9999px; }
.chips { display: flex; flex-wrap: wrap; gap: 8px; } .chip { position: relative; display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border: 1px solid var(--line); border-radius: 999px; font-size: .9rem; cursor: pointer; transition: background .3s, color .3s, border-color .3s; } .chip:has(input:checked) { background: var(--ink); color: #fff; border-color: var(--ink); } .chip input { position: absolute; opacity: 0; }
.tone-dark .chip { border-color: rgba(255,255,255,.2); } .tone-dark .chip:has(input:checked) { background: var(--blue); border-color: var(--blue); }
.file { display: flex; gap: 10px; align-items: center; border: 1px dashed var(--line); border-radius: 14px; padding: 14px 16px; cursor: pointer; color: var(--muted); font-size: .9rem; } .file input { display: none; }
.form .check { font-size: .9rem; }
.actions { display: grid; gap: 10px; justify-items: start; }
.actions .btn { padding: 1.1rem 2rem; font-size: 1.05rem; }
.ok { display: grid; grid-template-columns: auto 1fr; gap: 18px; align-items: center; padding: 8px 0; }
.okic { width: 56px; height: 56px; border-radius: 50%; display: grid; place-items: center; background: rgba(18,183,106,.14); color: var(--ok); animation: pop .6s var(--ease) both; }
.ok p { font-weight: 700; font-size: clamp(1.1rem, 1rem + .4vw, 1.4rem); letter-spacing: -.01em; line-height: 1.3; }
@keyframes pop { from { transform: scale(.4); opacity: 0; } to { transform: none; opacity: 1; } }
.err { color: var(--danger); font-size: .85rem; }
@media (max-width: 860px) { .inner, .cohort-subscribe .inner { grid-template-columns: 1fr; gap: 28px; } .copy { position: static; } }
@media (max-width: 560px) { .fields { grid-template-columns: 1fr; } .form { padding: 22px 18px; } .ok { grid-template-columns: 1fr; } }
</style>
