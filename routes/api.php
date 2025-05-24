<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Visitor;
use App\Models\UniqueVisitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

    // Get current counts without locking first
    $currentCounts = Cache::remember('current_visitor_counts', 1, function () {
        return Visitor::first() ?? Visitor::create([
            'id' => 1,
            'total_visits' => 0,
            'unique_visitors' => 0
        ]);
    });

    // Try to acquire a lock for this IP
    $lockKey = 'visitor_lock_' . $ip;
    if (!Cache::add($lockKey, true, 1)) { // Lock for 1 second
        Log::info('Request blocked by lock', ['ip' => $ip]);
        return [
            'total_visits' => $currentCounts->total_visits,
            'unique_visitors' => $currentCounts->unique_visitors
        ];
    }

    try {
        return DB::transaction(function () use ($ip, $key, $now, $request) {
            // Lock the visitor record for update
            $visitorCounts = Visitor::lockForUpdate()->find(1);

            if (!$visitorCounts) {
                $visitorCounts = Visitor::create([
                    'id' => 1,
                    'total_visits' => 0,
                    'unique_visitors' => 0
                ]);
            }

            // Increment total visits
            $visitorCounts->increment('total_visits');
            Log::info('Incremented total visits');

            // Check if this is a unique visitor
            if (!Cache::has($key)) {
                Log::info('New unique visitor detected', ['ip' => $ip]);

                // Set cache for 6 hours
                Cache::put($key, true, now()->addHours(6));

                // Record or update unique visitor
                $uniqueVisitor = UniqueVisitor::firstOrCreate(
                    ['ip_address' => $ip],
                    ['first_visit_at' => $now]
                );

                $uniqueVisitor->update(['last_visit_at' => $now]);

                if ($uniqueVisitor->wasRecentlyCreated) {
                    $visitorCounts->increment('unique_visitors');
                    Log::info('Incremented unique visitors');
                }
            }

            // Get fresh counts
            $freshCounts = $visitorCounts->fresh();

            Log::info('Final counts', [
                'total_visits' => $freshCounts->total_visits,
                'unique_visitors' => $freshCounts->unique_visitors
            ]);

            // Update the cache
            Cache::put('current_visitor_counts', $freshCounts, 60);

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
