<script setup lang="ts">
import { Cookie } from '@lucide/vue'
const { decided, accept, decline } = useConsent()
const localePath = useLocalePath()
const show = ref(false)
onMounted(() => setTimeout(() => (show.value = !decided.value), 1200))
watch(decided, (v) => { if (v) show.value = false })
</script>
<template>
  <Transition name="cb">
    <div v-if="show" class="cb" role="dialog" aria-live="polite" :aria-label="$t('cookie.title')">
      <div class="head"><span class="ic"><Cookie :size="18" /></span><strong>{{ $t('cookie.title') }}</strong></div>
      <p>{{ $t('cookie.text') }} <NuxtLink :to="localePath('/legal/cookies')">{{ $t('cookie.settings') }}</NuxtLink></p>
      <div class="acts"><button class="btn btn-ghost-dark" @click="decline()">{{ $t('cookie.decline') }}</button><button class="btn btn-navy" @click="accept()">{{ $t('cookie.accept') }}</button></div>
    </div>
  </Transition>
</template>
<style scoped>
.cb { position: fixed; z-index: 70; left: 16px; bottom: max(16px, env(safe-area-inset-bottom)); width: min(380px, calc(100vw - 32px)); background: #fff; color: var(--ink); border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 24px 60px rgba(16, 42, 86, .18); padding: 18px 18px 16px; display: grid; gap: 10px; }
.head { display: flex; align-items: center; gap: 10px; font-size: .95rem; }
.ic { width: 32px; height: 32px; border-radius: 10px; background: var(--warm); color: #B54708; display: grid; place-items: center; flex: none; }
.cb p { color: var(--ink-2); font-size: .82rem; line-height: 1.5; } .cb a { text-decoration: underline; text-underline-offset: 2px; }
.acts { display: flex; gap: 8px; margin-top: 4px; } .acts .btn { flex: 1; padding: .65rem .8rem; font-size: .82rem; border-radius: 12px; white-space: normal; }
.cb-enter-active, .cb-leave-active { transition: opacity .35s var(--ease), transform .35s var(--ease); } .cb-enter-from, .cb-leave-to { opacity: 0; transform: translateY(16px); }
</style>
