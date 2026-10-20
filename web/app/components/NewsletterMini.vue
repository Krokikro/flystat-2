<script setup lang="ts">
import { ArrowRight, Check } from '@lucide/vue'
const { t, locale } = useI18n()
const email = ref(''); const state = ref<'idle' | 'busy' | 'ok' | 'err'>('idle')
async function submit() {
  if (!email.value) return; state.value = 'busy'
  try {
    await $fetch(`${useRuntimeConfig().public.apiBase}/api/v2/leads`, { method: 'POST', body: { cohort: 'subscribe', email: email.value, locale: locale.value, source_page: useRoute().fullPath, consent: true, fields: { source: 'footer' } } })
    state.value = 'ok'
  } catch { state.value = 'err' }
}
</script>
<template>
  <form class="nl" @submit.prevent="submit">
    <label class="h" for="nl-email">{{ t('footer.newsletter') }}</label>
    <p class="hint">{{ t('footer.newsletter_text') }}</p>
    <div v-if="state !== 'ok'" class="row">
      <input id="nl-email" v-model="email" type="email" required :placeholder="t('form.email')" autocomplete="email">
      <button class="go" :disabled="state === 'busy'" :aria-label="t('footer.subscribe')"><span class="lbl">{{ t('footer.subscribe') }}</span><ArrowRight :size="20" /></button>
    </div>
    <p v-else class="ok"><Check :size="18" /> {{ t('thanks.default') }}</p>
    <p v-if="state === 'err'" class="err">{{ t('form.error') }}</p>
  </form>
</template>
<style scoped>
.nl { display: grid; gap: 10px; max-width: 520px; padding: clamp(20px, 2.4vw, 32px); border-radius: var(--radius); background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .1); }
.h { font-size: 1.15rem; font-weight: 700; color: #fff; letter-spacing: -.01em; }
.hint { font-size: .9rem; color: var(--muted-on-dark); line-height: 1.5; }
.row { display: flex; gap: 0; margin-top: 6px; border-radius: 16px; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .16); padding: 5px 5px 5px 0; transition: border-color .2s, box-shadow .2s; }
.row:focus-within { border-color: var(--blue); box-shadow: 0 0 0 4px rgba(46, 144, 250, .2); }
.row input { flex: 1; min-width: 0; padding: .85rem 1rem; border: 0; background: transparent; color: #fff; font: inherit; font-size: 1rem; outline: none; }
.row input::placeholder { color: rgba(255, 255, 255, .5); }
.go { display: inline-flex; align-items: center; gap: 8px; padding: .75rem 1.1rem; border-radius: 12px; background: var(--blue); color: #fff; font-weight: 700; font-size: .95rem; white-space: nowrap; transition: background .2s, transform .25s var(--ease); }
.go:hover { background: var(--blue-d); } .go:disabled { opacity: .6; }
.ok { display: inline-flex; align-items: center; gap: 8px; color: #8FD7A8; font-size: .95rem; } .err { color: #FDA29B; font-size: .8rem; }
@media (max-width: 420px) { .go .lbl { display: none; } .go { padding: .75rem .9rem; } }
</style>
