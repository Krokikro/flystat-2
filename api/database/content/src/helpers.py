"""Tiny authoring DSL. T() marks a translatable string; build.py expands it to {en, ru, ...}."""

class T(str):
    """A translatable English string."""
    __slots__ = ()

def block(type_, data, anchor=None, enabled=True):
    return {"type": type_, "anchor": anchor, "enabled": enabled, "data": data}

def page(slug, template, title, blocks, nav=None, seo_title=None, seo_desc=None, menu_group=None, in_header=False, in_footer=False, status="published", sort=None):
    return {
        "slug": slug, "template": template, "title": title, "nav_label": nav or title,
        "seo": {"title": seo_title or title, "description": seo_desc or "", "og_image": None, "noindex": False},
        "status": status, "in_header": in_header, "in_footer": in_footer, "menu_group": menu_group, "sort": sort,
        "blocks": blocks,
    }

def cta(label, href):
    return {"label": label, "href": href}

def item(title, text, icon=None, href=None, image=None):
    d = {"title": title, "text": text, "icon": icon}
    if href: d["href"] = href
    if image: d["image"] = image
    return d

def img(path, alt):
    return {"src": f"/media/{path}", "alt": alt}
