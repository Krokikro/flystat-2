// Procedural Flystat device: applicator (body of taper with superellipse section + thumb groove),
// sensor (lathe) and base (lathe, translucent). Units: millimetres, scaled by caller.
import * as THREE from 'three';

const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
const smooth = (t) => t * t * (3 - 2 * t);

export const DIM = { W: 31, D: 27.5, H: 66, sensorR: 17.5, sensorH: 5.4 };

/** half-width scale of the body at normalised height t (0 bottom → 1 top) */
function taper(t) {
  if (t < 0.3) return 1;
  const k = (t - 0.3) / 0.7;              // 0..1
  const e = k * k * (2 - k);               // ease: gentle start, near-linear end
  return 1 - 0.3 * e;                      // 1 → 0.70 at the very top
}
/** top dome: radial shrink near the top so the crown is softly rounded */
function crown(t) {
  const r = 0.045;                         // rounding zone (fraction of height)
  if (t < 1 - r) return 1;
  const k = (t - (1 - r)) / r;             // 0..1
  return Math.sqrt(Math.max(0, 1 - k * k)) * 0.5 + 0.5 - 0.5 * k * k; // ellipse-ish
}
/** thumb scoop on the front (+x) face: U-shaped region in (angle, height) space with a crisp lip and a concave floor */
function groove(t, ang) {
  const y0 = 0.56;                          // bottom of the U
  const A0 = 1.3;                          // half-width of the scoop in radians at full width
  if (t < y0) return 0;
  let a = Math.atan2(Math.sin(ang), Math.cos(ang));   // -π..π, 0 = front centre
  a = Math.abs(a);
  const k = clamp((t - y0) / 0.2, 0, 1);  // U narrows towards its round bottom
  const A = A0 * Math.sqrt(k);
  if (A <= 0 || a > A) return 0;
  const q = a / A;                          // 0 centre → 1 rim
  const floor = Math.pow(Math.max(0, 1 - q * q), 0.35);   // concave, fairly flat floor
  const lip = smooth(clamp((1 - q) / 0.1, 0, 1));       // crisp but not hard lip
  const topFade = t > 0.975 ? clamp((1 - t) / 0.025, 0, 1) : 1;   // thin rim stays at the top
  return Math.max(floor, 0.35) * lip * topFade;
}

export function applicatorGeometry({ W = DIM.W, D = DIM.D, H = DIM.H, n = 2.7, rings = 200, segs = 160 } = {}) {
  const pos = [], nor = [], uv = [], idx = [];
  const a0 = W / 2, b0 = D / 2;
  for (let i = 0; i <= rings; i++) {
    const t = i / rings;
    const s = taper(t) * crown(t);
    const g0 = 3.0; // groove depth mm
    for (let j = 0; j <= segs; j++) {
      const ang = (j / segs) * Math.PI * 2;
      const c = Math.cos(ang), sn = Math.sin(ang);
      // superellipse radius in direction ang
      const r = 1 / Math.pow(Math.pow(Math.abs(c / a0), n) + Math.pow(Math.abs(sn / b0), n), 1 / n);
      let rr = r * s - g0 * groove(t, ang);
      if (t < 0.012) rr *= 0.985; // tiny foot chamfer
      const y = t * (H - 2.5 * Math.max(0, c));   // crown slopes down toward the groove side
      pos.push(rr * c, y, rr * sn);
      uv.push(j / segs, t);
    }
  }
  for (let i = 0; i < rings; i++) for (let j = 0; j < segs; j++) {
    const a = i * (segs + 1) + j, b = a + segs + 1;
    idx.push(a, b, a + 1, b, b + 1, a + 1);
  }
  // top cap (fan)
  const capCenter = pos.length / 3; pos.push(0, H, 0); nor.push(0, 1, 0); uv.push(0.5, 1);
  const ringStart = rings * (segs + 1);
  for (let j = 0; j < segs; j++) idx.push(ringStart + j, capCenter, ringStart + j + 1);
  // bottom cap
  const botCenter = pos.length / 3; pos.push(0, 0, 0); uv.push(0.5, 0);
  for (let j = 0; j < segs; j++) idx.push(j + 1, botCenter, j);
  const g = new THREE.BufferGeometry();
  g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
  g.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
  g.setIndex(idx);
  g.computeVertexNormals();
  return g;
}

