# Flystat 2.0 — flystat.com + admin

Monorepo: `web/` — public site (Nuxt 4, 9 languages, ISR), `api/` — admin panel + CRM + content API (Laravel 13, Filament 5, PostgreSQL).
PRD: https://claude.ai/code/artifact/46df5d25-57ee-4181-99ce-ce40b3c8a594 · Jira epic SOFT-67.

## Local
```
cd api && composer install && cp .env.example .env && php artisan key:generate && touch database/database.sqlite && php artisan migrate --seed && php artisan serve --port=8000
cd web && pnpm install && API_BASE=http://127.0.0.1:8000 pnpm dev
```
Admin: http://localhost:8000/admin (ADMIN_EMAIL / ADMIN_PASSWORD from env, default admin@flystat.com / ChangeMe-2026!). 2FA is mandatory unless `ADMIN_REQUIRE_2FA=false`.

## Content
Authored source lives in `api/database/content/src/*.py` (English) + `translations/<locale>.json`. `python3 src/build.py` regenerates the JSON the seeder loads and the UI strings for the front. The seeder is idempotent and never overwrites admin edits unless `CONTENT_FORCE=1`.

## Deploy (Railway)
Two services from this repo (root dirs `api/` and `web/`) + PostgreSQL. Env vars are listed in `api/.env.example` and `web/.env.example`. Integrations (Telegram, Jira Service Management, mail, analytics) are switched on from Admin → Settings — no redeploy needed.
Demo mode: `SITE_MODE=demo` on the web service (noindex + robots Disallow) and optional `BASIC_AUTH=user:pass`. Switching Admin → Settings → Site mode to “published” and setting `SITE_MODE=published` on the web service opens the site to indexing.
