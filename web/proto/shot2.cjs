const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch({ args: ['--allow-file-access-from-files','--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: 700, height: 900 } });
  for (const ry of ['-2.3','-1.5']) {
    await p.goto('file://' + __dirname + '/index.html?pose=hero&w=700&h=900&ry=' + ry, { waitUntil: 'load' });
    await p.waitForFunction(() => window.__done, null, { timeout: 60000 }); await p.waitForTimeout(800);
    await p.screenshot({ path: __dirname + '/ry' + ry + '.png' });
  }
  await b.close();
})();
