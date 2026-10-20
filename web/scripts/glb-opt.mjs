// Optimise the KeyShot GLB for the web: light per-mesh simplification (only the big shells),
// no welding (the embossed logo relies on split normals), webp textures, draco compression.
import { NodeIO } from '@gltf-transform/core';
import { ALL_EXTENSIONS } from '@gltf-transform/extensions';
import { dedup, prune, simplifyPrimitive, textureCompress, draco, meshopt } from '@gltf-transform/functions';
import { MeshoptSimplifier, MeshoptEncoder, MeshoptDecoder } from 'meshoptimizer';
import draco3d from 'draco3dgltf';
import sharp from 'sharp';
import fs from 'fs';
const [inp, out, ratioArg, errArg, mode] = process.argv.slice(2);
const io = new NodeIO().registerExtensions(ALL_EXTENSIONS).registerDependencies({ 'draco3d.encoder': await draco3d.createEncoderModule(), 'draco3d.decoder': await draco3d.createDecoderModule(), 'meshopt.encoder': MeshoptEncoder, 'meshopt.decoder': MeshoptDecoder });
const doc = await io.read(inp);
const tri = () => { let t = 0; for (const m of doc.getRoot().listMeshes()) for (const p of m.listPrimitives()) t += (p.getIndices()?.getCount() ?? p.getAttribute('POSITION').getCount()) / 3; return Math.round(t); };
console.log('in tris', tri());
for (const n of doc.getRoot().listNodes()) if (n.getCamera()) n.dispose();
await doc.transform(dedup());
if (mode !== 'nosimplify') {
  await MeshoptSimplifier.ready;
  const ratio = +ratioArg || 0.5, err = +(errArg || 0.0002);
  for (const m of doc.getRoot().listMeshes()) for (const p of m.listPrimitives()) {
    const t = (p.getIndices()?.getCount() ?? p.getAttribute('POSITION').getCount()) / 3;
    if (t > 8000) simplifyPrimitive(p, { simplifier: MeshoptSimplifier, ratio, error: err, lockBorder: true });
  }
}
await doc.transform(prune());
console.log('after simplify tris', tri());
await doc.transform(textureCompress({ encoder: sharp, targetFormat: 'webp', resize: [512, 512] }));
if (mode === 'draco') await doc.transform(draco({ method: 'edgebreaker', quantizePosition: +(process.env.QP||20), quantizeNormal: +(process.env.QN||16) }));
else if (mode !== 'nodraco') { await MeshoptEncoder.ready; await doc.transform(meshopt({ encoder: MeshoptEncoder, level: 'medium', quantizePosition: +(process.env.QP||16), quantizeNormal: +(process.env.QN||12) })); }
await io.write(out, doc);
console.log('out', out, (fs.statSync(out).size / 1024).toFixed(0), 'KB');
