export default defineNuxtPlugin((nuxtApp) => {
  // v-reveal: adds .reveal and animates in when visible
  nuxtApp.vueApp.directive('reveal', {
    mounted(el: HTMLElement, binding) {
      el.classList.add('reveal')
      if (binding.value) el.style.transitionDelay = `${binding.value}ms`
      if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { el.classList.add('is-in'); return }
      const io = new IntersectionObserver((entries) => entries.forEach((en) => { if (en.isIntersecting) { el.classList.add('is-in'); io.disconnect() } }), { rootMargin: '0px 0px -8% 0px', threshold: 0.08 })
      io.observe(el)
      setTimeout(() => el.classList.add('is-in'), 4000) // safety: never leave content hidden
    },
    getSSRProps() { return {} },
  })
})
