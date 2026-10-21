// viewport screenshots at several scroll positions (realistic: lazy images, sticky hero, reveals)
const { chromium } = require('playwright');
const out = '/tmp/claude-0/-home-claude/10aeb560-254a-5529-b435-9e9e2992e54a/scratchpad/shots/';
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const [path, w, h, n] = [process.argv[2] || '/en', +(process.argv[3] || 1440), +(process.argv[4] || 900), +(process.argv[5] || 8)];
  const p = await b.newPage({ viewport: { width: w, height: h } });
  await p.goto('http://127.0.0.1:3011' + path + (path.includes('?') ? '&' : '?') + 'nospin=1', { waitUntil: 'load', timeout: 120000 }); await p.waitForTimeout(1500);
  await p.evaluate(() => document.querySelector('.cb')?.remove());
  const total = await p.evaluate(() => document.body.scrollHeight);
  const step = Math.floor((total - h) / (n - 1));
  const shots = [];
  for (let i = 0; i < n; i++) {
    await p.evaluate((y) => window.scrollTo({ top: y, behavior: 'instant' }), i * step); await p.waitForTimeout(900);
    await p.evaluate(async () => { await Promise.race([new Promise(r => setTimeout(r, 2500)), Promise.all(Array.from(document.images).filter(im => !im.complete).map(im => new Promise(r => { im.onload = im.onerror = r; })))]) });
    await p.waitForTimeout(300);
    const f = `${out}s_${w}_${i}.png`; await p.screenshot({ path: f, timeout: 90000 }); shots.push(f);
  }
  await b.close(); console.log(shots.length, 'shots', total);
})();
