<script setup lang="ts">
const props = defineProps<{ data: Record<string, any> }>()
const active = ref(0)
const screens = computed(() => props.data.screens || [])
</script>
<template>
  <section class="section app band-purple">
    <div class="container">
      <UiSectionHead :eyebrow="data.eyebrow" :title="data.title" :text="data.text" />
      <div class="layout">
        <ol class="feats">
          <li v-for="(s, i) in screens" :key="i" :class="{ on: active === i }" v-reveal="i * 60"><button @click="active = i"><span class="n mono">{{ String(i + 1).padStart(2, '0') }}</span><span class="t"><b>{{ s.title }}</b><small>{{ s.text }}</small></span></button></li>
        </ol>
        <div class="phone" v-reveal="150">
          <div class="frame">
            <TransitionGroup name="scr">
              <img v-for="(s, i) in screens" v-show="active === i" :key="i" :src="mediaUrl(s.image?.src)" :alt="s.image?.alt || s.title" loading="lazy">
            </TransitionGroup>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.band-purple { background: linear-gradient(180deg, #fff 0%, #F3F0FF 100%); }
.band-purple .eyebrow { color: var(--purple-d); }
.layout { display: grid; grid-template-columns: 1fr 380px; gap: clamp(28px, 5vw, 72px); align-items: center; }
.feats { list-style: none; margin: 0; padding: 0; display: grid; gap: 6px; }
.feats button { width: 100%; text-align: left; display: grid; grid-template-columns: 36px 1fr; gap: 10px; padding: 16px 18px; border-radius: 16px; transition: background .3s; }
.feats li.on button { background: #fff; box-shadow: var(--shadow); }
.n { color: var(--purple-d); padding-top: 3px; }
.t { display: grid; gap: 4px; } .t b { font-size: 1.0625rem; letter-spacing: -.01em; } .t small { font-size: 0.875rem; color: var(--ink-2); line-height: 1.45; }
.phone { justify-self: center; }
.frame { width: 300px; aspect-ratio: 9/19.5; border-radius: 44px; background: #0B1A3A; padding: 10px; box-shadow: 0 40px 80px rgba(89,37,220,.25), inset 0 0 0 2px rgba(255,255,255,.08); position: relative; overflow: hidden; }
.frame img { position: absolute; inset: 10px; width: calc(100% - 20px); height: calc(100% - 20px); object-fit: cover; border-radius: 36px; background: #fff; }
.scr-enter-active, .scr-leave-active { transition: opacity .35s, transform .35s; } .scr-enter-from { opacity: 0; transform: translateY(12px); } .scr-leave-to { opacity: 0; }
@media (max-width: 860px) { .layout { grid-template-columns: 1fr; } .phone { order: -1; } .frame { width: 240px; } }
</style>
