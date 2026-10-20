// Headless renders of GLB views via Playwright + swiftshader.
// usage: node scripts/glb-render.cjs out.png "file=/assets3d/Flystat_01.glb&az=35&el=15" [w h]
const { chromium } = require('playwright');
const http = require('http');
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..', '..'); // repo root (serves /web/node_modules and /assets3d)
const MIME = { '.js': 'text/javascript', '.html': 'text/html', '.glb': 'model/gltf-binary', '.hdr': 'application/octet-stream', '.exr': 'application/octet-stream', '.wasm': 'application/wasm', '.json': 'application/json' };
function serve() {
  return new Promise((res) => {
    const srv = http.createServer((req, rsp) => {
      let p = decodeURIComponent(req.url.split('?')[0]);
      if (p.startsWith('/node_modules/')) p = '/web' + p;
      if (p === '/view') p = '/web/scripts/glb-view.html';
      const f = path.join(ROOT, p);
      if (!fs.existsSync(f) || fs.statSync(f).isDirectory()) { rsp.writeHead(404); return rsp.end(); }
      rsp.writeHead(200, { 'Content-Type': MIME[path.extname(f)] || 'application/octet-stream' });
      fs.createReadStream(f).pipe(rsp);
    }).listen(0, () => res(srv));
  });
}
module.exports = { serve };

async function render(out, params, w = 1600, h = 900) {
  const srv = await serve(); const port = srv.address().port;
  const b = await chromium.launch({ args: ['--use-gl=angle', '--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] });
  const p = await b.newPage({ viewport: { width: w, height: h } });
  p.on('console', m => { if (m.type() === 'error') console.error('console:', m.text()); });
  await p.goto(`http://127.0.0.1:${port}/view?w=${w}&h=${h}&${params}`);
  await p.waitForFunction(() => window.__ready || window.__error, null, { timeout: 180000 });
  const err = await p.evaluate(() => window.__error); if (err) throw new Error(err);
  await p.screenshot({ path: out, omitBackground: true });
  await b.close(); srv.close();
}
if (require.main === module) {
  const [out, params, w, h] = process.argv.slice(2);
  render(out, params, w ? +w : undefined, h ? +h : undefined).then(() => console.log('ok', out)).catch(e => { console.error(e); process.exit(1); });
}
