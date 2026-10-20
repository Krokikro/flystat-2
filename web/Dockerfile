FROM node:22-alpine AS build
WORKDIR /app
RUN corepack enable && corepack prepare pnpm@9 --activate
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . .
# Railway passes service variables as build args; the sitemap/site URL is resolved at build time.
ARG SITE_URL API_BASE MEDIA_BASE SITE_MODE
ENV SITE_URL=$SITE_URL API_BASE=$API_BASE MEDIA_BASE=$MEDIA_BASE SITE_MODE=$SITE_MODE NODE_OPTIONS=--max-old-space-size=4096
RUN pnpm run build

FROM node:22-alpine
WORKDIR /app
ENV NODE_ENV=production HOST=0.0.0.0 PORT=3000
COPY --from=build /app/.output ./.output
EXPOSE 3000
CMD ["node", ".output/server/index.mjs"]
