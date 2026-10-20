const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader'] });
  const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
  p.on('console', m => console.log('console', m.type(), m.text().slice(0, 300)));
  p.on('pageerror', e => console.log('pageerror', e.message.slice(0, 300)));
  await p.goto('http://127.0.0.1:3011/en', { waitUntil: 'networkidle' });
  await p.waitForTimeout(2000);
  console.log(await p.evaluate(() => ({ reveal: document.querySelectorAll('.reveal').length, isin: document.querySelectorAll('.reveal.is-in').length, hydrated: !!window.useNuxtApp || !!document.querySelector('#__nuxt')?.__vue_app__, hdr: document.querySelector('header')?.className })));
  await p.evaluate(() => window.scrollTo(0, 3000)); await p.waitForTimeout(1500);
  console.log(await p.evaluate(() => ({ isin: document.querySelectorAll('.reveal.is-in').length, y: window.scrollY })));
  await b.close();
})();
