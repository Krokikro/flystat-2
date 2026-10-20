import { NodeIO } from '@gltf-transform/core';
import { ALL_EXTENSIONS } from '@gltf-transform/extensions';
const io = new NodeIO().registerExtensions(ALL_EXTENSIONS);
for (const f of process.argv.slice(2)) {
  const doc = await io.read(f); const root = doc.getRoot();
  console.log('==', f, 'meshes', root.listMeshes().length, 'nodes', root.listNodes().length, 'materials', root.listMaterials().length, 'textures', root.listTextures().length, 'anims', root.listAnimations().length);
  let tris = 0; for (const m of root.listMeshes()) for (const p of m.listPrimitives()) { const i = p.getIndices(); tris += (i ? i.getCount() : p.getAttribute('POSITION').getCount()) / 3; }
  console.log('tris', tris);
  const walk = (n, d) => { const m = n.getMesh(); const t = n.getTranslation(); console.log(' '.repeat(d) + n.getName() + (m ? ` [mesh ${m.getName()} prims ${m.listPrimitives().length} mat ${m.listPrimitives().map(p=>p.getMaterial()?.getName()).join('|')}]` : '') + ` t=${t.map(v=>v.toFixed(2))}`); for (const c of n.listChildren()) walk(c, d + 1); };
  for (const s of root.listScenes()) for (const n of s.listChildren()) walk(n, 0);
  for (const m of root.listMaterials()) console.log('MAT', m.getName(), m.getBaseColorFactor().map(v=>v.toFixed(2)), 'metal', m.getMetallicFactor(), 'rough', m.getRoughnessFactor(), 'alpha', m.getAlphaMode(), m.getBaseColorTexture() ? 'tex' : '');
  for (const t of root.listTextures()) console.log('TEX', t.getName(), t.getMimeType(), t.getSize(), t.getImage().byteLength);
  for (const a of root.listAnimations()) console.log('ANIM', a.getName(), a.listChannels().length);
}
