// Bake frames of the hero story with the same module the site uses.
// usage: node scripts/scene-render.cjs <outDir> <count|p1,p2,..> [w h] [bg=none]
const { chromium } = require('playwright'); const { serve } = require('./glb-render.cjs'); const path = require('path'); const fs = require('fs');
(async () => {
  const [outDir, spec, w = '1400', h = '900', bg = ''] = process.argv.slice(2);
  fs.mkdirSync(outDir, { recursive: true });
  const ps = spec.includes(',') || spec.includes('.') ? spec.split(',').map(Number) : Array.from({ length: +spec }, (_, i) => i / (+spec - 1));
  const srv = await serve(); const port = srv.address().port;
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: +w, height: +h } });
  p.on('console', (m) => { if (m.type() === 'error') console.error('console:', m.text()); });
  await p.goto(`http://127.0.0.1:${port}/web/scripts/scene-view.html?w=${w}&h=${h}${bg ? '&bg=none' : ''}`);
  await p.waitForFunction(() => window.__ready || window.__error, null, { timeout: 300000 });
  const err = await p.evaluate(() => window.__error); if (err) throw new Error(err);
  const meta = [];
  for (let i = 0; i < ps.length; i++) {
    const info = await p.evaluate((pp) => window.__renderAt(pp), ps[i]);
    const file = path.join(outDir, `f_${String(i).padStart(3, '0')}.png`);
    await p.screenshot({ path: file, omitBackground: !!bg });
    meta.push({ i, p: ps[i], ...info }); process.stdout.write(`\r${i + 1}/${ps.length}`);
  }
  fs.writeFileSync(path.join(outDir, 'meta.json'), JSON.stringify(meta));
  console.log('\ndone', outDir); await b.close(); srv.close();
})().catch((e) => { console.error(e); process.exit(1); });