/** canvas texture: side wordmark + top ring/logo, mapped on body UV (u around, v height) */
export function applicatorTexture(renderer) {
  const c = document.createElement('canvas'); c.width = 2048; c.height = 1024;
  const x = c.getContext('2d');
  x.fillStyle = '#7ea4e3'; x.fillRect(0, 0, c.width, c.height);
  // subtle vertical gradient (lighter top)
  const gr = x.createLinearGradient(0, 0, 0, c.height); gr.addColorStop(0, 'rgba(255,255,255,0)'); gr.addColorStop(1, 'rgba(255,255,255,0.10)');
  x.fillStyle = gr; x.fillRect(0, 0, c.width, c.height);
  // vertical "Flystat" wordmark on the left side (u≈0.25 → ang=90°, that is +z... we place at u=0.75 = -x side)
  x.save(); x.translate(c.width * 0.75, c.height * 0.47); x.scale(-1, 1); x.rotate(-Math.PI / 2);
  x.font = '500 290px "ALS Hauss", "Manrope", sans-serif'; x.fillStyle = 'rgba(60,100,175,0.5)'; x.textAlign = 'center'; x.textBaseline = 'middle';
  x.fillText('Flystat', 0, 0); x.restore();
  const t = new THREE.CanvasTexture(c); t.colorSpace = THREE.SRGBColorSpace; t.anisotropy = renderer?.capabilities.getMaxAnisotropy?.() || 4;
  return t;
}

export function topLogoTexture() {
  const c = document.createElement('canvas'); c.width = c.height = 512; const x = c.getContext('2d');
  x.clearRect(0, 0, 512, 512);
  // navy inset disc
  x.fillStyle = '#1f2f7a'; x.beginPath(); x.ellipse(256, 256, 236, 236, 0, 0, Math.PI * 2); x.fill();
  // white ring
  x.strokeStyle = '#ffffff'; x.lineWidth = 14; x.beginPath(); x.ellipse(256, 256, 190, 190, 0, 0, Math.PI * 2); x.stroke();
  // swirl
  x.lineWidth = 16; x.lineCap = 'round'; x.beginPath();
  for (let a = 0; a < Math.PI * 4.6; a += 0.05) { const r = 18 + a * 20; const px = 256 + Math.cos(a) * r, py = 256 + Math.sin(a) * r; a === 0 ? x.moveTo(px, py) : x.lineTo(px, py); }
  x.stroke();
  const t = new THREE.CanvasTexture(c); t.colorSpace = THREE.SRGBColorSpace; return t;
}

export function sensorGeometry({ R = DIM.sensorR, H = DIM.sensorH } = {}) {
  // profile: flat rim (1.2 mm) then dome
  const pts = [];
  pts.push(new THREE.Vector2(0, 0));
  pts.push(new THREE.Vector2(R, 0));
  pts.push(new THREE.Vector2(R, 0.9));
  pts.push(new THREE.Vector2(R - 1.2, 1.3));
  const domeR = R - 2.4, domeH = H - 1.3;
  for (let i = 0; i <= 24; i++) { const a = (i / 24) * Math.PI / 2; pts.push(new THREE.Vector2(domeR * Math.cos(a), 1.3 + domeH * Math.pow(Math.sin(a), 0.85))); }
  const g = new THREE.LatheGeometry(pts, 128);
  g.computeVertexNormals();
  return g;
}

