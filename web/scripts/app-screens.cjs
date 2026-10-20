const { chromium } = require('playwright'); const sharp = require('sharp');
(async () => {
  const b = await chromium.launch({ args: ['--allow-file-access-from-files'] });
  const p = await b.newPage({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2 });
  await p.goto('file://' + __dirname + '/app-screens.html'); await p.waitForTimeout(800);
  for (const id of ['home', 'graph', 'events', 'alerts', 'reports', 'assistant']) {
    const el = await p.$('#' + id); const png = await el.screenshot();
    await sharp(png).webp({ quality: 84 }).toFile(`${__dirname}/../public/media/app/${id}.webp`); console.log('ok', id);
  }
  await b.close();
})();
