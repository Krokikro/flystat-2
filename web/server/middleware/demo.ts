/**
 * While the site is in demo mode we keep it closed: optional basic auth (BASIC_AUTH="user:pass")
 * and X-Robots-Tag noindex on every response. Health checks and the revalidate hook stay open.
 */
export default defineEventHandler((event) => {
  const url = getRequestURL(event)
  if (url.pathname.startsWith('/api/') || url.pathname === '/robots.txt' || url.pathname.startsWith('/_nuxt') || url.pathname.startsWith('/media/') || url.pathname.startsWith('/fonts/')) return
  const auth = useRuntimeConfig(event).basicAuth as string
  if (auth) {
    const h = getHeader(event, 'authorization') || ''
    const ok = h.startsWith('Basic ') && Buffer.from(h.slice(6), 'base64').toString() === auth
    if (!ok) { setHeader(event, 'WWW-Authenticate', 'Basic realm="Flystat demo"'); throw createError({ statusCode: 401, statusMessage: 'Authentication required' }) }
  }
  if (process.env.SITE_MODE !== 'published') setHeader(event, 'X-Robots-Tag', 'noindex, nofollow, noarchive')
})