export function sensorTexture() {
  const c = document.createElement('canvas'); c.width = c.height = 1024; const x = c.getContext('2d');
  x.fillStyle = '#f2f3f5'; x.fillRect(0, 0, 1024, 1024);
  // fine dot texture (the adhesive rim)
  x.fillStyle = 'rgba(0,0,0,0.07)';
  for (let i = 0; i < 9000; i++) { const px = Math.random() * 1024, py = Math.random() * 1024; x.beginPath(); x.arc(px, py, 1.2, 0, 6.28); x.fill(); }
  // embossed wordmark along v
  x.font = '600 70px "ALS Hauss", "Manrope", sans-serif'; x.fillStyle = 'rgba(0,0,0,0.10)'; x.textAlign = 'center';
  x.fillText('Flystat', 512, 300);
  const t = new THREE.CanvasTexture(c); t.colorSpace = THREE.SRGBColorSpace; return t;
}

export function baseGeometry({ R = DIM.sensorR + 2, H = 9 } = {}) {
  const pts = [new THREE.Vector2(0, 0), new THREE.Vector2(R, 0), new THREE.Vector2(R, H * 0.55), new THREE.Vector2(R - 1.5, H * 0.6),
    new THREE.Vector2(R - 1.5, H), new THREE.Vector2(R - 3.2, H), new THREE.Vector2(R - 3.2, H * 0.45), new THREE.Vector2(3.6, H * 0.45),
    new THREE.Vector2(3.6, H * 1.3), new THREE.Vector2(0, H * 1.3)];
  const g = new THREE.LatheGeometry(pts, 96); g.computeVertexNormals(); return g;
}

/** builds the full group; returns handles for animation */
export function buildDevice(renderer) {
  const group = new THREE.Group();
  const blue = new THREE.MeshPhysicalMaterial({ map: applicatorTexture(renderer), color: 0xffffff, roughness: 0.42, metalness: 0.0, clearcoat: 0.6, clearcoatRoughness: 0.25, sheen: 0.0, sheenColor: new THREE.Color(0xcfe0ff) });
  const body = new THREE.Mesh(applicatorGeometry(), blue); body.castShadow = true; body.receiveShadow = true;
  // dark bottom lip
  const lip = new THREE.Mesh(new THREE.CylinderGeometry(DIM.W / 2 * 0.995, DIM.W / 2 * 0.995, 1.6, 96), new THREE.MeshStandardMaterial({ color: 0x182250, roughness: 0.6 }));
  lip.scale.z = DIM.D / DIM.W; lip.position.y = 0.8;
  // top logo disc
  const top = new THREE.Mesh(new THREE.CircleGeometry(1, 64), new THREE.MeshStandardMaterial({ map: topLogoTexture(), transparent: true, roughness: 0.5 }));
  top.rotation.x = -Math.PI / 2; top.position.y = DIM.H + 0.05; top.scale.set(DIM.W / 2 * 0.65 * 0.62, DIM.D / 2 * 0.65 * 0.62, 1);
  const applicator = new THREE.Group(); applicator.add(body, lip, top);

  const sensor = new THREE.Mesh(sensorGeometry(), new THREE.MeshPhysicalMaterial({ map: sensorTexture(), color: 0xffffff, roughness: 0.55, clearcoat: 0.1 }));
  sensor.castShadow = true;
  const base = new THREE.Mesh(baseGeometry(), new THREE.MeshPhysicalMaterial({ color: 0x1a1fb0, roughness: 0.25, transmission: 0.55, thickness: 4, ior: 1.45, attenuationColor: new THREE.Color(0x1a1fb0), attenuationDistance: 8, clearcoat: 0.6 }));
  const needle = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.22, 7, 12), new THREE.MeshStandardMaterial({ color: 0xdddddd, metalness: 0.9, roughness: 0.25 }));
  needle.position.y = 9 * 1.3 + 3.5; base.add(needle);

  group.add(applicator, sensor, base);
  // assembled pose: sensor + base hidden inside applicator bottom
  sensor.position.y = 2.5; base.position.y = -6; sensor.visible = false; base.visible = false;
  return { group, applicator, sensor, base, body };
}
