"""News imported from the old site (16 articles, en/ru/fr originals). Dates are approximate — see Jira SOFT-97."""
import json, os
from helpers import T
LOCALES = ["en", "ru", "fr", "es", "it", "de", "zh", "vi", "hi"]
_items = json.load(open(os.path.join(os.path.dirname(__file__), "news_scraped.json"), encoding="utf-8"))
NEWS = [dict(
    legacy_id=i["legacy_id"], slug={l: i["slug"] for l in LOCALES}, title=T(i["title"]), excerpt=T(i["excerpt"]), body=T(i["body"]),
    cover=i["cover"], category=i["category"], status="published", published_at=i["date"] + " 10:00:00", author="MLC",
) for i in _items]
