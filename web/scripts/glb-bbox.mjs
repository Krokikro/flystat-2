import { NodeIO } from '@gltf-transform/core';
import { ALL_EXTENSIONS } from '@gltf-transform/extensions';
import { getBounds } from '@gltf-transform/core';
const io = new NodeIO().registerExtensions(ALL_EXTENSIONS);
for (const f of process.argv.slice(2)) {
  const doc = await io.read(f); const root = doc.getRoot();
  console.log('==', f);
  const scene = root.listScenes()[0];
  const b = getBounds(scene); console.log('scene bounds', b.min.map(v=>v.toFixed(4)), b.max.map(v=>v.toFixed(4)));
  const want = ['Аппликатор','Датчик','Корпус датчика','Внутренние компоненты','Patch and Glue','(Clear)','(Pantone 659 C translucence + Clear)','(Silver-white or bright silver)','(White+Clear)','SOFT','PCB','Board'];
  const walk = (n, path) => { const name = n.getName(); if (want.includes(name) && n.listChildren().length) { const bb = getBounds(n); console.log(path + '/' + name, 'min', bb.min.map(v=>v.toFixed(4)).join(','), 'max', bb.max.map(v=>v.toFixed(4)).join(','), 'size', bb.max.map((v,i)=>(v-bb.min[i]).toFixed(4)).join(',')); } for (const c of n.listChildren()) walk(c, path + '/' + name); };
  for (const n of scene.listChildren()) walk(n, '');
  const cam = root.listNodes().find(n => n.getCamera()); if (cam) console.log('camera', cam.getName(), cam.getWorldTranslation().map(v=>v.toFixed(4)), cam.getWorldRotation().map(v=>v.toFixed(2)));
}
