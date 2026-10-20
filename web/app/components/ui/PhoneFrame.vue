<script setup lang="ts">
/** CSS phone chassis. Width is driven by the parent via `--pw` (defaults to 100% of the parent); everything inside scales with it. */
defineProps<{ dark?: boolean }>()
</script>
<template>
  <div class="phone" :class="{ dark }" aria-hidden="false">
    <i class="k k1" /><i class="k k2" /><i class="k k3" /><i class="k k4" />
    <div class="chassis">
      <div class="screen"><slot /></div>
      <i class="island" />
    </div>
  </div>
</template>
<style scoped>
.phone { --pw: 100%; position: relative; width: var(--pw); aspect-ratio: 9 / 19.2; container-type: inline-size; }
.chassis { position: absolute; inset: 0; border-radius: 15.5cqw; background: linear-gradient(160deg, #1b2b4d 0%, #0a1733 55%, #050d22 100%); padding: 3.2cqw; box-shadow: 0 0 0 .5cqw rgba(255,255,255,.08) inset, 0 0 0 1.1cqw #0a1733 inset, 0 1cqw 2cqw rgba(0,0,0,.25) inset, 0 50px 90px -20px rgba(6, 38, 77, .45), 0 20px 40px -20px rgba(6, 38, 77, .35); }
.screen { position: absolute; inset: 3.2cqw; border-radius: 12.5cqw; overflow: hidden; background: #fff; isolation: isolate; }
.screen :deep(img) { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: top; }
.island { position: absolute; top: 5.4cqw; left: 50%; transform: translateX(-50%); width: 27cqw; height: 7.4cqw; border-radius: 999px; background: #050b1a; box-shadow: 0 0 0 .6cqw rgba(0,0,0,.35); z-index: 2; }
.k { position: absolute; width: 1.2cqw; background: linear-gradient(90deg, #243555, #0b1733); border-radius: 1cqw; }
.k1 { left: -1cqw; top: 19cqw; height: 7cqw; } .k2 { left: -1cqw; top: 30cqw; height: 13cqw; } .k3 { left: -1cqw; top: 46cqw; height: 13cqw; } .k4 { right: -1cqw; top: 34cqw; height: 20cqw; }
</style>
