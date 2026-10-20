const { chromium } = require('playwright');
(async () => { const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1440, height: 900 } });
p.on('console', m => console.log('console', m.type(), m.text().slice(0, 300)));
await p.goto('http://127.0.0.1:3011/en', { waitUntil: 'networkidle' }); await p.waitForTimeout(2500);
console.log(await p.evaluate(() => { const h = document.querySelector('header'); return { cls: h.className, dark: h.dataset.dark, scrolled: h.dataset.scrolled } }));
await b.close(); })();
