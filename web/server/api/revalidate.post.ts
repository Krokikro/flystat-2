/** Called by the admin after publishing: drops the ISR cache. Body: { secret, paths: ['*' | '/en/device', ...] } */
export default defineEventHandler(async (event) => {
  const cfg = useRuntimeConfig(event)
  const body = await readBody(event).catch(() => ({}))
  if (!cfg.revalidateSecret || body?.secret !== cfg.revalidateSecret) throw createError({ statusCode: 403, statusMessage: 'Forbidden' })
  const storage = useStorage('cache')
  const keys = await storage.getKeys('nitro:handlers')
  let n = 0
  for (const k of keys) { await storage.removeItem(k); n++ }
  return { ok: true, cleared: n }
})
