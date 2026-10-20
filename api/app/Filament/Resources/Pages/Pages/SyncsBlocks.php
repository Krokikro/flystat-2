<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Models\Page;
use App\Services\RevalidateService;

trait SyncsBlocks
{
    protected array $pendingBlocks = [];

    protected function extractBlocks(array $data): array
    {
        $this->pendingBlocks = $data['blocks'] ?? [];
        unset($data['blocks']);

        return $data;
    }

    protected function persistBlocks(Page $page): void
    {
        $keep = [];
        foreach ($this->pendingBlocks as $i => $b) {
            $d = $b['data'] ?? [];
            $anchor = $d['anchor'] ?? null;
            $enabled = $d['enabled'] ?? true;
            unset($d['anchor'], $d['enabled']);
            $id = $d['__id'] ?? null;
            unset($d['__id']);
            $block = $id ? $page->blocks()->find($id) : null;
            $payload = ['type' => $b['type'], 'anchor' => $anchor, 'enabled' => (bool) $enabled, 'data' => $d, 'sort' => $i];
            $block ? $block->update($payload) : $block = $page->blocks()->create($payload);
            $keep[] = $block->id;
        }
        $page->blocks()->whereNotIn('id', $keep)->delete();
        RevalidateService::paths(['/*'.($page->slug ? '/'.$page->slug : '')]);
    }

    protected function blocksToForm(Page $page): array
    {
        return $page->blocks->map(fn ($b) => ['type' => $b->type, 'data' => $b->data + ['anchor' => $b->anchor, 'enabled' => $b->enabled, '__id' => $b->id]])->values()->all();
    }
}
