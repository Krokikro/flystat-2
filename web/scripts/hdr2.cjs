const { chromium } = require('playwright');
(async () => { const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
await p.goto('http://127.0.0.1:3011/en', { waitUntil: 'networkidle' }); await p.waitForTimeout(2500);
console.log(await p.evaluate(() => { const h = document.querySelector('header'); const c = h.__vueParentComponent; const s = c?.setupState || {}; return { keys: Object.keys(s).slice(0, 20), dark: s.dark, scrolled: s.scrolled, nuxt: typeof window.__NUXT__, hdrdark: document.documentElement.outerHTML.includes('header-dark') } }));
await b.close(); })();
