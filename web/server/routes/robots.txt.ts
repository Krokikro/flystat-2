export default defineEventHandler((event) => {
  setHeader(event, 'Content-Type', 'text/plain')
  const site = useRuntimeConfig(event).public.siteUrl
  if (process.env.SITE_MODE !== 'published') return 'User-agent: *\nDisallow: /\n'
  return `User-agent: *\nAllow: /\nDisallow: /*/thanks/\nSitemap: ${site}/sitemap.xml\n`
})
