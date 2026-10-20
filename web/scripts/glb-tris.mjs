import { NodeIO } from '@gltf-transform/core';
const io = new NodeIO(); const doc = await io.read(process.argv[2]);
for (const m of doc.getRoot().listMeshes()) { let t=0; for (const p of m.listPrimitives()) t += (p.getIndices()?.getCount() ?? p.getAttribute('POSITION').getCount())/3; console.log(Math.round(t), m.getName(), m.listPrimitives().map(p=>p.getMaterial()?.getName()).join('|')); }
