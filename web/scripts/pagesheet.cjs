// full-page screenshot of a route (reveals forced), downsized contact sheet in slices
const { chromium } = require('playwright'); const sharp = require('sharp');
const [path, out, w = '1440'] = process.argv.slice(2);
(async () => {
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader'] });
  const p = await b.newPage({ viewport: { width: +w, height: 900 } });
  await p.goto('http://127.0.0.1:3011' + path, { waitUntil: 'networkidle' });
  await p.waitForTimeout(800);
  await p.evaluate(() => { document.querySelector('.cb')?.remove(); document.querySelectorAll('.reveal').forEach(e => e.classList.add('is-in')); });
  // scroll through to trigger lazy images
  const H = await p.evaluate(() => document.body.scrollHeight);
  for (let y = 0; y < H; y += 700) { await p.evaluate((yy) => window.scrollTo(0, yy), y); await p.waitForTimeout(120); }
  await p.evaluate(() => window.scrollTo(0, 0)); await p.waitForTimeout(600);
  await p.screenshot({ path: out, fullPage: true, timeout: 120000 });
  const m = await sharp(out).metadata(); console.log('page', m.width, m.height);
  await b.close();
})();
