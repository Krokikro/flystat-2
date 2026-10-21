"""Replace the generated sensor in a lifestyle photo with a render of the real Flystat sensor.

For each photo we know roughly where the generated patch is. We segment the light patch around that point,
fit an ellipse (gives size, tilt and foreshortening), inpaint the skin, then paste a render of the real sensor
taken at the matching elevation, scaled to the requested real-world size, tinted to the photo's light and
given a soft contact shadow.

usage: python3 composite-sensor.py <photo> <out> <guess_x> <guess_y> [scale=0.9] [rotate_extra=0]
"""
import sys, math
import numpy as np, cv2
from PIL import Image, ImageFilter, ImageEnhance

RENDERS = '/home/claude/flystat-2/assets3d/out/sens'

def segment_patch(img, gx, gy, r=200, expect=None):
    h, w = img.shape[:2]
    x0, y0 = max(0, gx - r), max(0, gy - r)
    sub = img[y0:y0 + 2 * r, x0:x0 + 2 * r]
    hsv = cv2.cvtColor(sub, cv2.COLOR_BGR2HSV)
    S, V = hsv[..., 1], hsv[..., 2]
    mask = ((S < 45) & (V > 165)).astype(np.uint8) * 255
    mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, np.ones((7, 7), np.uint8))
    mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, np.ones((5, 5), np.uint8))
    n, lab, stats, cents = cv2.connectedComponentsWithStats(mask)
    best, bd = None, 1e9
    for i in range(1, n):
        cx, cy = cents[i]
        d = math.hypot(cx - r, cy - r)
        area = stats[i, cv2.CC_STAT_AREA]
        if expect and not (0.2 * math.pi * (expect / 2) ** 2 < area < 2.2 * math.pi * (expect / 2) ** 2): continue
        if area > 400 and d < bd:
            best, bd = i, d
    if best is None:
        raise SystemExit('patch not found')
    comp = (lab == best).astype(np.uint8) * 255
    cnts, _ = cv2.findContours(comp, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)
    (cx, cy), (MA, ma), ang = cv2.fitEllipse(max(cnts, key=cv2.contourArea))
    full = np.zeros((h, w), np.uint8); full[y0:y0 + 2 * r, x0:x0 + 2 * r] = comp
    return (cx + x0, cy + y0), (MA, ma), ang, full

