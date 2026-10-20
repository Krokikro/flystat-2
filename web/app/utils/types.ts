export interface MenuItem { slug: string; label: string; group: string | null }
export interface Bootstrap {
  locale: string; locales: string[]; locale_names: Record<string, string>
  settings: Record<string, any>
  header: MenuItem[]; footer: MenuItem[]
  legal: { slug: string; title: string }[]
}
export interface Block { id: number; type: string; anchor: string | null; data: Record<string, any> }
export interface PageData { slug: string; template: string; title: string; seo: Record<string, any>; blocks: Block[]; redirect?: string; code?: number }
export interface NewsCard { slug: string; title: string; excerpt: string; cover: string | null; category: string; tags: string[] | null; published_at: string | null }
