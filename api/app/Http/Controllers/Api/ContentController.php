<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Distributor;
use App\Models\Document;
use App\Models\Faq;
use App\Models\News;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Support\Localize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    protected function loc(string $locale): string
    {
        $l = Localize::locale($locale);
        app()->setLocale($l);

        return $l;
    }

    protected function previewAllowed(Request $r): bool
    {
        $token = Setting::get('preview_token');

        return $token && $r->query('preview') === $token;
    }

    /** Everything the layout needs: menus, settings, locale list. */
    public function bootstrap(Request $r, string $locale): JsonResponse
    {
        $l = $this->loc($locale);
        $pages = Page::published()->orderBy('sort')->get();
        $menu = fn ($flag) => $pages->filter(fn ($p) => $p->$flag)->map(fn ($p) => [
            'slug' => $p->slug, 'label' => $p->getTranslation('nav_label', $l, false) ?: $p->getTranslation('title', $l), 'group' => $p->menu_group,
        ])->values();

        return response()->json([
            'locale' => $l,
            'locales' => config('flystat.locales'),
            'locale_names' => config('flystat.locale_names'),
            'settings' => Setting::public(),
            'header' => $menu('in_header'),
            'footer' => $menu('in_footer'),
            'legal' => Document::where('type', 'legal')->where('is_published', true)->orderBy('sort')->get()
                ->map(fn ($d) => ['slug' => $d->slug, 'title' => $d->getTranslation('title', $l)])->values(),
        ])->header('Cache-Control', 'public, max-age=60');
    }

    public function page(Request $r, string $locale, string $slug = ''): JsonResponse
    {
        $l = $this->loc($locale);
        $slug = trim($slug, '/');
        $q = Page::query()->where('slug', $slug)->with('blocks');
        if (! $this->previewAllowed($r)) {
            $q->published();
        }
        $page = $q->first();
        if (! $page) {
            $redirect = Redirect::where('from', '/'.$slug)->first();
            if ($redirect) {
                return response()->json(['redirect' => $redirect->to, 'code' => $redirect->code], 200);
            }

            return response()->json(['message' => 'Not found'], 404);
        }
        $faqs = Faq::where('is_published', true)->orderBy('sort')->get();
        $blocks = $page->blocks->where('enabled', true)->values()->map(function ($b) use ($l, $faqs, $slug) {
            $data = Localize::resolve($b->data, $l);
            if ($b->type === 'faq') {
                $cats = $data['categories'] ?? null;
                $items = $faqs->filter(fn ($f) => (! $cats || in_array($f->category, $cats)) && (! $f->pages || in_array($slug === '' ? 'home' : $slug, $f->pages)));
                $data['items'] = $items->take($data['limit'] ?? 50)->map(fn ($f) => ['q' => $f->getTranslation('question', $l), 'a' => $f->getTranslation('answer', $l), 'category' => $f->category])->values();
            }
            if ($b->type === 'news') {
                $data['items'] = News::published()->latest('published_at')->take($data['limit'] ?? 3)->get()->map(fn ($n) => $this->newsCard($n, $l));
            }
            if ($b->type === 'map') {
                $data['countries'] = Country::orderBy('code')->get()->map(fn ($c) => ['code' => $c->code, 'name' => $c->getTranslation('name', $l), 'region' => $c->region, 'status' => $c->status]);
                $data['distributors'] = Distributor::where('is_published', true)->get()->map(fn ($d) => ['name' => $d->name, 'countries' => $d->countries, 'website' => $d->website, 'logo' => $d->logo]);
            }
            if ($b->type === 'documents') {
                $data['items'] = Document::where('is_published', true)->whereIn('type', $data['types'] ?? ['spec', 'presskit', 'presentation', 'manual'])->orderBy('sort')->get()
                    ->map(fn ($d) => ['slug' => $d->slug, 'type' => $d->type, 'title' => $d->getTranslation('title', $l), 'file' => $d->files[$l] ?? ($d->files['en'] ?? null), 'version' => $d->version]);
            }
            if ($b->type === 'manual_sections') {
                $doc = Document::where('type', 'manual')->where('is_published', true)->first();
                $data['manual'] = $doc ? ['title' => $doc->getTranslation('title', $l), 'body' => $doc->getTranslation('body', $l), 'version' => $doc->version,
                    'effective_at' => $doc->effective_at?->toDateString(), 'file' => $doc->files[$l] ?? ($doc->files['en'] ?? null), 'files' => $doc->files] : null;
            }

            return ['id' => $b->id, 'type' => $b->type, 'anchor' => $b->anchor, 'data' => $data];
        });

        return response()->json([
            'slug' => $page->slug, 'template' => $page->template, 'title' => $page->getTranslation('title', $l),
            'seo' => Localize::resolve($page->seo ?? [], $l), 'blocks' => $blocks, 'updated_at' => $page->updated_at,
        ])->header('Cache-Control', 'public, max-age=60');
    }

    protected function newsCard(News $n, string $l): array
    {
        return [
            'slug' => $n->getTranslation('slug', $l, false) ?: $n->getTranslation('slug', 'en'),
            'title' => $n->getTranslation('title', $l), 'excerpt' => $n->getTranslation('excerpt', $l),
            'cover' => $n->cover, 'category' => $n->category, 'tags' => $n->tags, 'published_at' => $n->published_at?->toDateString(),
        ];
    }

    public function news(Request $r, string $locale): JsonResponse
    {
        $l = $this->loc($locale);
        $q = News::published()->latest('published_at');
        if ($c = $r->query('category')) {
            $q->where('category', $c);
        }
        $p = $q->paginate(12);

        return response()->json(['items' => collect($p->items())->map(fn ($n) => $this->newsCard($n, $l)), 'total' => $p->total(), 'page' => $p->currentPage(), 'last_page' => $p->lastPage()]);
    }

    public function newsItem(Request $r, string $locale, string $slug): JsonResponse
    {
        $l = $this->loc($locale);
        $n = News::published()->get()->first(fn ($n) => in_array($slug, array_values($n->getTranslations('slug'))));
        if (! $n) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $neighbors = News::published()->where('id', '!=', $n->id)->latest('published_at')->take(3)->get()->map(fn ($x) => $this->newsCard($x, $l));

        return response()->json($this->newsCard($n, $l) + [
            'body' => $n->getTranslation('body', $l), 'author' => $n->author, 'seo' => Localize::resolve($n->seo ?? [], $l),
            'slugs' => $n->getTranslations('slug'), 'neighbors' => $neighbors,
        ]);
    }

    public function document(Request $r, string $locale, string $slug): JsonResponse
    {
        $l = $this->loc($locale);
        $d = Document::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return response()->json(['slug' => $d->slug, 'type' => $d->type, 'title' => $d->getTranslation('title', $l), 'body' => $d->getTranslation('body', $l),
            'version' => $d->version, 'effective_at' => $d->effective_at?->toDateString(), 'file' => $d->files[$l] ?? ($d->files['en'] ?? null)]);
    }

    public function faqs(Request $r, string $locale): JsonResponse
    {
        $l = $this->loc($locale);

        return response()->json(Faq::where('is_published', true)->orderBy('sort')->get()
            ->map(fn ($f) => ['q' => $f->getTranslation('question', $l), 'a' => $f->getTranslation('answer', $l), 'category' => $f->category]));
    }

    public function countries(Request $r, string $locale): JsonResponse
    {
        $l = $this->loc($locale);

        return response()->json(Country::orderBy('code')->get()->map(fn ($c) => ['code' => $c->code, 'name' => $c->getTranslation('name', $l), 'region' => $c->region, 'status' => $c->status]));
    }

    /** Sitemap feed for the front: every published page / news / document with all locale slugs. */
    public function sitemap(): JsonResponse
    {
        $pages = Page::published()->get()->map(fn ($p) => ['slug' => $p->slug, 'updated_at' => $p->updated_at]);
        $news = News::published()->get()->map(fn ($n) => ['slugs' => $n->getTranslations('slug'), 'updated_at' => $n->updated_at]);
        $docs = Document::where('is_published', true)->where('type', 'legal')->get()->map(fn ($d) => ['slug' => $d->slug, 'updated_at' => $d->updated_at]);

        return response()->json(compact('pages', 'news', 'docs'));
    }
}
