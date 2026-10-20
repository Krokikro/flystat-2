const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch({ args: ['--allow-file-access-from-files', '--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: 1200, height: 900 } });
  p.on('console', m => console.log('console:', m.text()));
  p.on('pageerror', e => console.log('pageerror:', e.message));
  for (const pose of (process.argv[2] || 'hero,exploded,sensor').split(',')) {
    await p.goto('file://' + __dirname + '/index.html?pose=' + pose, { waitUntil: 'load' });
    await p.waitForFunction(() => window.__done, null, { timeout: 60000 });
    await p.waitForTimeout(1200);
    await p.screenshot({ path: __dirname + '/out_' + pose + '.png' });
    console.log('ok', pose);
  }
  await b.close();
})();
