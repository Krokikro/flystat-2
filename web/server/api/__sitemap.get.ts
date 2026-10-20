/** Feeds @nuxtjs/sitemap with every published page / news / legal doc in every locale, with hreflang alternates. */
export default defineEventHandler(async (event) => {
  const cfg = useRuntimeConfig(event)
  if (process.env.SITE_MODE !== 'published') return []
  const locales = ['en', 'ru', 'fr', 'es', 'it', 'de', 'zh', 'vi', 'hi']
  const data = await $fetch<any>(`${cfg.apiBase}/api/v2/sitemap`).catch(() => ({ pages: [], news: [], docs: [] }))
  const out: any[] = []
  const alts = (f: (l: string) => string) => locales.map((l) => ({ hreflang: l, href: f(l) })).concat([{ hreflang: 'x-default', href: f('en') }])
  for (const p of data.pages || []) for (const l of locales) out.push({ loc: `/${l}${p.slug ? '/' + p.slug : ''}`, lastmod: p.updated_at, alternatives: alts((x) => `/${x}${p.slug ? '/' + p.slug : ''}`) })
  for (const n of data.news || []) for (const l of locales) { const s = n.slugs?.[l] || n.slugs?.en; if (s) out.push({ loc: `/${l}/news/${s}`, lastmod: n.updated_at, alternatives: alts((x) => `/${x}/news/${n.slugs?.[x] || n.slugs?.en}`) }) }
  for (const d of data.docs || []) for (const l of locales) out.push({ loc: `/${l}/legal/${d.slug}`, lastmod: d.updated_at, alternatives: alts((x) => `/${x}/legal/${d.slug}`) })
  return out
})
