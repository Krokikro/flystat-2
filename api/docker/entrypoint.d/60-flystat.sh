#!/bin/sh
# Runs on container start: seed roles + content (idempotent), cache config, start queue + scheduler in background.
cd /var/www/html || exit 0
php artisan filament:assets >/dev/null 2>&1 || true
php artisan db:seed --force --no-interaction || true
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan filament:optimize || true
php artisan storage:link >/dev/null 2>&1 || true
if [ "${RUN_WORKER:-1}" = "1" ]; then
  (while true; do php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --quiet; sleep 1; done) &
  (while true; do php artisan schedule:run --quiet; sleep 60; done) &
fi
