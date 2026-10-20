#!/bin/bash
# restart the local preview server (used during development in this session)
pkill -f "node .output/server/index.mjs" 2>/dev/null; sleep 1
cd "$(dirname "$0")/.." && PORT=3011 NUXT_PUBLIC_API_BASE=http://127.0.0.1:8011 NUXT_API_BASE=http://127.0.0.1:8011 NUXT_REVALIDATE_SECRET=dev-secret nohup node .output/server/index.mjs > /tmp/web.log 2>&1 &
sleep 4; curl -s -o /dev/null -w "web %{http_code}\n" http://127.0.0.1:3011/en
