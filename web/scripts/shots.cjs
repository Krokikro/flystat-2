const { chromium } = require('playwright');
const out = '/tmp/claude-0/-home-claude/10aeb560-254a-5529-b435-9e9e2992e54a/scratchpad/shots/';
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const pages = (process.argv[2] || '/en,/en/device,/en/partners').split(',');
  for (const vp of [{ w: 1440, h: 900, n: 'd' }, { w: 390, h: 844, n: 'm' }]) {
    const p = await b.newPage({ viewport: { width: vp.w, height: vp.h }, deviceScaleFactor: 1 });
    p.on('pageerror', e => console.log('pageerror', e.message.slice(0, 200)));
    p.on('console', m => { if (m.type() === 'error') console.log('console.error', m.text().slice(0, 200)) });
    for (const path of pages) {
      await p.goto('http://127.0.0.1:3011' + path, { waitUntil: 'networkidle', timeout: 60000 });
      await p.waitForTimeout(1500);
      const name = path.replace(/\//g, '_') || '_home';
      await p.screenshot({ path: `${out}${vp.n}${name}_top.png` });
      // scroll through to trigger reveals and hero progress, then full page
      const h = await p.evaluate(() => document.body.scrollHeight);
      for (let y = 0; y < h; y += vp.h * 0.8) { await p.evaluate((yy) => window.scrollTo(0, yy), y); await p.waitForTimeout(120); }
      await p.evaluate(() => { document.querySelectorAll('.reveal').forEach(e => e.classList.add('is-in')); document.querySelector('.cb')?.remove(); window.scrollTo(0, 0) }); await p.waitForTimeout(900);
      await p.screenshot({ path: `${out}${vp.n}${name}_full.png`, fullPage: true });
      console.log('ok', vp.n, path, h);
    }
    await p.close();
  }
  await b.close();
})();
