/** IntersectionObserver-based reveal for .reveal elements inside a root. */
export function useReveal(root: Ref<HTMLElement | null>) {
  onMounted(() => {
    if (!root.value) return
    const els = root.value.querySelectorAll<HTMLElement>('.reveal')
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { els.forEach((e) => e.classList.add('is-in')); return }
    const io = new IntersectionObserver((entries) => entries.forEach((en) => { if (en.isIntersecting) { (en.target as HTMLElement).classList.add('is-in'); io.unobserve(en.target) } }), { rootMargin: '0px 0px -8% 0px', threshold: 0.08 })
    els.forEach((e, i) => { e.style.transitionDelay = `${Math.min(i % 6, 5) * 60}ms`; io.observe(e) })
    setTimeout(() => els.forEach((e) => e.classList.add('is-in')), 4000)
    onBeforeUnmount(() => io.disconnect())
  })
}
