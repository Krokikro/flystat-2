<script setup lang="ts">
/** Pull quote. Data: { text, author, role, image? }. */
defineProps<{ data: Record<string, any> }>()
</script>
<template>
  <section class="section tq">
    <div class="container">
      <blockquote class="q" v-reveal>
        <span class="mark" aria-hidden="true">“</span>
        <p class="txt">{{ data.text }}</p>
        <footer>
          <UiPic v-if="data.image?.src" :img="data.image" class="ava" />
          <span v-else class="ava ini" aria-hidden="true">{{ String(data.author || 'F').trim().charAt(0) }}</span>
          <span class="who"><b>{{ data.author }}</b><span>{{ data.role }}</span></span>
        </footer>
      </blockquote>
    </div>
  </section>
</template>
<style scoped>
.q { margin: 0; position: relative; display: grid; gap: clamp(22px, 3vw, 36px); max-width: 1040px; margin-inline: auto; padding: clamp(32px, 5vw, 80px) clamp(24px, 5vw, 96px); border-radius: var(--radius-lg); background: var(--warm-2); overflow: hidden; }
.mark { position: absolute; left: clamp(12px, 3vw, 48px); top: -.1em; font-size: clamp(7rem, 14vw, 15rem); line-height: 1; font-weight: 900; color: rgba(16, 42, 86, .07); pointer-events: none; letter-spacing: -.05em; }
.txt { position: relative; font-size: clamp(1.4rem, 1rem + 1.6vw, 2.6rem); font-weight: 500; letter-spacing: -.02em; line-height: 1.3; text-wrap: balance; color: var(--ink); }
footer { display: flex; gap: 16px; align-items: center; }
.ava { width: 64px; height: 64px; border-radius: 50%; overflow: hidden; flex: none; }
.ini { display: grid; place-items: center; background: var(--ink); color: #fff; font-weight: 900; font-size: 1.4rem; }
.who { display: grid; gap: 2px; font-size: 1rem; } .who b { font-size: 1.1rem; } .who span { color: var(--muted); }
</style>
