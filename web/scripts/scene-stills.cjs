// Product stills from the real model, transparent background, for the light sections of the site.
const { chromium } = require('playwright'); const { serve } = require('./glb-render.cjs'); const fs = require('fs'); const path = require('path');
const STILLS = {
  // name: [w, h, override]
  'applicator-front': [1400, 1800, { p: 0, cam: { az: 22, el: 8, dist: 2.7, ty: 0, shift: 0 } }],
  'applicator-angle': [1400, 1800, { p: 0, cam: { az: 58, el: 14, dist: 2.7, ty: 0, shift: 0 } }],
  'applicator-back': [1400, 1800, { p: 0, cam: { az: 200, el: 10, dist: 2.7, ty: 0, shift: 0 } }],
  'applicator-top': [1600, 1600, { p: 0, cam: { az: 35, el: 62, dist: 2.9, ty: 0, shift: 0 } }],
  'open': [1600, 1800, { p: 0.5, cam: { az: 40, el: 10, dist: 4.4, ty: 0.05, shift: 0 }, explode: { applicator: 45, needle: 45 } }],
  'exploded': [1600, 1800, { p: 0.75, cam: { az: 35, el: 12, dist: 5.2, ty: 0.15, shift: 0 }, explode: { applicator: 75, needle: 75, housing: 20, internals: 10, patch: -8, cap: -12 } }],
  'sensor': [1600, 1200, { p: 1, cam: { az: 40, el: 24, dist: 0.72, ty: -0.33, shift: 0 }, explode: { applicator: 400, needle: 400 }, hide: ['cap'] }],
  'sensor-top': [1600, 1600, { p: 1, cam: { az: 20, el: 72, dist: 1.05, ty: -0.33, shift: 0 }, explode: { applicator: 400, needle: 400 }, hide: ['cap'] }],
  'sensor-side': [1600, 800, { p: 1, cam: { az: 90, el: 6, dist: 0.72, ty: -0.33, shift: 0 }, explode: { applicator: 400, needle: 400 }, hide: ['cap'] }],
  'sensor-exploded': [1600, 1800, { p: 0.75, cam: { az: 35, el: 14, dist: 1.0, ty: -0.3, shift: 0 }, explode: { applicator: 400, needle: 400, housing: 18, internals: 9, patch: -7 }, hide: ['cap'] }],
};
(async () => {
  const outDir = process.argv[2]; fs.mkdirSync(outDir, { recursive: true });
  const only = process.argv[3] ? process.argv[3].split(',') : Object.keys(STILLS);
  const srv = await serve(); const port = srv.address().port;
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  for (const name of only) {
    const [w, h, ov] = STILLS[name];
    const p = await b.newPage({ viewport: { width: w, height: h } });
    p.on('console', (m) => { if (m.type() === 'error') console.error('console:', m.text().slice(0, 200)); });
    await p.goto(`http://127.0.0.1:${port}/web/scripts/scene-view.html?w=${w}&h=${h}&bg=none`);
    await p.waitForFunction(() => window.__ready || window.__error, null, { timeout: 300000 });
    await p.evaluate((o) => window.__renderCustom(o), ov);
    await p.screenshot({ path: path.join(outDir, name + '.png'), omitBackground: true });
    await p.close(); console.log('ok', name);
  }
  await b.close(); srv.close();
})().catch((e) => { console.error(e); process.exit(1); });
