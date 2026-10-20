<?php

return [
    'locales' => ['en', 'ru', 'fr', 'es', 'it', 'de', 'zh', 'vi', 'hi'],
    'default_locale' => 'en',
    'locale_names' => [
        'en' => 'English', 'ru' => 'Русский', 'fr' => 'Français', 'es' => 'Español', 'it' => 'Italiano',
        'de' => 'Deutsch', 'zh' => '简体中文', 'vi' => 'Tiếng Việt', 'hi' => 'हिन्दी',
    ],
    'cohorts' => [
        'distribution' => 'Distribution (MoU)',
        'waitlist' => 'Waitlist / pre-order',
        'support' => 'Support',
        'distributor_contact' => 'Contact a distributor',
        'cooperation' => 'Cooperation',
        'subscribe' => 'Newsletter / app launch',
    ],
    'lead_statuses' => [
        'new' => 'New', 'in_progress' => 'In progress', 'waiting' => 'Waiting for reply',
        'processed' => 'Processed', 'spam' => 'Spam', 'declined' => 'Declined',
    ],
    'block_types' => [
        'hero_dark', 'hero_light', 'stats', 'how_it_works', 'text_media', 'cards', 'steps', 'spec_table',
        'comparison', 'faq', 'form', 'news', 'map', 'cta', 'app_screens', 'segments', 'timeline',
        'manual_sections', 'account_link', 'documents', 'contacts', 'team_quote', 'video',
    ],
    'web_url' => env('WEB_URL', 'http://localhost:3000'),
    'revalidate_secret' => env('REVALIDATE_SECRET'),
    'require_2fa' => env('ADMIN_REQUIRE_2FA', true),
];