def main():
    photo, out, gx, gy = sys.argv[1], sys.argv[2], int(sys.argv[3]), int(sys.argv[4])
    scale = float(sys.argv[5]) if len(sys.argv) > 5 else 0.9
    rot_extra = float(sys.argv[6]) if len(sys.argv) > 6 else 0
    expect = float(sys.argv[7]) if len(sys.argv) > 7 else None
    img = cv2.imread(photo)
    (cx, cy), (MA, ma), ang, mask = segment_patch(img, gx, gy, r=int((expect or 300) * 0.9), expect=expect)
    major, minor = max(MA, ma), min(MA, ma)
    # fitEllipse angle is of the first axis (MA); if MA is the minor axis rotate by 90
    tilt = ang if MA >= ma else ang + 90
    el = max(30, min(90, math.degrees(math.asin(max(0.05, min(1, minor / major))))))
    el_r = min((30, 40, 50, 60, 70, 80, 90), key=lambda e: abs(e - el))
    print(f'patch at ({cx:.0f},{cy:.0f}) axes {major:.0f}x{minor:.0f} tilt {tilt:.0f}° → elevation {el:.0f}° (render {el_r})')
    # the segmented blob is the white housing; the generated grey patch around it is ~1.4x wider → inpaint that whole area
    if minor / major > 0.85: tilt = 90  # near-circular: the fitted angle is noise
    patch = np.zeros_like(mask)
    cv2.ellipse(patch, (int(cx), int(cy)), (int(major * 0.75), int(minor * 0.75 + (major - minor) * 0.15)), tilt, 0, 360, 255, -1)
    mask = patch
    # 1. inpaint the generated sensor with surrounding skin
    dil = cv2.dilate(mask, np.ones((int(major * 0.1) | 1,) * 2, np.uint8))
    clean = cv2.inpaint(img, dil, 9, cv2.INPAINT_TELEA)
    # soften the inpainted zone so the skin texture seam does not show
    # give the inpainted skin back some grain so it does not read as a smooth patch
    noise = np.random.default_rng(1).normal(0, 2.2, clean.shape)
    m3 = (cv2.GaussianBlur(dil, (0, 0), 4)[..., None] / 255.0)
    clean = np.clip(clean + noise * m3, 0, 255).astype(np.uint8)
    base = Image.fromarray(cv2.cvtColor(clean, cv2.COLOR_BGR2RGB)).convert('RGBA')
    # 2. the render: major axis = patch diameter * scale
    # the client's KeyShot top-view render (patch included), foreshortened to the measured ellipse
    ren = Image.open('/home/claude/flystat-2/assets3d/ref/sensor-top.png').convert('RGBA'); ren = ren.crop(ren.getbbox())
    sq = max(0.45, min(1.0, minor / major))
    ren = ren.resize((ren.width, max(1, int(ren.height * sq))), Image.LANCZOS)
    # fake the dome's side under foreshortening: a slightly darker lower rim
    if sq < 0.95:
        rim = Image.new('RGBA', ren.size, (0, 0, 0, 0)); rd = np.zeros((ren.height, ren.width), np.uint8)
        cv2.ellipse(rd, (ren.width // 2, int(ren.height * 0.56)), (int(ren.width * 0.36), int(ren.height * 0.36)), 0, 20, 160, 255, int(ren.height * (1 - sq) * 0.35) | 1)
        rd = cv2.GaussianBlur(rd, (0, 0), ren.width * 0.012)
        rim.putalpha(Image.fromarray((rd * 0.35).astype(np.uint8))); rim.paste((120, 110, 100, 0), (0, 0, ren.width, ren.height), Image.fromarray(np.zeros_like(rd)))
        shade = Image.new('RGBA', ren.size, (90, 80, 75, 0)); shade.putalpha(Image.fromarray((rd * 0.12).astype(np.uint8)))
        a0 = ren.split()[3]; ren.alpha_composite(shade); ren.putalpha(a0)
    target_w = major * 1.17 * scale  # real patch is 28 mm over a 24 mm housing
    k = target_w / ren.width
    ren = ren.resize((max(1, int(ren.width * k)), max(1, int(ren.height * k))), Image.LANCZOS)
    # tint towards the photo's local light: match mean brightness of the skin ring around the patch
    ring = cv2.dilate(mask, np.ones((int(major * 0.5) | 1,) * 2, np.uint8)) & ~dil
    skin = img[ring > 0].astype(float); lum = skin.mean() / 255 if len(skin) else 0.6
    warm = skin.mean(0)[::-1] / 255 if len(skin) else np.array([.8, .7, .6])  # RGB
    rgb = np.asarray(ren).astype(float)
    exposure = 0.78 + 0.5 * lum
    tint = warm / warm.max()
    rgb[..., :3] = np.clip(rgb[..., :3] * exposure * (0.82 + 0.18 * tint), 0, 255)
    # photo grain on the render, scaled with its alpha
    g = np.random.default_rng(2).normal(0, 3.0, rgb.shape[:2])[..., None]
    rgb[..., :3] = np.clip(rgb[..., :3] + g * (rgb[..., 3:4] / 255), 0, 255)
    ren = Image.fromarray(rgb.astype(np.uint8), 'RGBA')
    # soften render edges a touch (film look) and rotate to the patch tilt
    ren = ren.filter(ImageFilter.GaussianBlur(0.6))
    ren = ren.rotate(-(tilt - 90) + rot_extra, resample=Image.BICUBIC, expand=True)
    # 3. contact shadow
    sh = Image.new('RGBA', ren.size, (0, 0, 0, 0)); a = ren.split()[3]
    sh.paste((40, 20, 10, 110), (0, 0), a)
    sh = sh.filter(ImageFilter.GaussianBlur(major * 0.05))
    px, py = int(cx - ren.width / 2), int(cy - ren.height / 2)
    base.alpha_composite(sh, (px + int(major * 0.02), py + int(major * 0.06)))
    base.alpha_composite(ren, (px, py))
    # add a touch of film grain to the pasted zone so it matches
    base.convert('RGB').save(out, quality=94)
    print('saved', out)

if __name__ == '__main__':
    main()
