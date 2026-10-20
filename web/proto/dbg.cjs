const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: 600, height: 400 } });
  p.on('console', m => console.log('console:', m.text()));
  p.on('pageerror', e => console.log('pageerror:', e.message));
  p.on('requestfailed', r => console.log('reqfail:', r.url()));
  await p.goto('file://' + __dirname + '/index.html?pose=hero', { waitUntil: 'load' });
  await p.waitForTimeout(4000);
  console.log('done?', await p.evaluate(() => window.__done));
  console.log('webgl', await p.evaluate(() => { const c=document.createElement('canvas'); return !!c.getContext('webgl2'); }));
  await b.close();
})();
