export function useConsent() {
  const cookie = useCookie<'all' | 'necessary' | undefined>('flystat_consent', { maxAge: 60 * 60 * 24 * 365, sameSite: 'lax' })
  const decided = computed(() => !!cookie.value)
  const analytics = computed(() => cookie.value === 'all')
  return { cookie, decided, analytics, accept: () => (cookie.value = 'all'), decline: () => (cookie.value = 'necessary'), reset: () => (cookie.value = undefined) }
}
