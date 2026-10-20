<script setup lang="ts">
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
      <button class="btn btn-primary" :disabled="state === 'busy'">{{ t('footer.subscribe') }}</button>
    </div>
    <p v-else class="ok">✓ {{ t('thanks.default') }}</p>
    <p v-if="state === 'err'" class="err">{{ t('form.error') }}</p>
  </form>
</template>
<style scoped>
.nl { display: grid; gap: 8px; max-width: 420px; }
.h { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; font-weight: 700; color: #fff; opacity: .6; }
.hint { font-size: 13px; }
.row { display: flex; gap: 8px; }
.row input { flex: 1; min-width: 0; padding: 11px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); color: #fff; font: inherit; font-size: 14px; }
.row .btn { padding: 10px 14px; font-size: 13px; }
.ok { color: #8FD7A8; font-size: 13px; } .err { color: #FDA29B; font-size: 12px; }
</style>
