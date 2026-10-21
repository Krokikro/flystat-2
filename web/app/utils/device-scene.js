/**
 * Live Three.js scene of the real Flystat applicator + sensor (KeyShot GLB, meshopt-compressed).
 * Used by the hero (scroll-driven story) and by the headless frame baker. No Vue in here.
 */
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js'
import { MeshoptDecoder } from 'three/examples/jsm/libs/meshopt_decoder.module.js'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'
import { story, PARTS, MM } from './device-story.js'

const norm = (x) => String(x || '').replace(/[^\p{L}\p{N}]+/gu, '').toLowerCase()

export const COLORS = { bg: 0x06264d, shell: 0x6f9cd9, shellDark: 0x5583c6, shellLogo: 0xa9c9f2, cap: 0x244b8c, glue: 0xe9f2d2 }

/**
 * @param {HTMLCanvasElement} canvas
 * @param {{ url: string, dpr?: number, background?: number|null, onProgress?: (f:number)=>void, quality?: 'high'|'low' }} opts
 */
export async function createDeviceScene(canvas, opts) {
  const quality = opts.quality || 'high'
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: opts.background === null, powerPreference: 'high-performance' })
  renderer.setPixelRatio(opts.dpr || Math.min(quality === 'high' ? 1.75 : 1.25, window.devicePixelRatio || 1))
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 0.82
  renderer.outputColorSpace = THREE.SRGBColorSpace

  const scene = new THREE.Scene()
  if (opts.background !== null) scene.background = new THREE.Color(opts.background ?? COLORS.bg)
  const pmrem = new THREE.PMREMGenerator(renderer)
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture
  scene.environmentIntensity = 0.75
  pmrem.dispose()

  const key = new THREE.DirectionalLight(0xffffff, 1.1); key.position.set(2.5, 3.2, 2.2); scene.add(key)
  const rim = new THREE.DirectionalLight(0x9cc8ff, 1.1); rim.position.set(-3, 1.2, -2.5); scene.add(rim)
  const fill = new THREE.DirectionalLight(0xdfe9ff, 0.5); fill.position.set(-2, -1, 3); scene.add(fill)
  const top = new THREE.DirectionalLight(0xffffff, 0.35); top.position.set(0, 4, -1); scene.add(top)

  const camera = new THREE.PerspectiveCamera(26, 1, 0.1, 20) // tight near/far: the emboss sits 0.1 mm proud of the shell and must not z-fight

  // soft contact shadow under the applicator (a radial-gradient sprite; real shadows are not worth the GPU here)
  const shadowTex = (() => { const c = document.createElement('canvas'); c.width = c.height = 256; const x = c.getContext('2d'); const g = x.createRadialGradient(128, 128, 10, 128, 128, 128); g.addColorStop(0, 'rgba(0,0,0,0.55)'); g.addColorStop(0.5, 'rgba(0,0,0,0.22)'); g.addColorStop(1, 'rgba(0,0,0,0)'); x.fillStyle = g; x.fillRect(0, 0, 256, 256); return new THREE.CanvasTexture(c) })()
  const shadow = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, depthWrite: false }))
  shadow.rotation.x = -Math.PI / 2; shadow.scale.set(0.9, 0.9, 1); scene.add(shadow)

  const loader = new GLTFLoader(); loader.setMeshoptDecoder(MeshoptDecoder)
  const gltf = await new Promise((res, rej) => loader.load(opts.url, res, (e) => opts.onProgress?.(e.total ? e.loaded / e.total : 0), rej))
  const root = gltf.scene
  // normalise: largest dimension = 1, centred at the origin
  const box = new THREE.Box3().setFromObject(root)
  const size = box.getSize(new THREE.Vector3()), centre = box.getCenter(new THREE.Vector3())
  const s = 1 / Math.max(size.x, size.y, size.z)
  const pivot = new THREE.Group(); pivot.add(root); scene.add(pivot)
  root.scale.setScalar(s); root.position.copy(centre).multiplyScalar(-s)
  shadow.position.y = -0.5 - 0.002

  // ---- materials: KeyShot export is flat; give the plastics their real finish
  const shellMats = new Set(), capMats = new Set()
  root.traverse((o) => {
    if (!o.isMesh) return
    o.frustumCulled = false
    const m = o.material; const name = m.name || ''
    if (/659 ?C Shiny/.test(name)) {
      // embossed logo / emblem: keep the exporter's own material (a replacement loses the emboss), just polish it
      m.roughness = 0.16; m.metalness = 0; m.color.set(COLORS.shellLogo); m.envMapIntensity = 1.2
      // the letters share their base plane with the shell: pull them a hair towards the camera in depth so nothing shimmers
      m.polygonOffset = true; m.polygonOffsetFactor = -1.5; m.polygonOffsetUnits = -2
    } else if (/659 ?C Pantone/.test(name) && !/translucence/.test(o.parent?.name || '') && m.opacity === 1) {
      // light-blue applicator shell: glossy ABS with a clearcoat
      const pm = new THREE.MeshPhysicalMaterial({ color: COLORS.shell, roughness: 0.42, metalness: 0, clearcoat: 0.3, clearcoatRoughness: 0.35, envMapIntensity: 0.55 })
      o.material = pm; shellMats.add(pm)
    } else if (m.transparent && /659C/.test(name)) {
      // translucent dark-blue base cap
      o.material = new THREE.MeshPhysicalMaterial({ color: COLORS.cap, roughness: 0.22, metalness: 0, transparent: true, opacity: 0.78, clearcoat: 0.6, clearcoatRoughness: 0.2, depthWrite: false, envMapIntensity: 1.1 })
      o.renderOrder = 2; capMats.add(o.material)
    } else if (/Clear Rough Plastic White/.test(name)) {
      o.material = new THREE.MeshPhysicalMaterial({ color: 0xffffff, roughness: 0.15, transparent: true, opacity: 0.35, depthWrite: false, envMapIntensity: 1 })
      o.renderOrder = 1
    } else if (/^Glue/.test(name)) {
      o.material = new THREE.MeshStandardMaterial({ color: COLORS.glue, roughness: 0.6, transparent: true, opacity: 0.6, depthWrite: false })
    } else if (/MLC Cloth/.test(name)) {
      m.metalness = 0; m.roughness = 0.95; m.color.set(0xf4f4f2)
    } else if (/Warm Gray/.test(name)) {
      m.color.set(0xe9e6e1); m.roughness = 0.3; m.envMapIntensity = 0.95
    } else if (/Hard Shiny Plastic White/.test(name)) {
      m.roughness = 0.2; m.envMapIntensity = 0.8
    } else if (/Board/.test(name)) {
      m.color.set(0x0f6b3a); m.roughness = 0.5
    } else if (/Gold/.test(name)) {
      m.roughness = 0.25
    }
  })

  // ---- parts lookup (names come sanitised by three; match by normalised substring + direct parent)
  const byName = {}
  root.traverse((o) => { if (o.name) (byName[o.name] ||= []).push(o) })
  const find = (token, parent) => Object.keys(byName).filter((n) => norm(n).includes(norm(token))).flatMap((n) => byName[n]).filter((o) => !parent || norm(o.parent?.name) === norm(parent))
  const parts = {}
  for (const [k, d] of Object.entries(PARTS)) parts[k] = find(d.token, d.parent)
  root.updateMatrixWorld(true)
  const base = new Map() // object → { y0, k: local units per normalised unit }
  const ws = new THREE.Vector3()
  for (const list of Object.values(parts)) for (const o of list) { o.parent.getWorldScale(ws); base.set(o, { y0: o.position.y, k: 1 / ws.y }) }

  // anchors for HTML callouts: world-space centres of parts, recomputed per frame
  const anchorBox = new THREE.Box3()
  const anchor = (key) => { anchorBox.makeEmpty(); for (const o of parts[key] || []) anchorBox.expandByObject(o); return anchorBox.isEmpty() ? null : anchorBox.getCenter(new THREE.Vector3()) }

  // ---- state
  let progress = 0, pointerX = 0, pointerY = 0, idle = 0, width = 1, height = 1, override = null, variant = opts.variant || 'rotate'
  const target = new THREE.Vector3()
  function apply(now) {
    const st = story(progress, variant)
    if (override) { Object.assign(st.cam, override.cam || {}); Object.assign(st.explode, override.explode || {}); if (override.hide) for (const k of override.hide) for (const o of parts[k] || []) o.visible = false; if (override.show) for (const k of override.show) for (const o of parts[k] || []) o.visible = true }
    for (const [k, mm] of Object.entries(st.explode)) for (const o of parts[k] || []) { const b = base.get(o); o.position.y = b.y0 + mm * MM * b.k }
    const capF = override?.fade?.cap ?? st.fade?.cap ?? 1
    for (const m of capMats) m.opacity = 0.78 * capF
    for (const o of parts.cap || []) o.visible = capF > 0.01 && !(override?.hide || []).includes('cap')
    // idle turntable before the story starts, pointer parallax always (damped)
    idle = progress < 0.02 ? idle + 0.0016 : idle * 0.96
    pivot.rotation.y = idle
    const az = THREE.MathUtils.degToRad(st.cam.az) + pointerX * 0.12
    const el = THREE.MathUtils.degToRad(st.cam.el) - pointerY * 0.06
    const d = st.cam.dist
    target.set(0, st.cam.ty, 0)
    camera.position.set(target.x + d * Math.cos(el) * Math.sin(az), target.y + d * Math.sin(el), target.z + d * Math.cos(el) * Math.cos(az))
    camera.lookAt(target)
    // composition: push the model right of centre on wide screens by shifting the projection
    const wide = width / height > 1.15
    camera.setViewOffset(width, height, wide ? -st.cam.shift * width * 0.5 : 0, height * (wide ? 0 : -0.06), width, height)
    shadow.material.opacity = st.shadow ?? Math.max(0, 1 - st.phase.B * 1.4)
    shadow.rotation.z = idle
    renderer.render(scene, camera)
    return st
  }
  function resize(w, h) { width = Math.max(1, w); height = Math.max(1, h); renderer.setSize(w, h, false); camera.aspect = w / h; camera.updateProjectionMatrix() }
  function project(v) { const p = v.clone().project(camera); return { x: (p.x + 1) / 2 * width, y: (1 - p.y) / 2 * height, z: p.z } }
  function anchors() { const out = {}; for (const k of ['housing', 'internals', 'patch', 'cap']) { const a = anchor(k); if (a) out[k] = project(a) } return out }

  return {
    THREE, scene, camera, renderer, root, parts,
    setProgress: (p) => { progress = p }, setVariant: (v) => { variant = v }, setOverride: (o) => { override = o }, setPointer: (x, y) => { pointerX = x; pointerY = y },
    render: apply, resize, anchors, project,
    dispose: () => { renderer.dispose(); scene.traverse((o) => { o.geometry?.dispose?.(); const m = o.material; if (m) (Array.isArray(m) ? m : [m]).forEach((x) => x.dispose?.()) }) },
  }
}
