const { chromium } = require('playwright');
(async () => { const b = await chromium.launch({ args: ['--use-gl=angle','--use-angle=swiftshader','--enable-unsafe-swiftshader'] }); const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
p.on('console', m => { if (['error','warning'].includes(m.type())) console.log('console', m.type(), m.text().slice(0, 400)) });
await p.goto('http://127.0.0.1:3011/en', { waitUntil: 'networkidle' }); await p.waitForTimeout(3000);
console.log(await p.evaluate(() => ({ cls: document.querySelector('header').className, color: getComputedStyle(document.querySelector('header')).color, hero: !!document.querySelector('#main > .page > .hero'), sh: document.documentElement.scrollHeight, bodyOv: getComputedStyle(document.body).overflowY, htmlOv: getComputedStyle(document.documentElement).overflowY })));
await p.evaluate(() => window.scrollTo({ top: 1200, behavior: 'instant' })); await p.waitForTimeout(800);
console.log(await p.evaluate(() => ({ y: window.scrollY, cls: document.querySelector('header').className })));
await b.close(); })();
