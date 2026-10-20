<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Document;
use App\Models\Faq;
use App\Models\News;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Loads the authored content from database/content/*.json. Idempotent: pages are matched by slug,
 * everything else by a stable key. Existing admin edits are kept unless --force (CONTENT_FORCE=1).
 */
class ContentSeeder extends Seeder
{
    protected string $dir;

    public function run(): void
    {
        $this->dir = database_path('content');
        $force = (bool) env('CONTENT_FORCE', false);

        foreach ($this->json('settings') as $key => $row) {
            if ($force || Setting::where('key', $key)->doesntExist()) {
                Setting::set($key, $row['value'], $row['group'] ?? 'general', $row['secret'] ?? false);
            }
        }

        foreach ($this->json('pages') as $p) {
            $page = Page::firstOrNew(['slug' => $p['slug']]);
            if ($page->exists && ! $force) {
                continue;
            }
            $page->fill(collect($p)->except('blocks')->all())->save();
            $page->blocks()->delete();
            foreach ($p['blocks'] as $i => $b) {
                $page->blocks()->create(['type' => $b['type'], 'anchor' => $b['anchor'] ?? null, 'enabled' => $b['enabled'] ?? true, 'sort' => $i, 'data' => $b['data']]);
            }
        }

        foreach ($this->json('faqs') as $i => $f) {
            $existing = Faq::where('question->en', $f['question']['en'])->first();
            if ($existing && ! $force) {
                continue;
            }
            ($existing ?? new Faq)->fill($f + ['sort' => $i])->save();
        }

        foreach ($this->json('documents') as $d) {
            $doc = Document::firstOrNew(['slug' => $d['slug']]);
            if ($doc->exists && ! $force) {
                continue;
            }
            $doc->fill($d)->save();
        }

        foreach ($this->json('countries') as $c) {
            $country = Country::firstOrNew(['code' => $c['code']]);
            if ($country->exists && ! $force) {
                continue;
            }
            $country->fill($c)->save();
        }

        foreach ($this->json('news') as $n) {
            $item = News::where('slug->en', $n['slug']['en'])->first();
            if ($item && ! $force) {
                continue;
            }
            ($item ?? new News)->fill($n)->save();
        }

        foreach ($this->json('redirects') as $r) {
            Redirect::updateOrCreate(['from' => $r['from']], $r);
        }
    }

    protected function json(string $name): array
    {
        $path = "{$this->dir}/{$name}.json";

        return File::exists($path) ? json_decode(File::get($path), true) : [];
    }
}
