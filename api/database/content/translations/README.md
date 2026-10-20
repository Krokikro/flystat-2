# Translation files

`_source.json` — every unique English string used on the site (keys). `<locale>.json` — map `EN string → translation`.
`src/build.py` merges them into the content JSON that the seeder loads.

## Rules for translators (human or model)
- Keep HTML tags, attributes, `<br>`, entities and placeholders like `{privacy}` / `{consent}` exactly as in the source.
- Keep these untranslated: Flystat, MLC, CGM (add the local expansion once on first use if natural), App Store, Google Play, Bluetooth, NFC, IP58, mmol/L, mg/dL, CE, MDR, AGP, Jira, Telegram, email addresses, URLs, numbers and units with the same decimal convention as the target language (e.g. 2,2–22,2 ммоль/л in ru/fr/es/it/de; keep "mmol/L" written as the local convention: ммоль/л, mmol/L, mmol/l...).
- Tone (from the Flystat text guide): plain, warm, specific, no exclamation marks, no medical promises, no "revolutionary". Short sentences. Address the reader as "you" in the polite but not stiff register of the language (ru: «вы» lowercase; de: Sie; fr: vous; es: usted → use "tú"? No: use neutral "usted" forms where needed, prefer impersonal; it: Lei-neutral; zh/vi/hi: standard polite).
- Glossary: sensor → ru сенсор (not датчик in marketing text; датчик acceptable in technical rows), applicator → аппликатор, waiting list → лист ожидания, distributor → дистрибьютор, memorandum of understanding → меморандум о взаимопонимании, continuous glucose monitoring → непрерывный мониторинг глюкозы (НМГ), finger prick → прокол пальца, time in range → время в целевом диапазоне, warm-up → прогрев, patch → пластырь (фиксирующий патч), filament → нить сенсора, clinician → врач, conformity assessment → оценка соответствия, CE marking → CE-маркировка.
- Slugs and keys that look like identifiers (`for/diabetes`, icon names) never appear here; if a value is a bare number or a code, copy it.
- Dates like "end of 2027" translate naturally.
- Legal and manual texts: translate fully and precisely; they are long HTML blocks — keep the heading ids (`id="install"`).
