import type { Bootstrap, PageData } from '~/utils/types'

/** Server-side we call the API by its internal URL, client-side by the public one. */
export function apiBase() {
  const cfg = useRuntimeConfig()
  return (import.meta.server ? (cfg.apiBase as string) : cfg.public.apiBase) .replace(/\/$/, '')
}

export function useBootstrap() {
  const { locale } = useI18n()
  return useAsyncData<Bootstrap>(`bootstrap-${locale.value}`, () => $fetch(`${apiBase()}/api/v2/${locale.value}/bootstrap`), { watch: [locale] })
}

export function usePage(slug: string) {
  const { locale } = useI18n()
  const route = useRoute()
  const preview = route.query.preview ? `?preview=${route.query.preview}` : ''
  return useAsyncData<PageData>(`page-${locale.value}-${slug || 'home'}`, () => $fetch(`${apiBase()}/api/v2/${locale.value}/pages/${slug}${preview}`), { watch: [locale] })
}

/** Resolve a media path: /media/* lives in the front's public dir, /storage/* on the API host. */
export function mediaUrl(src?: string | null) {
  if (!src) return ''
  if (/^https?:\/\//.test(src)) return src
  if (src.startsWith('/media/')) return src
  const cfg = useRuntimeConfig()
  const base = (cfg.public.mediaBase || cfg.public.apiBase) as string
  return `${base.replace(/\/$/, '')}/storage/${src.replace(/^\/?storage\//, '').replace(/^\//, '')}`
}
