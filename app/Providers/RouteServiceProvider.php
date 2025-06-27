<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
  /**
   * Define your route model bindings, pattern filters, and other route configuration.
   */
  public function boot(): void
  {
    // Configure the API rate limiter with a more lenient limit for visitor counting
    RateLimiter::for('api', function (Request $request) {
      $key = $request->ip();

      // If it's the visitor count endpoint, use a more lenient limit
      if ($request->is('api/visitor-count')) {
        return Limit::perMinute(30)->by($key);
      }

      // Default API rate limit
      return Limit::perMinute(60)->by($request->user()?->id ?: $key);
    });
    // ✅ Add this new contact-specific limiter
    RateLimiter::for('contact', function (Request $request) {
      return Limit::perMinute(5)->by($request->ip()); // Limit contact form to 5 requests per minute per IP
    });
    $this->routes(function () {
      Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api.php'));

      Route::middleware('web')
        ->group(base_path('routes/web.php'));
    });
  }
}
