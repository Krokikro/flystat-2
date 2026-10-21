/**
 * The hero story, as a pure function of scroll progress p ∈ [0,1].
 * Shared by the live Three.js scene on the site and by the headless renderer that bakes the
 * poster / mobile frames, so both always agree.
 *
 * Units: the model is normalised so its largest dimension (applicator height, 89.8 mm) = 1.
 * Explode offsets are given in millimetres of the real device.
 */
export const MM = 1 / 89.8 // normalised units per millimetre

const clamp01 = (x) => Math.min(1, Math.max(0, x))
const ease = (x) => { x = clamp01(x); return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2 } // easeInOutCubic
const seg = (p, a, b) => ease((p - a) / (b - a))
const lerp = (a, b, t) => a + (b - a) * t

/** Named parts and how to find them in the KeyShot node tree (token = normalised substring, parent = direct parent token). */
export const PARTS = {
  applicator: { token: 'Аппликатор' },
  needle: { token: '(Clear)', parent: 'Датчик' },          // needle carriage, travels with the applicator
  housing: { token: 'Корпус датчика' },                    // sensor housing (top dome + lower shell)
  internals: { token: 'Внутренние компоненты' },           // PCB, battery, connector
  patch: { token: 'Patch and Glue' },
  cap: { token: 'translucence' },                          // translucent base cap of the applicator
}

/** Sensor stack centre (mm, model Y) used as the camera target once the applicator has lifted. */
export const SENSOR_Y_MM = -28 // relative to the model centre (housing 53.5–58 mm, model 37.6–127.4 mm)

export function story(p) {
  p = clamp01(p)
  // ---- phases (slow: the hero section is ~7 viewports tall)
  const A = seg(p, 0.0, 0.3)    // turntable, logo facing the camera
  const B = seg(p, 0.3, 0.62)   // applicator lifts, camera stays wide so the body (and its logo) is seen leaving
  const C = seg(p, 0.62, 0.86)  // sensor explodes
  const D = seg(p, 0.86, 1.0)   // settle on the sensor
  const Bc = Math.pow(B, 2.2)   // the camera follows late: first the body rises, then we go down to the sensor
  // ---- camera (spherical, degrees / normalised units). Logo is readable between az ≈ 15° and 60°.
  const az = 22 + A * 22 + B * 12 + C * 38 + D * 26
  const el = lerp(lerp(lerp(11, 9, Bc), 16, C), 24, D)
  const dist = lerp(lerp(lerp(3.1, 2.4, Bc), 1.3, C), 0.95, D)
  const ty = lerp(lerp(0.02, SENSOR_Y_MM * MM, Bc), SENSOR_Y_MM * MM + 0.03, C)
  // horizontal composition offset: model sits right of centre while the headline is on the left
  const shift = lerp(0.34, 0.14, Bc) * (1 - D * 0.4)
  // ---- explode (mm). The lift eases in slowly so the first centimetres read as a gentle separation.
  const lift = Math.pow(B, 1.35) * 130 + C * 60
  const ex = {
    applicator: lift,
    needle: lift,
    housing: lerp(0, 17, C) * (1 - D),
    internals: lerp(0, 8, C) * (1 - D),
    patch: -lerp(0, 7, C) * (1 - D),
    cap: -lerp(0, 9, C) - D * 6,
  }
  const spin = 0
  // ---- copy stages
  const text = {
    headline: 1 - seg(p, 0.08, 0.26),
    stage2: seg(p, 0.4, 0.5) * (1 - seg(p, 0.62, 0.68)),
    labels: seg(p, 0.72, 0.8) * (1 - seg(p, 0.88, 0.93)),
    stage4: seg(p, 0.93, 1.0),
  }
  return { cam: { az, el, dist, ty, shift }, explode: ex, spin, text, phase: { A, B, C, D } }
}
