"""Expand T() strings into locale maps using translations/<locale>.json (EN text -> translation).
Also writes translations/_source.json: every unique EN string (for translators).
Usage: python3 build.py   (from database/content/src)"""
import json, os, sys, importlib
sys.path.insert(0, os.path.dirname(__file__))
from helpers import T

LOCALES = ["en", "ru", "fr", "es", "it", "de", "zh", "vi", "hi"]
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TR_DIR = os.path.join(ROOT, "translations")
os.makedirs(TR_DIR, exist_ok=True)

translations = {}
for l in LOCALES[1:]:
    p = os.path.join(TR_DIR, f"{l}.json")
    translations[l] = json.load(open(p, encoding="utf-8")) if os.path.exists(p) else {}

source = {}
missing = {l: 0 for l in LOCALES[1:]}

def expand(v):
    if isinstance(v, T):
        s = str(v)
        if s == "":
            return ""
        source[s] = ""
        out = {"en": s}
        for l in LOCALES[1:]:
            t = translations[l].get(s)
            if t:
                out[l] = t
            else:
                missing[l] += 1
        return out
    if isinstance(v, dict):
        return {k: expand(x) for k, x in v.items()}
    if isinstance(v, list):
        return [expand(x) for x in v]
    return v

def emit(name, data):
    with open(os.path.join(ROOT, f"{name}.json"), "w", encoding="utf-8") as f:
        json.dump(expand(data), f, ensure_ascii=False, indent=1)

pages = importlib.import_module("pages_en").PAGES
for i, p in enumerate(pages):
    p["sort"] = p["sort"] if p["sort"] is not None else i * 10
emit("pages", pages)
for mod, attr in [("faqs_en", "FAQS"), ("documents_en", "DOCUMENTS"), ("countries_en", "COUNTRIES"), ("news_en", "NEWS"), ("settings_en", "SETTINGS"), ("redirects_en", "REDIRECTS"), ("ui_en", "UI")]:
    try:
        m = importlib.import_module(mod)
    except ModuleNotFoundError:
        continue
    emit(mod.replace("_en", ""), getattr(m, attr))

json.dump(source, open(os.path.join(TR_DIR, "_source.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
print(f"strings: {len(source)}; missing per locale: {missing}")

# ---- export UI strings per locale for the Nuxt front (web/app/i18n/locales/<locale>.json)
try:
    ui = expand(importlib.import_module("ui_en").UI)
    # ROOT = api/database/content → repo root is three levels up
    web_dir = os.path.abspath(os.path.join(ROOT, "..", "..", "..", "web", "i18n", "locales"))
    os.makedirs(web_dir, exist_ok=True)
    def pick(v, l):
        if isinstance(v, dict) and "en" in v and all(k in LOCALES for k in v):
            return v.get(l) or v["en"]
        if isinstance(v, dict):
            return {k: pick(x, l) for k, x in v.items()}
        return v
    for l in LOCALES:
        json.dump(pick(ui, l), open(os.path.join(web_dir, f"{l}.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
    print("ui locales written to", web_dir)
except ModuleNotFoundError:
    pass
