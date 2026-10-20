/** Small helpers around gsap + ScrollTrigger for block choreography.
 *  gsap is loaded lazily on the client only; everything is a no-op when the user prefers reduced motion. */
export const reducedMotion = () => typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches

let lib: Promise<{ gsap: any; ScrollTrigger: any }> | null = null
export function loadGsap() {
  if (!lib) lib = Promise.all([import('gsap'), import('gsap/ScrollTrigger')]).then(([g, s]) => { g.gsap.registerPlugin(s.ScrollTrigger); return { gsap: g.gsap, ScrollTrigger: s.ScrollTrigger } })
  return lib
}

/** Run `fn` with gsap once mounted (client, motion allowed). Whatever `fn` returns (tween, timeline, ScrollTrigger or an array of them) is killed on unmount. */
export function useScrollFx(fn: (g: { gsap: any; ScrollTrigger: any }) => any) {
  const kills: any[] = []
  onMounted(async () => {
    if (reducedMotion()) return
    const g = await loadGsap()
    const r = fn(g)
    if (r) kills.push(...(Array.isArray(r) ? r : [r]))
  })
  onBeforeUnmount(() => kills.forEach((k) => k?.scrollTrigger?.kill?.() ?? k?.kill?.()))
}

/** Vertical parallax on scroll: element moves from +amount to -amount while it crosses the viewport. */
export function useParallax(el: Ref<HTMLElement | null>, amount = 40, trigger?: Ref<HTMLElement | null>) {
  useScrollFx(({ gsap }) => {
    if (!el.value) return
    return gsap.fromTo(el.value, { y: amount }, { y: -amount, ease: 'none', scrollTrigger: { trigger: trigger?.value || el.value, start: 'top bottom', end: 'bottom top', scrub: 0.6 } })
  })
}
