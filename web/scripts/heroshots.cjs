// screenshots of the hero at given progress fractions of the hero section
const { chromium } = require('playwright');
const out = process.argv[2]; const path = process.argv[3] || '/en'; const w = +(process.argv[4] || 1440), h = +(process.argv[5] || 900);
const fr = (process.argv[6] || '0,0.15,0.3,0.42,0.55,0.68,0.8,0.92,1').split(',').map(Number);
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: w, height: h } });
  p.on('console', (m) => { if (m.type() === 'error') console.error('console:', m.text().slice(0, 200)); });
  await p.goto('http://127.0.0.1:3011' + path, { waitUntil: 'networkidle' });
  await p.waitForFunction(() => document.querySelector('.dev.ready'), null, { timeout: 240000 }).catch(() => console.error('scene not ready'));
  await p.evaluate(() => document.querySelector('.cb')?.remove());
  const H = await p.evaluate(() => { const s = document.querySelector('.hero'); return s.getBoundingClientRect().height - innerHeight; });
  for (let i = 0; i < fr.length; i++) {
    await p.evaluate((y) => window.scrollTo({ top: y }), Math.round(fr[i] * H)); await p.waitForTimeout(1200);
    await p.screenshot({ path: `${out}/h_${w}_${i}.png`, timeout: 90000 });
  }
  await b.close(); console.log('ok');
})();
