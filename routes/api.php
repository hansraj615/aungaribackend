<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Visitor;
use App\Models\UniqueVisitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\OccasionController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TrusteeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DarshanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/visitor-count', function (Request $request) {
    $ip = $request->ip();
    $key = 'visitor_' . $ip;
    $now = Carbon::now();

    Log::info('Visitor count request', [
        'ip' => $ip,
        'user_agent' => $request->header('User-Agent'),
        'referer' => $request->header('Referer'),
        'time' => $now->toDateTimeString()
    ]);

    // Try to acquire a lock for this IP
    $lockKey = 'visitor_lock_' . $ip;
    if (!Cache::add($lockKey, true, 1)) { // Lock for 1 second
        // Get current counts from database
        $currentCounts = Visitor::first();
        return [
            'total_visits' => $currentCounts ? $currentCounts->total_visits : 0,
            'unique_visitors' => $currentCounts ? $currentCounts->unique_visitors : 0
        ];
    }

    try {
        return DB::transaction(function () use ($ip, $key, $now) {
            // Lock the visitor record for update
            $visitorCounts = Visitor::lockForUpdate()->firstOrCreate(
                ['id' => 1],
                [
                    'total_visits' => 0,
                    'unique_visitors' => 0
                ]
            );

            // Increment total visits
            $visitorCounts->increment('total_visits');
            Log::info('Incremented total visits');

            // Check if this is a unique visitor
            $uniqueVisitor = UniqueVisitor::where('ip_address', $ip)->first();

            if (!$uniqueVisitor) {
                Log::info('New unique visitor detected', ['ip' => $ip]);

                // Create new unique visitor record
                UniqueVisitor::create([
                    'ip_address' => $ip,
                    'first_visit_at' => $now,
                    'last_visit_at' => $now
                ]);

                // Increment unique visitors count
                $visitorCounts->increment('unique_visitors');
                Log::info('Incremented unique visitors');
            } else {
                // Update last visit timestamp
                $uniqueVisitor->update(['last_visit_at' => $now]);
            }

            // Get fresh counts
            $freshCounts = $visitorCounts->fresh();

            Log::info('Final counts', [
                'total_visits' => $freshCounts->total_visits,
                'unique_visitors' => $freshCounts->unique_visitors
            ]);

            return [
                'total_visits' => $freshCounts->total_visits,
                'unique_visitors' => $freshCounts->unique_visitors
            ];
        });
    } finally {
        // Always release the lock
        Cache::forget($lockKey);
    }
});

// Gallery Routes
Route::get('galleries', [GalleryController::class, 'index']);
Route::get('galleries/featured', [GalleryController::class, 'featured']);
Route::get('galleries/latest', [GalleryController::class, 'latest']);
Route::get('galleries/{id}', [GalleryController::class, 'show']);

// Occasion Routes
Route::get('occasions', [OccasionController::class, 'index']);
Route::get('occasions/active', [OccasionController::class, 'activeWithCounts']);
Route::get('occasions/{slug}', [OccasionController::class, 'show']);

// Event Routes
Route::get('events', [EventController::class, 'index']);
Route::get('events/{id}', [EventController::class, 'show']);

// Trustee Routes
Route::get('trustees', [TrusteeController::class, 'index']);

// Contact Routes
Route::post('contact', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:contact'); // Apply throttle middleware to limit requests


Route::get('/darshan-videos', [DarshanController::class, 'index']);
Route::get('/darshan-videos/{id}', [DarshanController::class, 'show']);
