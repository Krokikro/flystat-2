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
  // ---- phases
  const A = seg(p, 0.0, 0.22)   // turntable
  const B = seg(p, 0.22, 0.5)   // applicator lifts
  const C = seg(p, 0.5, 0.8)    // sensor explodes
  const D = seg(p, 0.8, 1.0)    // settle on the sensor
  // ---- camera (spherical, degrees / normalised units)
  const az = 28 + A * 26 + B * 30 + C * 40 + D * 30
  const el = lerp(lerp(lerp(12, 7, B), 16, C), 24, D)
  const dist = lerp(lerp(lerp(3.1, 2.0, B), 1.3, C), 0.95, D)
  const ty = lerp(lerp(0.02, SENSOR_Y_MM * MM, B), SENSOR_Y_MM * MM + 0.03, C)
  // horizontal composition offset: model sits right of centre while the headline is on the left
  const shift = lerp(0.34, 0.12, B) * (1 - D * 0.4)
  // ---- explode (mm)
  const lift = lerp(0, 95, B) + C * 40
  const ex = {
    applicator: lift,
    needle: lift,
    housing: lerp(0, 17, C) * (1 - D),
    internals: lerp(0, 8, C) * (1 - D),
    patch: -lerp(0, 7, C) * (1 - D),
    cap: -lerp(0, 9, C) - D * 6,
  }
  // ---- turntable spin (radians) while idle at the top, blended out as the story starts
  const spin = (1 - B) * 0.0
  // ---- copy stages
  const text = {
    headline: 1 - seg(p, 0.05, 0.2),
    stage2: seg(p, 0.3, 0.42) * (1 - seg(p, 0.5, 0.58)),
    labels: seg(p, 0.62, 0.72) * (1 - seg(p, 0.82, 0.9)),
    stage4: seg(p, 0.9, 1.0),
  }
  return { cam: { az, el, dist, ty, shift }, explode: ex, spin, text, phase: { A, B, C, D } }
}
