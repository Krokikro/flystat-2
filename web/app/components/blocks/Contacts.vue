<script setup lang="ts">
import { Mail, Copy, Check } from '@lucide/vue'
defineProps<{ data: Record<string, any> }>()
const boot = inject<any>('boot', null)
const company = computed(() => boot?.value?.settings?.company || {})
const copied = ref('')
const copy = async (e: string) => { try { await navigator.clipboard.writeText(e); copied.value = e; setTimeout(() => (copied.value = ''), 1500) } catch {} }
</script>
<template>
  <section class="section contacts">
    <div class="container inner">
      <div>
        <UiSectionHead :title="data.title" />
        <ul class="list">
          <li v-for="(it, i) in data.items" :key="i" v-reveal="i * 50">
            <div class="ic"><Mail :size="18" /></div>
            <div class="t"><b>{{ it.label }}</b><a :href="`mailto:${it.email}`">{{ it.email }}</a><small v-if="it.text">{{ it.text }}</small></div>
            <button class="cp" :aria-label="'copy ' + it.email" @click="copy(it.email)"><Check v-if="copied === it.email" :size="16" /><Copy v-else :size="16" /></button>
          </li>
        </ul>
      </div>
      <div v-if="data.show_company" class="legal card" v-reveal="120">
        <p class="eyebrow">{{ company.brand || 'Flystat' }} · {{ company.group }}</p>
        <h3>{{ company.name }}</h3>
        <p v-if="company.name_cn" class="cn">{{ company.name_cn }}</p>
        <dl>
          <div v-if="company.address"><dt>{{ $t('footer.address') }}</dt><dd>{{ company.address }}</dd></div>
          <div v-if="company.reg_number"><dt>Reg. №</dt><dd class="mono">{{ company.reg_number }}</dd></div>
          <div v-if="company.founded"><dt>Est.</dt><dd>{{ company.founded }}</dd></div>
        </dl>
      </div>
    </div>
  </section>
</template>
<style scoped>
.inner { display: grid; grid-template-columns: 1.2fr .8fr; gap: clamp(24px, 4vw, 64px); align-items: start; }
.list { list-style: none; margin: 0; padding: 0; display: grid; gap: 10px; }
.list li { display: grid; grid-template-columns: auto 1fr auto; gap: 14px; align-items: center; padding: 14px 16px; border: 1px solid var(--line); border-radius: 16px; }
.ic { width: 40px; height: 40px; border-radius: 12px; background: var(--bg-alt); color: var(--blue-d); display: grid; place-items: center; }
.t { display: grid; } .t b { font-size: 15px; } .t a { color: var(--blue-d); font-weight: 600; } .t small { color: var(--muted); font-size: 13px; }
.cp { width: 36px; height: 36px; border-radius: 10px; color: var(--muted); display: grid; place-items: center; } .cp:hover { background: var(--bg-alt); }
.legal { gap: 8px; } .cn { color: var(--muted); }
dl { display: grid; gap: 8px; margin: 10px 0 0; } dl div { display: grid; grid-template-columns: 90px 1fr; gap: 8px; font-size: 14px; } dt { color: var(--muted); } dd { margin: 0; }
@media (max-width: 860px) { .inner { grid-template-columns: 1fr; } }
</style>
