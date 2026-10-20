// Precompute SVG paths per ISO alpha-2 country (Natural Earth 110m, equirectangular-ish Robinson-like via geoNaturalEarth1)
import { createRequire } from 'module';
import { writeFileSync } from 'fs';
const require = createRequire(import.meta.url);
const topo = require('world-atlas/countries-110m.json');
const { feature } = require('topojson-client');
const { geoNaturalEarth1, geoPath } = require('d3-geo');
const iso = require('i18n-iso-countries');
const fc = feature(topo, topo.objects.countries);
const proj = geoNaturalEarth1().fitSize([1000, 520], { type: 'Sphere' });
const path = geoPath(proj);
const out = {};
for (const f of fc.features) {
  const a2 = iso.numericToAlpha2(String(f.id).padStart(3, '0'));
  if (!a2) continue;
  const d = path(f); if (!d) continue;
  const c = path.centroid(f);
  out[a2] = { d: d.replace(/(\d+\.\d{1})\d+/g, '$1'), c: [Math.round(c[0]), Math.round(c[1])] };
}
writeFileSync(new URL('../app/assets/world.json', import.meta.url), JSON.stringify({ w: 1000, h: 520, sphere: path({ type: 'Sphere' }).replace(/(\d+\.\d{1})\d+/g, '$1'), countries: out }));
console.log(Object.keys(out).length, 'countries');
