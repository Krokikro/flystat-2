const LOCALES = ['en', 'ru', 'fr', 'es', 'it', 'de', 'zh', 'vi', 'hi'] as const

export default defineNuxtConfig({
  compatibilityDate: '2026-10-01',
  devtools: { enabled: false },
  ssr: true,
  modules: ['@nuxtjs/i18n', '@nuxt/image', '@nuxt/fonts', '@nuxtjs/sitemap'],
  css: ['~/assets/css/main.css'],
  app: {
    head: {
      htmlAttrs: { lang: 'en' },
      meta: [{ name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover' }, { name: 'theme-color', content: '#06264D' }],
      link: [{ rel: 'icon', href: '/favicon.svg', type: 'image/svg+xml' }, { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' }],
    },
    pageTransition: { name: 'page', mode: 'out-in' },
  },
  runtimeConfig: {
    apiBase: process.env.API_BASE_INTERNAL || process.env.API_BASE || 'http://127.0.0.1:8000',
    revalidateSecret: process.env.REVALIDATE_SECRET || '',
    basicAuth: process.env.BASIC_AUTH || '',          // "user:pass" — protects every page while in demo
    public: {
      apiBase: process.env.API_BASE || 'http://127.0.0.1:8000',
      siteUrl: process.env.SITE_URL || 'http://localhost:3000',
      mediaBase: process.env.MEDIA_BASE || '',          // where /storage uploads from the admin live (API origin)
    },
  },
  i18n: {
    locales: LOCALES.map((code) => ({ code, language: ({ en: 'en', ru: 'ru', fr: 'fr', es: 'es', it: 'it', de: 'de', zh: 'zh-Hans', vi: 'vi', hi: 'hi' } as Record<string, string>)[code], file: `${code}.json`, dir: 'ltr' })),
    defaultLocale: 'en',
    strategy: 'prefix',
    lazy: true,
    langDir: 'locales',
    detectBrowserLanguage: { useCookie: true, cookieKey: 'flystat_lang', redirectOn: 'root', alwaysRedirect: false, fallbackLocale: 'en' },
    baseUrl: process.env.SITE_URL || 'http://localhost:3000',
  },
  fonts: {
    families: [
      { name: 'ALS Hauss', provider: 'local', weights: [400, 500, 700, 900] },
      { name: 'Noto Sans SC', provider: 'google', weights: [400, 500, 700, 900], subsets: ['chinese-simplified'] },
      { name: 'Noto Sans Devanagari', provider: 'google', weights: [400, 500, 700, 900], subsets: ['devanagari'] },
      { name: 'JetBrains Mono', provider: 'google', weights: [400, 500] },
    ],
  },
  image: { domains: [], format: ['webp'], quality: 80 },
  site: { url: process.env.SITE_URL || 'http://localhost:3000', name: 'Flystat' },
  sitemap: { sources: ['/api/__sitemap'], exclude: ['/*/thanks/**'] },
  routeRules: {
    '/**': { isr: 60 },
    '/api/**': { isr: false, cache: false },
  },
  nitro: { compressPublicAssets: true, prerender: { crawlLinks: false } },
  vite: { optimizeDeps: { include: ['gsap', 'gsap/ScrollTrigger', 'three'] } },
})
