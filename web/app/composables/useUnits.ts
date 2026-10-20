/** Glucose units switch shared across the site, remembered per visitor. */
export function useUnits() {
  const units = useState<'mmol' | 'mgdl'>('units', () => 'mmol')
  const cookie = useCookie<'mmol' | 'mgdl'>('flystat_units', { maxAge: 60 * 60 * 24 * 365, sameSite: 'lax' })
  if (cookie.value && units.value !== cookie.value) units.value = cookie.value
  const set = (u: 'mmol' | 'mgdl') => { units.value = u; cookie.value = u }
  const toggle = () => set(units.value === 'mmol' ? 'mgdl' : 'mmol')
  const fmt = (mmol: number) => units.value === 'mmol' ? `${mmol.toFixed(1)} mmol/L` : `${Math.round(mmol * 18.016)} mg/dL`
  return { units, set, toggle, fmt }
}
