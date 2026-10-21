/**
 * The hero stories, as pure functions of scroll progress p ∈ [0,1].
 * Shared by the live Three.js scene on the site and by the headless renderer that bakes the
 * poster / mobile frames, so both always agree.
 *
 * Units: the model is normalised so its largest dimension (applicator height, 89.8 mm) = 1.
 * Explode offsets are given in millimetres of the real device. Camera azimuth in degrees:
 * the applicator logo is readable between az ≈ 15° and 60°, the sensor lettering reads upright
 * from above at az ≈ 225° (SENSOR_TEXT_AZ).
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
/** Camera azimuth from which the embossed "Flystat" on the sensor dome reads upright when seen from above. */
export const SENSOR_TEXT_AZ = 225

export const VARIANTS = ['rotate', 'device']

export function story(p, variant = 'rotate') {
  return variant === 'device' ? storyDevice(p) : storyRotate(p)
}

/**
 * Home: the assembled applicator turns, lifts off the sensor, the sensor opens up, then turns its
 * face to the viewer (camera swings up over it) and closes again — "that is all you wear".
 */
function storyRotate(p) {
  p = clamp01(p)
  // ---- phases (slow: the hero section is ~7 viewports tall)
  const A = seg(p, 0.0, 0.3)    // turntable, logo facing the camera
  const B = seg(p, 0.3, 0.62)   // applicator lifts, camera stays wide so the body (and its logo) is seen leaving
  const C = seg(p, 0.62, 0.86)  // sensor explodes, camera swings back and down to it
  const D = seg(p, 0.86, 1.0)   // sensor closes and the camera rises over it: lettering to the viewer
  const Bc = Math.pow(B, 2.2)   // the camera follows late: first the body rises, then we go down to the sensor
  // ---- camera (spherical, degrees / normalised units)
  const az = 22 + A * 22 + B * 12 + C * 84 + D * (SENSOR_TEXT_AZ - 140) // 22 → 44 → 56 → 140 → 225
  const el = lerp(lerp(lerp(11, 9, Bc), 16, C), 68, D)
  const dist = lerp(lerp(lerp(3.1, 2.4, Bc), 1.3, C), 1.0, D)
  const ty = lerp(lerp(0.02, SENSOR_Y_MM * MM, Bc), SENSOR_Y_MM * MM + 0.03, C) - D * 0.025
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
    cap: -lerp(0, 9, C) - D * 40,
  }
  const fade = { cap: 1 - seg(p, 0.86, 0.94) } // the base cap leaves the frame; only the sensor is left
  const shadow = Math.max(0, 1 - B * 1.4)
  // ---- copy stages
  const text = {
    headline: 1 - seg(p, 0.08, 0.26),
    stage2: seg(p, 0.4, 0.5) * (1 - seg(p, 0.62, 0.68)),
    labels: seg(p, 0.72, 0.8) * (1 - seg(p, 0.88, 0.93)),
    stage4: seg(p, 0.94, 1.0),
  }
  return { cam: { az, el, dist, ty, shift }, explode: ex, fade, shadow, text, phase: { A, B, C, D } }
}

/**
 * Device page: the reverse journey. We start inside — the open sensor with its parts named —
 * the sensor closes, the applicator comes down over it and the cap seals it, then the finished
 * product turns once around and settles with the logo to the viewer.
 */
function storyDevice(p) {
  p = clamp01(p)
  const A = seg(p, 0.0, 0.3)     // exploded sensor, close; callouts
  const B = seg(p, 0.3, 0.5)     // sensor closes
  const pull = seg(p, 0.5, 0.68) // camera pulls back to the whole product
  const land = seg(p, 0.6, 0.8)  // applicator descends over the sensor
  const seal = seg(p, 0.7, 0.86) // cap closes
  const D = seg(p, 0.8, 1.0)     // full turn of the finished product
  const C = pull
  const az = 35 - A * 20 + B * 10 - C * 5 + D * 360 // 35 → 15 → 25 → 20 → 380 (≡ 20)
  const el = lerp(lerp(14, 12, B), 10, pull) + Math.sin(D * Math.PI) * 14
  const Cl = Math.pow(1 - land, 1.6) // the applicator eases into place: fast approach, slow last millimetres
  // the applicator enters from the top of the frame and comes down onto the sensor; the camera recentres as it lands
  const dist = lerp(lerp(1.05, 1.2, B), 2.6, pull)
  const ty = lerp(SENSOR_Y_MM * MM + 0.02, lerp(SENSOR_Y_MM * MM + 0.3, 0.02, land), pull)
  const shift = lerp(0.2, 0.3, pull)
  const ex = {
    applicator: Cl * 110,
    needle: Cl * 110,
    housing: 18 * (1 - B),
    internals: 9 * (1 - B),
    patch: -7 * (1 - B),
    cap: -14 * (1 - seal),
  }
  const fade = { cap: 1 }
  const shadow = Math.pow(land, 3)
  const text = {
    headline: 1 - seg(p, 0.06, 0.2),
    labels: seg(p, 0.14, 0.22) * (1 - seg(p, 0.3, 0.36)),
    stage2: seg(p, 0.52, 0.6) * (1 - seg(p, 0.76, 0.82)),
    stage4: seg(p, 0.93, 1.0),
  }
  return { cam: { az, el, dist, ty, shift }, explode: ex, fade, shadow, text, phase: { A, B, C, D } }
}
