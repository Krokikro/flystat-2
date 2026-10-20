<script setup lang="ts">
const { decided, accept, decline } = useConsent()
const localePath = useLocalePath()
const show = ref(false)
onMounted(() => setTimeout(() => (show.value = !decided.value), 1200))
watch(decided, (v) => { if (v) show.value = false })
</script>
<template>
  <Transition name="cb">
    <div v-if="show" class="cb" role="dialog" aria-live="polite" :aria-label="$t('cookie.title')">
      <div class="txt"><strong>{{ $t('cookie.title') }}</strong><p>{{ $t('cookie.text') }} <NuxtLink :to="localePath('/legal/cookies')">{{ $t('cookie.settings') }}</NuxtLink></p></div>
      <div class="acts"><button class="btn btn-ghost-dark" @click="decline()">{{ $t('cookie.decline') }}</button><button class="btn btn-navy" @click="accept()">{{ $t('cookie.accept') }}</button></div>
    </div>
  </Transition>
</template>
<style scoped>
.cb { position: fixed; z-index: 70; left: 16px; right: 16px; bottom: max(16px, env(safe-area-inset-bottom)); margin-inline: auto; max-width: 760px; background: #fff; color: var(--ink); border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow); padding: 18px 20px; display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
.txt { flex: 1 1 320px; font-size: 13.5px; } .txt p { color: var(--ink-2); margin-top: 4px; } .txt a { text-decoration: underline; }
.acts { display: flex; gap: 8px; } .acts .btn { padding: 10px 14px; font-size: 13px; }
.cb-enter-active, .cb-leave-active { transition: opacity .3s, transform .3s; } .cb-enter-from, .cb-leave-to { opacity: 0; transform: translateY(12px); }
</style>
