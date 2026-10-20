<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_page_renders(): void
    {
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $user = User::first();
        $pages = ['/admin', '/admin/leads', '/admin/pages', '/admin/pages/create', '/admin/news', '/admin/news/create', '/admin/faqs/create', '/admin/documents/create',
            '/admin/countries/create', '/admin/distributors/create', '/admin/redirects/create', '/admin/media-items/create', '/admin/users/create', '/admin/settings'];
        foreach ($pages as $p) {
            $r = $this->actingAs($user)->get($p);
            $this->assertSame(200, $r->status(), "page $p returned {$r->status()}: ".mb_strimwidth(strip_tags($r->getContent()), 0, 400));
        }
    }
}
