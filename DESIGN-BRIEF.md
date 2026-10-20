# Flystat 2.0 — front-end design pass (brief for agents)

Repo: `/home/claude/flystat-2` (monorepo). Front: `web/` — Nuxt 4, pnpm, TypeScript, scoped CSS per component, tokens in `web/app/assets/css/main.css`. Content comes from the API (`api/`, Laravel) as blocks; the authoring source is Python: `api/database/content/src/pages_en.py` (+ `translations/<locale>.json`, 8 locales) → `python3 build.py` → `api/database/content/*.json` → `cd api && CONTENT_FORCE=1 php artisan db:seed --force` (API on `http://127.0.0.1:8011`, already running; if not: `cd api && setsid nohup php artisan serve --port=8011 > /tmp/api.log 2>&1 &`).

## Product / audience
Flystat is a 15-day continuous glucose monitoring sensor (24 mm disc, 4.5 mm thin, applicator pre-loaded, 1 reading/min, Bluetooth+NFC, IP58) from the MLC group. Not yet CE-marked (planned end of 2027): no prices, no medical promises, no fake accuracy numbers. Audience: people with diabetes and their families, sport/nutrition users, clinicians, distributors. Tone: plain, warm, specific.

## Design direction (C, approved by the client)
Dark navy hero (`#06264D`) → light, airy sections; the device and the app are the heroes. Think Apple / Dexcom / Oura product pages: **large type, generous spacing, image-led full-bleed bands, alternating layouts, strong scroll choreography**. Client feedback on the current state (verbatim, translated): "looks very dry — too few images and too few impressive transitions between blocks and animations; there must be many of them for a wow effect; elements are too small for desktop; layouts are broken in many places; everything must be straight on any desktop size; fast and crisp".

Font: ALS Hauss (loaded). Root font size is fluid (16 → 19.5px) and all component font sizes are in `rem` — keep it that way (no `px` font sizes). Tokens: see `:root` in main.css (`--navy-800`, `--ink`, `--blue`, `--sky`, `--purple`, `--radius*`, `--ease`, `--container` 1320/1480). Do not edit `main.css` (owned by the lead) — if you need a token, ask in your report; use local scoped CSS otherwise.

## Imagery available (use real assets only — no placeholders, no generated device images)
- Renders from the real engineering model (transparent webp): `web/public/media/renders/{applicator-front,applicator-angle,applicator-back,applicator-top,open,exploded,sensor,sensor-top,sensor-side,sensor-exploded}.webp` (+ legacy aliases front/angle/exploded/sensor). Light background sections: put them on soft gradient plates / warm panels, add drop shadows via CSS filter.
- Lifestyle photos: `web/public/media/lifestyle/*.webp` (look at them first).
- App screens: `web/public/media/app/*.webp` are currently HTML mockups (will be replaced by real Figma exports later — keep the same file names/slots).
- Hero: `DeviceScene.vue` / `blocks/HeroDark.vue` — live Three.js, already done, do not touch.
- World map: `blocks/WorldMap.vue` (SVG paths in `web/app/assets/world.json`).
- Higgsfield-generated production images (`media/production/*.webp`) must NOT be used anymore (device looks wrong) — remove references.

## Motion
`gsap` (+ ScrollTrigger) and `three` are installed. There is a `v-reveal` directive (`.reveal` → `.is-in`) and `useReveal`. Add real choreography: staggered reveals, parallax on images, pinned/sticky side text with scrolling cards, number counters, clip-path/mask image reveals, horizontal scroll strips where it helps, hover micro-interactions. Respect `prefers-reduced-motion`. Everything must stay fast: no layout thrash, transforms/opacity only, IntersectionObserver gating, lazy images with explicit width/height (CLS = 0).

## Quality bar / verification
- Audit at 1280, 1440, 1680 and 1920 wide (desktop first), then 390 (phone). Nothing may overflow horizontally; grids must not leave orphan cards; images must never be empty boxes.
- Build & preview: `cd web && pnpm run build && PORT=<your port> NUXT_PUBLIC_API_BASE=http://127.0.0.1:8011 NUXT_API_BASE=http://127.0.0.1:8011 NUXT_REVALIDATE_SECRET=dev-secret node .output/server/index.mjs &` (use `setsid nohup`; never `pkill -f` on a pattern that matches your own shell). Screenshot helpers: `web/scripts/scrollshots.cjs <path> <w> <h> <n>` (edit the port inside or copy it), Playwright flags `--use-gl=angle --use-angle=swiftshader --enable-unsafe-swiftshader`. Look at your screenshots (Read the PNG) — do not ship what you have not seen.
- i18n: any new UI string goes through `$t()` with keys added to `web/i18n/locales/*.json`?? NO — UI strings are generated: add them to `api/database/content/src/ui_en.py` and translations for all 8 locales in `api/database/content/translations/<locale>.json` (key = the English string), then `python3 build.py`. Prefer reusing existing copy; keep new copy minimal.
- No git commits/pushes; the lead merges. Report: what changed per component, what you verified, open issues.
