<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
            $table->string('locale', 5)->default('en');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('group')->default('general');
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // same slug for all locales; '' = home
            $table->string('template', 40)->default('default');
            $table->json('title');                       // translatable
            $table->json('nav_label')->nullable();       // translatable
            $table->json('seo')->nullable();             // {title:{..}, description:{..}, og_image}
            $table->string('status', 20)->default('draft');
            $table->boolean('in_header')->default(false);
            $table->boolean('in_footer')->default(false);
            $table->string('menu_group', 20)->nullable(); // people | partners
            $table->unsignedInteger('sort')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('anchor', 60)->nullable();
            $table->json('data');                        // block payload, translatable fields inside
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->json('slug');                        // translatable
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();            // translatable, HTML
            $table->string('cover')->nullable();
            $table->string('category', 40)->default('news');
            $table->json('tags')->nullable();
            $table->string('author')->nullable();
            $table->json('seo')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('telegram_posted_at')->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40)->default('device');
            $table->json('question');
            $table->json('answer');
            $table->json('pages')->nullable();           // slugs where shown
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);                  // legal | manual | spec | presskit | presentation
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('body')->nullable();            // translatable HTML (legal / manual sections)
            $table->json('files')->nullable();           // {en: path, ru: path}
            $table->string('version', 30)->nullable();
            $table->date('effective_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->json('name');
            $table->string('region', 30);                // middle_east | europe | africa | asia | americas | cis
            $table->string('status', 20)->default('free'); // present | negotiation | free
            $table->boolean('is_priority')->default(false);
            $table->timestamps();
        });

        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('countries')->nullable();       // iso codes
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('cohort', 30);                // distribution | waitlist | support | distributor_contact | cooperation | subscribe
            $table->string('status', 30)->default('new'); // new | in_progress | waiting | processed | spam | declined
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('country', 3)->nullable();
            $table->json('fields')->nullable();          // cohort-specific fields
            $table->text('message')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('source_page')->nullable();
            $table->json('utm')->nullable();
            $table->json('meta')->nullable();            // ip, ua, screen, referrer
            $table->json('attachments')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('first_action_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->string('ticket_key', 40)->nullable();
            $table->string('ticket_url')->nullable();
            $table->string('ticket_status', 60)->nullable();
            $table->timestamp('telegram_notified_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('consent_version', 20)->nullable();
            $table->timestamps();
            $table->index(['cohort', 'status']);
            $table->index('created_at');
        });

        Schema::create('lead_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from')->unique();
            $table->string('to');
            $table->unsignedSmallInteger('code')->default(301);
            $table->timestamps();
        });

        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('disk', 20)->default('public');
            $table->string('kind', 20)->default('image'); // image | video | document | model
            $table->string('folder')->nullable();
            $table->json('alt')->nullable();             // translatable
            $table->json('tags')->nullable();
            $table->boolean('is_generated')->default(false);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['media_items', 'redirects', 'lead_comments', 'leads', 'distributors', 'countries', 'documents', 'faqs', 'news', 'blocks', 'pages', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
