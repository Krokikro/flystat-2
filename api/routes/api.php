<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::get('sitemap', [ContentController::class, 'sitemap']);
    Route::post('leads', [LeadController::class, 'store'])->middleware('throttle:30,1');
    Route::post('webhooks/jsm', [WebhookController::class, 'jsm']);

    Route::prefix('{locale}')->where(['locale' => '[a-z]{2}'])->group(function () {
        Route::get('bootstrap', [ContentController::class, 'bootstrap']);
        Route::get('news', [ContentController::class, 'news']);
        Route::get('news/{slug}', [ContentController::class, 'newsItem']);
        Route::get('documents/{slug}', [ContentController::class, 'document']);
        Route::get('faqs', [ContentController::class, 'faqs']);
        Route::get('countries', [ContentController::class, 'countries']);
        Route::get('pages/{slug?}', [ContentController::class, 'page'])->where('slug', '.*');
    });
});
